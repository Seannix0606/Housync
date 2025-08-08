# ESP32 RFID Integration with Laravel

This document provides instructions for integrating an ESP32 with RFID RC522 scanner with your Laravel application using USB serial communication instead of WiFi.

## Hardware Requirements

- ESP32 Development Board
- RFID RC522 Module  
- RFID Cards/Tags
- USB Cable (for data transfer to computer)
- Jumper wires for connections

## Hardware Connections

Connect the RFID RC522 module to your ESP32 as follows:

```
RC522 Module    ESP32 Pin
VCC       ->    3.3V
GND       ->    GND
RST       ->    GPIO 0
SDA(SS)   ->    GPIO 5
MOSI      ->    GPIO 23
MISO      ->    GPIO 19
SCK       ->    GPIO 18
```

**Note**: The pin configuration above matches your existing setup. If you're using different pins, update the `SS_PIN` and `RST_PIN` definitions in the Arduino code accordingly.

## ESP32 Arduino Code

Create a new Arduino sketch with the following code:

```cpp
#include <SPI.h>
#include <MFRC522.h>
#include <ArduinoJson.h>

// RFID pins
#define SS_PIN 5
#define RST_PIN 0

// Create MFRC522 instance
MFRC522 mfrc522(SS_PIN, RST_PIN);

// Device configuration
const String DEVICE_ID = "ESP32_SCANNER_01";
const String SCANNER_LOCATION = "Main Entrance";

void setup() {
  Serial.begin(115200);
  SPI.begin();
  mfrc522.PCD_Init();
  
  Serial.println("ESP32 RFID Scanner Ready");
  Serial.println("Device ID: " + DEVICE_ID);
  Serial.println("Location: " + SCANNER_LOCATION);
  Serial.println("Waiting for RFID cards...");
}

void loop() {
  // Look for new cards
  if (!mfrc522.PICC_IsNewCardPresent()) {
    return;
  }
  
  // Select one of the cards
  if (!mfrc522.PICC_ReadCardSerial()) {
    return;
  }
  
  // Get card UID
  String cardUID = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    cardUID += String(mfrc522.uid.uidByte[i] < 0x10 ? "0" : "");
    cardUID += String(mfrc522.uid.uidByte[i], HEX);
  }
  cardUID.toUpperCase();
  
  // Create JSON data to send to Laravel
  StaticJsonDocument<300> doc;
  doc["card_uid"] = cardUID;
  doc["device_id"] = DEVICE_ID;
  doc["scanner_location"] = SCANNER_LOCATION;
  doc["timestamp"] = millis();
  
  // Add additional sensor data if available
  JsonObject additional_data = doc.createNestedObject("additional_data");
  additional_data["signal_strength"] = "good";
  additional_data["scan_duration"] = "fast";
  
  // Send JSON to serial port
  Serial.print("RFID_SCAN:");
  serializeJson(doc, Serial);
  Serial.println();
  
  // Visual feedback
  Serial.println("Card detected: " + cardUID);
  
  // Halt PICC
  mfrc522.PICC_HaltA();
  
  // Stop encryption on PCD
  mfrc522.PCD_StopCrypto1();
  
  delay(1000); // Prevent multiple reads
}
```

## PHP Bridge Script (Recommended)

Since you're already using PHP for Laravel, you can use PHP for the ESP32 bridge as well. This eliminates the need for Python dependencies and integrates better with your existing environment.

The PHP bridge script (`esp32_bridge.php`) is already included in your project and provides:

- Direct serial communication with ESP32
- JSON processing and Laravel API integration  
- Cross-platform support (Windows/Linux/Mac)
- Built-in connection testing and error handling
- Command line interface with helpful options

### Key features of the PHP Bridge:
- Uses native PHP file operations for serial communication
- No external dependencies beyond PHP
- Graceful error handling and logging
- Real-time RFID scan processing
- Two-way communication with ESP32

## Alternative: Python Bridge Script

If you prefer Python, you can also use this Python script:

```python
# esp32_bridge.py (alternative)
import serial
import requests
import json
import time
import sys
from datetime import datetime

class ESP32Bridge:
    def __init__(self, serial_port='COM7', baud_rate=115200, laravel_url='http://localhost:8000'):
        self.serial_port = serial_port
        self.baud_rate = baud_rate
        self.laravel_url = laravel_url
        self.ser = None
        
    def connect_serial(self):
        try:
            self.ser = serial.Serial(self.serial_port, self.baud_rate, timeout=1)
            print(f"Connected to ESP32 on {self.serial_port}")
            return True
        except Exception as e:
            print(f"Failed to connect to ESP32: {e}")
            return False
    
    def send_to_laravel(self, rfid_data):
        try:
            # Add timestamp
            rfid_data['timestamp'] = datetime.now().isoformat()
            
            # Send to Laravel endpoint
            response = requests.post(
                f"{self.laravel_url}/api/esp32/rfid-scan",
                json=rfid_data,
                headers={'Content-Type': 'application/json'},
                timeout=10
            )
            
            if response.status_code == 200:
                result = response.json()
                print(f"✓ Access {result.get('access_result', 'processed')}: {result.get('message', 'OK')}")
                if 'tenant_name' in result:
                    print(f"  Tenant: {result['tenant_name']}")
                return result
            else:
                print(f"✗ Laravel error: {response.status_code}")
                return None
                
        except Exception as e:
            print(f"✗ Communication error: {e}")
            return None
    
    def run(self):
        if not self.connect_serial():
            return
            
        print("ESP32 Bridge running... Press Ctrl+C to stop")
        
        try:
            while True:
                if self.ser and self.ser.in_waiting > 0:
                    line = self.ser.readline().decode('utf-8').strip()
                    
                    if line.startswith('RFID_SCAN:'):
                        # Extract JSON data
                        json_data = line[10:]  # Remove 'RFID_SCAN:' prefix
                        
                        try:
                            rfid_data = json.loads(json_data)
                            print(f"\n📡 RFID Scan: {rfid_data.get('card_uid', 'Unknown')}")
                            
                            # Send to Laravel
                            result = self.send_to_laravel(rfid_data)
                            
                        except json.JSONDecodeError as e:
                            print(f"Invalid JSON from ESP32: {e}")
                    
                    elif line:
                        # Print other ESP32 messages
                        print(f"ESP32: {line}")
                
                time.sleep(0.1)
                
        except KeyboardInterrupt:
            print("\nShutting down ESP32 Bridge...")
        finally:
            if self.ser:
                self.ser.close()

if __name__ == "__main__":
    # Configuration
    SERIAL_PORT = 'COM3'  # Change this to your ESP32 port
    LARAVEL_URL = 'http://localhost:8000'  # Change to your Laravel URL
    
    if len(sys.argv) > 1:
        SERIAL_PORT = sys.argv[1]
    
    bridge = ESP32Bridge(SERIAL_PORT, 115200, LARAVEL_URL)
    bridge.run()
```

## Setup Instructions

### 1. Install Required Libraries

For Arduino IDE:
```
- Install MFRC522 library by GithubCommunity
- Install ArduinoJson library by Benoit Blanchon
```

For PHP Bridge (Recommended):
```
- PHP 7.4+ (already required for Laravel)
- No additional dependencies needed!
```

For Python bridge (Alternative):
```bash
pip install pyserial requests
```

### 2. Upload ESP32 Code

1. Open Arduino IDE
2. Install ESP32 board support if not already installed
3. Select your ESP32 board
4. Select the correct serial port
5. Upload the code to your ESP32

### 3. Configure Laravel

1. Set up your MySQL database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

2. Run the migrations:
```bash
php artisan migrate
```

### 4. Run the Bridge

#### Option A: PHP Bridge (Recommended)

1. Find your ESP32 serial port:
   - Windows: Check Device Manager (usually COM3, COM4, etc.)
   - Linux/Mac: Check `/dev/ttyUSB*` or `/dev/ttyACM*`

2. **Easy Start (Windows)**: Double-click `start_bridge.bat`
   
3. **Manual Start**: 
```bash
# Windows
php esp32_bridge.php COM7

# Linux/Mac  
php esp32_bridge.php /dev/ttyUSB0

# With custom Laravel URL
php esp32_bridge.php COM7 http://localhost:8000
```

4. **Available Commands**:
```bash
# Show help
php esp32_bridge.php --help

# List available serial ports
php esp32_bridge.php --list

# Test Laravel connection only
php esp32_bridge.php --test
```

#### Option B: Python Bridge (Alternative)

1. Install Python dependencies:
```bash
pip install pyserial requests
```

2. Run the bridge script:
```bash
python esp32_bridge.py COM3  # Replace COM3 with your port
```

### 5. Test the System

1. Make sure your Laravel application is running (`php artisan serve`)
2. Run the PHP bridge script (or use `start_bridge.bat` on Windows)
3. Place an RFID card near the scanner
4. Check the console output and Laravel security logs

**Expected Output:**
```
📡 RFID Scan: A1B2C3D4
   Device: ESP32_SCANNER_01
   Location: Main Entrance
✓ Access granted: Access Granted
  Tenant: John Doe
  Unit: 101
```

## Usage Flow

1. **Card Registration**: Landlords create RFID cards in the Laravel admin panel
2. **Card Assignment**: Cards are assigned to tenants with specific access permissions
3. **Real-time Scanning**: ESP32 scans cards and sends data via USB serial
4. **Bridge Processing**: Python script receives serial data and sends HTTP requests
5. **Access Control**: Laravel processes the request and returns access decision
6. **Logging**: All access attempts are logged in the security_logs table

## Troubleshooting

### ESP32 Issues
- Check wiring connections
- Verify correct pin assignments
- Ensure 3.3V power supply to RFID module
- Check serial monitor for error messages

### Serial Communication Issues
- Verify correct serial port in Python script
- Check baud rate matches (115200)
- Ensure ESP32 drivers are installed
- Try different USB cables/ports

### Laravel Issues
- Check database connection
- Verify routes are registered
- Check Laravel logs for errors
- Ensure CSRF protection is disabled for API routes

## Security Considerations

1. **Local Network**: This setup uses USB serial communication, keeping data local
2. **Access Control**: Only authorized cards grant access
3. **Logging**: All access attempts are logged with timestamps
4. **Card Management**: Cards can be activated/deactivated remotely
5. **Time Restrictions**: Optional time-based access control

## Extension Ideas

1. **Multiple Scanners**: Connect multiple ESP32 devices with different device IDs
2. **Door Control**: Add relay control for automatic door locks
3. **Visual Feedback**: Add LEDs or buzzers for access status
4. **Card Writing**: Use ESP32 to write data to new RFID cards
5. **Offline Mode**: Store access decisions locally when network is unavailable

This setup provides a secure, reliable RFID access control system using ESP32 hardware and Laravel backend without requiring WiFi connectivity.
