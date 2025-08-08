#include <SPI.h>
#include <MFRC522.h>
#include <ArduinoJson.h>

// RFID Configuration (using your existing pins)
#define SS_PIN  5
#define RST_PIN 0

// Initialize RFID reader
MFRC522 rfid(SS_PIN, RST_PIN);

// Device configuration for Laravel integration
const String DEVICE_ID = "ESP32_SCANNER_01";
const String SCANNER_LOCATION = "Main Entrance";

// Variables
String lastCardUID = "";
unsigned long lastReadTime = 0;
const unsigned long READ_DELAY = 2000; // 2 seconds between reads

void setup() {
  Serial.begin(115200);
  
  // Initialize SPI bus
  SPI.begin();
  
  // Initialize RFID reader
  rfid.PCD_Init();
  
  // Show RFID reader details
  rfid.PCD_DumpVersionToSerial();
  
  Serial.println("ESP32 RFID Scanner Ready");
  Serial.println("Device ID: " + DEVICE_ID);
  Serial.println("Location: " + SCANNER_LOCATION);
  Serial.println("Waiting for RFID cards...");
  Serial.println("Format: RFID_SCAN:{JSON}");
}

void loop() {
  // Check if new card is present
  if (!rfid.PICC_IsNewCardPresent()) {
    return;
  }
  
  // Read the card
  if (!rfid.PICC_ReadCardSerial()) {
    return;
  }
  
  // Get current time
  unsigned long currentTime = millis();
  
  // Check if enough time has passed since last read
  if (currentTime - lastReadTime < READ_DELAY) {
    return;
  }
  
  // Get card UID
  String cardUID = "";
  for (byte i = 0; i < rfid.uid.size; i++) {
    cardUID += String(rfid.uid.uidByte[i] < 0x10 ? "0" : "");
    cardUID += String(rfid.uid.uidByte[i], HEX);
  }
  cardUID.toUpperCase();
  
  // Check if it's the same card (avoid duplicate reads)
  if (cardUID == lastCardUID) {
    return;
  }
  
  Serial.println("Card detected!");
  Serial.print("UID: ");
  Serial.println(cardUID);
  
  // Send data to Laravel via PHP bridge
  sendToLaravel(cardUID);
  
  // Optional: Print detailed card info for debugging
  if (Serial.available() > 0) {
    String command = Serial.readString();
    command.trim();
    if (command == "INFO") {
      printCardInfo();
    }
  }
  
  // Update variables
  lastCardUID = cardUID;
  lastReadTime = currentTime;
  
  // Halt PICC
  rfid.PICC_HaltA();
  // Stop encryption on PCD
  rfid.PCD_StopCrypto1();
}

void sendToLaravel(String cardUID) {
  // Create JSON object for Laravel
  StaticJsonDocument<300> doc;
  doc["card_uid"] = cardUID;  // Changed from cardUID to card_uid for Laravel
  doc["device_id"] = DEVICE_ID;
  doc["scanner_location"] = SCANNER_LOCATION;
  doc["timestamp"] = millis();
  
  // Add additional sensor data
  JsonObject additional_data = doc.createNestedObject("additional_data");
  additional_data["signal_strength"] = "good";
  additional_data["scan_duration"] = "fast";
  additional_data["card_type"] = getCardType();
  
  // Send JSON to PHP bridge with proper prefix
  Serial.print("RFID_SCAN:");
  serializeJson(doc, Serial);
  Serial.println();
  
  // Visual feedback
  Serial.println("Data sent to Laravel: " + cardUID);
}

String getCardType() {
  MFRC522::PICC_Type piccType = rfid.PICC_GetType(rfid.uid.sak);
  return String(rfid.PICC_GetTypeName(piccType));
}

void printCardInfo() {
  Serial.println("=== Card Information ===");
  Serial.print("UID: ");
  for (byte i = 0; i < rfid.uid.size; i++) {
    Serial.print(rfid.uid.uidByte[i] < 0x10 ? " 0" : " ");
    Serial.print(rfid.uid.uidByte[i], HEX);
  }
  Serial.println();
  
  Serial.print("PICC type: ");
  MFRC522::PICC_Type piccType = rfid.PICC_GetType(rfid.uid.sak);
  Serial.println(rfid.PICC_GetTypeName(piccType));
  
  Serial.print("Card size: ");
  Serial.print(rfid.uid.size);
  Serial.println(" bytes");
  Serial.println("========================");
}

// Function to handle responses from Laravel (optional)
void handleLaravelResponse() {
  if (Serial.available() > 0) {
    String response = Serial.readStringUntil('\n');
    response.trim();
    
    if (response.startsWith("RESPONSE:")) {
      String jsonResponse = response.substring(9);
      
      // Parse Laravel response
      StaticJsonDocument<200> responseDoc;
      DeserializationError error = deserializeJson(responseDoc, jsonResponse);
      
      if (error) {
        Serial.println("Failed to parse Laravel response");
        return;
      }
      
      bool accessGranted = responseDoc["access_granted"];
      String message = responseDoc["message"];
      
      Serial.println("Laravel Response:");
      Serial.println("Access: " + String(accessGranted ? "GRANTED" : "DENIED"));
      Serial.println("Message: " + message);
      
      // You can add LED/buzzer control here based on access result
      if (accessGranted) {
        // Green LED on, success beep, etc.
        Serial.println("✓ ACCESS GRANTED");
      } else {
        // Red LED on, error beep, etc.
        Serial.println("✗ ACCESS DENIED");
      }
    }
  }
}
