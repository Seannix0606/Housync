#!/bin/bash

# ESP32 RFID Bridge Starter Script for Linux/Mac
echo "ESP32 RFID Bridge - Starting..."
echo

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo "Error: PHP is not installed or not in PATH"
    echo "Please install PHP first"
    exit 1
fi

# Set default values
SERIAL_PORT=${1:-"/dev/ttyUSB0"}
LARAVEL_URL=${2:-"http://localhost:8000"}

echo "Configuration:"
echo "  Serial Port: $SERIAL_PORT"
echo "  Laravel URL: $LARAVEL_URL"
echo

# Check if serial port exists
if [ ! -e "$SERIAL_PORT" ]; then
    echo "Warning: Serial port $SERIAL_PORT does not exist"
    echo "Available ports:"
    ls /dev/ttyUSB* /dev/ttyACM* 2>/dev/null || echo "  No USB serial ports found"
    echo
fi

# Check if Laravel is running
echo "Checking Laravel application..."
if ! curl -s --max-time 3 "$LARAVEL_URL" > /dev/null 2>&1; then
    echo "Warning: Cannot connect to Laravel at $LARAVEL_URL"
    echo "Make sure Laravel development server is running:"
    echo "  php artisan serve"
    echo
    read -p "Continue anyway? (y/n): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

echo "Starting ESP32 Bridge..."
echo "Press Ctrl+C to stop the bridge"
echo

# Run the PHP bridge
php esp32_bridge.php "$SERIAL_PORT" "$LARAVEL_URL"

echo
echo "Bridge stopped."
