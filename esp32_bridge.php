<?php
/**
 * ESP32 RFID Bridge - PHP Version
 * 
 * This script bridges the ESP32 serial communication with your Laravel application
 * using PHP instead of Python for better integration with your Laravel environment.
 */

require_once 'vendor/autoload.php';

class ESP32Bridge
{
    private $serialPort;
    private $baudRate;
    private $laravelUrl;
    private $serialHandle;
    private $isRunning = false;

    public function __construct($serialPort = 'COM7', $baudRate = 115200, $laravelUrl = 'http://localhost:8000')
    {
        $this->serialPort = $serialPort;
        $this->baudRate = $baudRate;
        $this->laravelUrl = $laravelUrl;
    }

    /**
     * Initialize serial connection to ESP32
     */
    public function connectSerial()
    {
        try {
            // Windows specific serial port opening
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $this->serialHandle = fopen($this->serialPort . ':', 'r+b');
                if (!$this->serialHandle) {
                    throw new Exception("Failed to open serial port {$this->serialPort}");
                }
                
                // Configure serial port settings for Windows
                exec("mode {$this->serialPort}: BAUD={$this->baudRate} PARITY=N DATA=8 STOP=1");
                
            } else {
                // Linux/Mac serial port opening
                $device = str_replace('COM', '/dev/ttyUSB', $this->serialPort);
                if (strpos($this->serialPort, 'COM') === false) {
                    $device = $this->serialPort; // Already in Unix format
                }
                
                // Configure serial port
                exec("stty -F {$device} {$this->baudRate} cs8 -cstopb -parity raw");
                
                $this->serialHandle = fopen($device, 'r+b');
                if (!$this->serialHandle) {
                    throw new Exception("Failed to open serial port {$device}");
                }
            }

            // Set non-blocking mode
            stream_set_blocking($this->serialHandle, false);
            
            echo "✓ Connected to ESP32 on {$this->serialPort}\n";
            return true;
            
        } catch (Exception $e) {
            echo "✗ Failed to connect to ESP32: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Send RFID data to Laravel endpoint
     */
    public function sendToLaravel($rfidData)
    {
        try {
            // Add timestamp
            $rfidData['timestamp'] = date('c'); // ISO 8601 format
            
            // Prepare HTTP context
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => [
                        'Content-Type: application/json',
                        'Accept: application/json',
                        'X-Requested-With: XMLHttpRequest'
                    ],
                    'content' => json_encode($rfidData),
                    'timeout' => 10
                ]
            ]);

            // Send to Laravel endpoint
            $response = file_get_contents(
                $this->laravelUrl . '/api/esp32/rfid-scan',
                false,
                $context
            );

            if ($response !== false) {
                $result = json_decode($response, true);
                
                echo "✓ Access {$result['access_result']}: {$result['message']}\n";
                
                if (isset($result['tenant_name'])) {
                    echo "  Tenant: {$result['tenant_name']}\n";
                }
                
                if (isset($result['unit_number'])) {
                    echo "  Unit: {$result['unit_number']}\n";
                }
                
                return $result;
            } else {
                echo "✗ Laravel communication failed\n";
                return null;
            }
            
        } catch (Exception $e) {
            echo "✗ Communication error: " . $e->getMessage() . "\n";
            return null;
        }
    }

    /**
     * Process incoming serial data from ESP32
     */
    public function processSerialData($line)
    {
        $line = trim($line);
        
        if (strpos($line, 'RFID_SCAN:') === 0) {
            // Extract JSON data (new format)
            $jsonData = substr($line, 10); // Remove 'RFID_SCAN:' prefix
            
            try {
                $rfidData = json_decode($jsonData, true);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    echo "\n📡 RFID Scan: " . ($rfidData['card_uid'] ?? 'Unknown') . "\n";
                    echo "   Device: " . ($rfidData['device_id'] ?? 'Unknown') . "\n";
                    echo "   Location: " . ($rfidData['scanner_location'] ?? 'Unknown') . "\n";
                    
                    // Send to Laravel
                    $result = $this->sendToLaravel($rfidData);
                    
                    // Optional: Send response back to ESP32
                    if ($result && $this->serialHandle) {
                        $response = json_encode([
                            'access_granted' => $result['access_granted'] ?? false,
                            'message' => $result['message'] ?? 'Processed',
                            'timestamp' => date('c')
                        ]);
                        fwrite($this->serialHandle, "RESPONSE:" . $response . "\n");
                    }
                    
                } else {
                    echo "✗ Invalid JSON from ESP32: " . json_last_error_msg() . "\n";
                    echo "   Raw data: {$jsonData}\n";
                }
                
            } catch (Exception $e) {
                echo "✗ Error processing RFID data: " . $e->getMessage() . "\n";
            }
            
        } elseif ($this->isJsonLine($line)) {
            // Handle old format: direct JSON (backward compatibility)
            try {
                $rfidData = json_decode($line, true);
                
                if (json_last_error() === JSON_ERROR_NONE && isset($rfidData['cardUID'])) {
                    echo "\n📡 RFID Scan (Legacy): " . $rfidData['cardUID'] . "\n";
                    
                    // Convert old format to new format
                    $convertedData = [
                        'card_uid' => $rfidData['cardUID'],
                        'device_id' => 'ESP32_LEGACY',
                        'scanner_location' => 'Legacy Scanner',
                        'timestamp' => $rfidData['timestamp'] ?? date('c'),
                        'additional_data' => [
                            'legacy_format' => true
                        ]
                    ];
                    
                    // Send to Laravel
                    $result = $this->sendToLaravel($convertedData);
                    
                } else {
                    echo "✗ Invalid legacy JSON from ESP32\n";
                }
                
            } catch (Exception $e) {
                echo "✗ Error processing legacy RFID data: " . $e->getMessage() . "\n";
            }
            
        } elseif (!empty($line)) {
            // Print other ESP32 messages
            echo "[ESP32] {$line}\n";
        }
    }

    /**
     * Check if a line contains JSON data (for backward compatibility)
     */
    private function isJsonLine($line)
    {
        return (strpos($line, '{') === 0 && strpos($line, '}') !== false);
    }

    /**
     * Main bridge loop
     */
    public function run()
    {
        if (!$this->connectSerial()) {
            return;
        }

        $this->isRunning = true;
        echo "ESP32 Bridge running... Press Ctrl+C to stop\n";
        echo "Listening for RFID scans...\n\n";

        // Set up signal handlers for graceful shutdown
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGINT, [$this, 'shutdown']);
            pcntl_signal(SIGTERM, [$this, 'shutdown']);
        }

        $buffer = '';
        
        while ($this->isRunning) {
            if ($this->serialHandle) {
                // Read data from serial port
                $data = fread($this->serialHandle, 1024);
                
                if ($data !== false && strlen($data) > 0) {
                    $buffer .= $data;
                    
                    // Process complete lines
                    while (($pos = strpos($buffer, "\n")) !== false) {
                        $line = substr($buffer, 0, $pos);
                        $buffer = substr($buffer, $pos + 1);
                        
                        $this->processSerialData($line);
                    }
                }
            }
            
            // Process signals if available
            if (function_exists('pcntl_signal_dispatch')) {
                pcntl_signal_dispatch();
            }
            
            // Small delay to prevent CPU spinning
            usleep(100000); // 0.1 second
        }

        $this->cleanup();
    }

    /**
     * Graceful shutdown handler
     */
    public function shutdown($signal = null)
    {
        echo "\nShutting down ESP32 Bridge...\n";
        $this->isRunning = false;
    }

    /**
     * Cleanup resources
     */
    private function cleanup()
    {
        if ($this->serialHandle) {
            fclose($this->serialHandle);
            echo "Serial connection closed.\n";
        }
    }

    /**
     * Test Laravel connection
     */
    public function testLaravelConnection()
    {
        echo "Testing Laravel connection...\n";
        
        try {
            $testData = [
                'card_uid' => 'TEST123456',
                'device_id' => 'BRIDGE_TEST',
                'scanner_location' => 'Test Location',
                'timestamp' => date('c')
            ];
            
            $result = $this->sendToLaravel($testData);
            
            if ($result) {
                echo "✓ Laravel connection test successful\n";
                return true;
            } else {
                echo "✗ Laravel connection test failed\n";
                return false;
            }
            
        } catch (Exception $e) {
            echo "✗ Laravel connection error: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * List available serial ports (Windows only)
     */
    public static function listSerialPorts()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            echo "Available COM ports:\n";
            exec('wmic path Win32_SerialPort get DeviceID,Description', $output);
            foreach ($output as $line) {
                if (trim($line) && strpos($line, 'COM') !== false) {
                    echo "  {$line}\n";
                }
            }
        } else {
            echo "Available serial devices:\n";
            $devices = glob('/dev/ttyUSB* /dev/ttyACM* /dev/ttyS*');
            foreach ($devices as $device) {
                if (is_readable($device)) {
                    echo "  {$device}\n";
                }
            }
        }
    }
}

// Command line interface
if (php_sapi_name() === 'cli') {
    echo "ESP32 RFID Bridge - PHP Version\n";
    echo "================================\n\n";

    // Configuration
    $serialPort = $argv[1] ?? 'COM7';
    $laravelUrl = $argv[2] ?? 'http://localhost:8000';

    // Handle command line options
    if (isset($argv[1])) {
        switch ($argv[1]) {
            case '--help':
            case '-h':
                echo "Usage: php esp32_bridge.php [SERIAL_PORT] [LARAVEL_URL]\n\n";
                echo "Options:\n";
                echo "  --help, -h     Show this help message\n";
                echo "  --list, -l     List available serial ports\n";
                echo "  --test, -t     Test Laravel connection only\n\n";
                echo "Examples:\n";
                echo "  php esp32_bridge.php COM7\n";
                echo "  php esp32_bridge.php COM7 http://localhost:8000\n";
                echo "  php esp32_bridge.php /dev/ttyUSB0 http://localhost:8000\n";
                exit(0);

            case '--list':
            case '-l':
                ESP32Bridge::listSerialPorts();
                exit(0);

            case '--test':
            case '-t':
                $bridge = new ESP32Bridge($serialPort, 115200, $laravelUrl);
                $bridge->testLaravelConnection();
                exit(0);
        }
    }

    echo "Configuration:\n";
    echo "  Serial Port: {$serialPort}\n";
    echo "  Laravel URL: {$laravelUrl}\n";
    echo "  Baud Rate: 115200\n\n";

    // Create and run bridge
    $bridge = new ESP32Bridge($serialPort, 115200, $laravelUrl);
    
    // Test connection first
    if ($bridge->testLaravelConnection()) {
        echo "\nStarting bridge...\n";
        $bridge->run();
    } else {
        echo "\nPlease check your Laravel application is running and accessible.\n";
        exit(1);
    }
}
?>
