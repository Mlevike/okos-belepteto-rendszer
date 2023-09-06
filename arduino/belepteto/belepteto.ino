

//Beimportáljuk a szükséges külső könyvtárakat
#include <ArduinoJson.h>
#include <Keypad.h>
#include "HD44780_LCD_PCF8574.h"
#include <MFRC522v2.h>
#include <MFRC522DriverSPI.h>
#include <MFRC522DriverPinSimple.h>
#include <MFRC522Debug.h>
#include <Adafruit_Fingerprint.h> //Ez majd lehet, hogy később eltávolításra kerül

//Definiáljuk a hangszóró beállításait
#define BUZZER_FREQUENCY 2400 //Megadjuk a frekvenciát Hz-ben
#define BUZZER_PIN A0 //Megadjuk a kimeneti pin-t
#define SHORT_TIME 500 //Megadjuk a rövid csippanás idejét (ms)
#define LONG_TIME 1000 //Megadjuk a hosszú csippanás idejét (ms)
#define MUTED true //Megadjuk, hogy le van-e némítva az eszköz?

//Definiáljuk a billentyűzet kiosztását
const int ROW_NUM = 4; //Sorok száma
const int COLUMN_NUM = 4; //Oszlopok száma
char keys[ROW_NUM][COLUMN_NUM] = {
  {'1','2','3', 'A'},
  {'4','5','6', 'B'},
  {'7','8','9', 'C'},
  {'*','0','#', 'D'}
};

//Definiáljuk a keypad pin kiosztását
byte pin_rows[ROW_NUM] = {9, 8, 7, 6}; //Sorok
byte pin_column[COLUMN_NUM] = {5, 4, 3, 2}; //Oszlopok

MFRC522DriverPinSimple ss_pin(10); //Definiáljuk a SPI SS lábát
MFRC522DriverSPI driver{ss_pin}; //Inicializáljuk az SPI meghajtót

SoftwareSerial mySerial(2, 3); //Inicializáljuk a szoftveres soros portot
Adafruit_Fingerprint finger = Adafruit_Fingerprint(&mySerial); //Inicializáljuk az ujjlenyomat olvasó könyvtárat
uint8_t id;
bool fingerprintOK = false; //Létrehozunk egy változót az ujjlenyomat olvasó állaőptának reprezentálásához

Keypad keypad = Keypad( makeKeymap(keys), pin_rows, pin_column, ROW_NUM, COLUMN_NUM ); //Inicializáljuk a Keypad-et
HD44780LCD myLCD(2, 16, 0x27, &Wire); //Iniciálizáljuk az LCD kijelzőt
MFRC522 mfrc522{driver};  //Inicializáljuk az RFID olvasót
StaticJsonDocument<200> rx; //Definiáljuk a bemenő JSON adatszerkezetet
StaticJsonDocument<200> tx; //Definiáljuk a kimenő JSON adatszerkezetet

String GetCode(char mask){ //Kód kérő függvény
  String code = ""; //Létrehozunk egy sztringet a kód tárolására
  for(int i = 0; i < 4; i++){ //Azért hívjuk meg négyszer a karakterbekérő függvényt, mert négy számjegyből áll a kód
    char key = ' '; //Létrehozunk egy char típusú változót a bevitt karakterek ideiglenes tárolására
    while(!isDigit(key)){
      key = keypad.getKey(); //Lekérünk egy billentyűt a keypad-ről
    }
    code = code + key; //A key változó tartalmát hozzáfűzzük a code változóhoz
    myLCD.PCF8574_LCDSendChar(mask); //Kiíratjuk a *-ot az LCD kijelzőre
  }
  return(code); //Visszaadjuk a kódot sztringként
}


void LcdClearScreen(){ //Az LCD tartaémának törléséért felelős metódus
  myLCD.PCF8574_LCDClearScreen(); //LCD letörlése
}

void ShortBeep(){
  if(!MUTED){
  pinMode(BUZZER_PIN, OUTPUT);
  tone(BUZZER_PIN, BUZZER_FREQUENCY);
  delay(SHORT_TIME);
  noTone(BUZZER_PIN);
  }
}

void LongBeep(){
  if(!MUTED){
  pinMode(BUZZER_PIN, OUTPUT);
  tone(BUZZER_PIN, BUZZER_FREQUENCY);
  delay(LONG_TIME);
  noTone(BUZZER_PIN);
  }
}

void CustomBeep(int freq, int delayedTime){
  if(!MUTED){
  pinMode(BUZZER_PIN, OUTPUT);
  tone(BUZZER_PIN, freq);
  delay(delayedTime);
  noTone(BUZZER_PIN);
  }
}

void LcdGoto(int row, int column){ //Az LCD-n adott pozicióra ugrásért felelő metódus
  if(row == 0){
    myLCD.PCF8574_LCDGOTO(myLCD.LCDLineNumberOne, column); //Pozicióra ugrás, ha row == 0
  }
  if(row == 1){
    myLCD.PCF8574_LCDGOTO(myLCD.LCDLineNumberTwo, column); //Pozicióra ugrás, ha row == 1
  }
           
}

void LcdSendString(String text){ //Az string LCD-re történő kiíratásáért felelő metódus
  char charBuf[text.length() + 1]; //Buffer létrehozása a String --> char[] konvertáláshoz
  text.toCharArray(charBuf, text.length() + 1); //String átkonvertálása char[]-é
  myLCD.PCF8574_LCDSendString(charBuf); //Kiirjuk az üzenetet az LCD-re
}

void SoftwareReset(){ //Az eszköz szoftverből való újraindítására szolgáló metódus
  asm volatile ("jmp 0");
}

int EnrollFingerprint(){
   int p = -1;
  Serial.print("Waiting for valid finger to enroll as #"); Serial.println(id);
  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
    switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image taken");
      break;
    case FINGERPRINT_NOFINGER:
      Serial.println(".");
      break;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      break;
    case FINGERPRINT_IMAGEFAIL:
      Serial.println("Imaging error");
      break;
    default:
      Serial.println("Unknown error");
      break;
    }
  }

  // OK success!

  p = finger.image2Tz(1);
  switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image converted");
      break;
    case FINGERPRINT_IMAGEMESS:
      Serial.println("Image too messy");
      return p;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      return p;
    case FINGERPRINT_FEATUREFAIL:
      Serial.println("Could not find fingerprint features");
      return p;
    case FINGERPRINT_INVALIDIMAGE:
      Serial.println("Could not find fingerprint features");
      return p;
    default:
      Serial.println("Unknown error");
      return p;
  }

  Serial.println("Remove finger");
  delay(2000);
  p = 0;
  while (p != FINGERPRINT_NOFINGER) {
    p = finger.getImage();
  }
  Serial.print("ID "); Serial.println(id);
  p = -1;
  Serial.println("Place same finger again");
  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
    switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image taken");
      break;
    case FINGERPRINT_NOFINGER:
      Serial.print(".");
      break;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      break;
    case FINGERPRINT_IMAGEFAIL:
      Serial.println("Imaging error");
      break;
    default:
      Serial.println("Unknown error");
      break;
    }
  }

  // OK success!

  p = finger.image2Tz(2);
  switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image converted");
      break;
    case FINGERPRINT_IMAGEMESS:
      Serial.println("Image too messy");
      return p;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      return p;
    case FINGERPRINT_FEATUREFAIL:
      Serial.println("Could not find fingerprint features");
      return p;
    case FINGERPRINT_INVALIDIMAGE:
      Serial.println("Could not find fingerprint features");
      return p;
    default:
      Serial.println("Unknown error");
      return p;
  }

  // OK converted!
  Serial.print("Creating model for #");  Serial.println(id);

  p = finger.createModel();
  if (p == FINGERPRINT_OK) {
    Serial.println("Prints matched!");
  } else if (p == FINGERPRINT_PACKETRECIEVEERR) {
    Serial.println("Communication error");
    return p;
  } else if (p == FINGERPRINT_ENROLLMISMATCH) {
    Serial.println("Fingerprints did not match");
    return p;
  } else {
    Serial.println("Unknown error");
    return p;
  }

  Serial.print("ID "); Serial.println(id);
  p = finger.storeModel(id);
  if (p == FINGERPRINT_OK) {
    Serial.println("Stored!");
  } else if (p == FINGERPRINT_PACKETRECIEVEERR) {
    Serial.println("Communication error");
    return p;
  } else if (p == FINGERPRINT_BADLOCATION) {
    Serial.println("Could not store in that location");
    return p;
  } else if (p == FINGERPRINT_FLASHERR) {
    Serial.println("Error writing to flash");
    return p;
  } else {
    Serial.println("Unknown error");
    return p;
  }

  return true;
}

void setup(){
  ShortBeep(); //Csak teszteléshez
  delay(50);
  //Beállítjuk az LCD kijelzőt
  myLCD.PCF8574_LCDBackLightSet(true); //Engedélyezzük az LCD háttérvilágítását
  myLCD.PCF8574_LCDInit(myLCD.LCDCursorTypeOff); //Ne jelenítsük meg a kurzort az LCD kijelzőn
  Serial.begin(9600); //Elindítjuk a soros kommunikációt
  mfrc522.PCD_Init();  //Inicializáljuk az RFID olvasót
  finger.begin(115200); //Beállítjuk az ujjlenyomat olvasó kapcsolatának sebességét
  if (finger.verifyPassword()) {
    fingerprintOK = true;
  }
}

void loop(){
  //Figyeljük a bejövő soros adatforgalmat
  if (Serial.available()){
    deserializeJson(rx, Serial);
    if(rx["type"] == "action"){
      if(rx["action"] == "get_code"){
        //Elküldjük válaszként a kódot!
        tx["type"] = "event";
        tx["event"] = "code_given";
        tx["code"] = GetCode('*'); 
        serializeJson(tx, Serial); //Szerializáljuk és továbbítjuk soros kommunikáción keresztül a JSON adatszerkezetünket
        Serial.println(); //Küldjünk egy sor végét is a soros kommunikáción keresztül
        tx.clear(); //Töröljük a json adatszerkezet tartalmát, mert már nincs szükségünk rá!
      }
      if(rx["action"] == "custom_beep"){
        CustomBeep(rx["frequency"], rx["delay"]);
      }
      if(rx["action"] == "lcd_goto"){
        LcdGoto(rx["row"], rx["column"]);
      }

      if(rx["action"] == "lcd_clear_screen"){
        LcdClearScreen();
      }
      
      if(rx["action"] == "lcd_send_string"){
        LcdSendString(rx["string"]);
      }
      if(rx["action"] == "soft_reset"){
        SoftwareReset();
      }
    }
    rx.clear(); //Töröljük a json adatszerkezet tartalmát, mert már nincs szükségünk rá!  
  }
  //Figyeljük, hogy érintenek-e az olvasóhoz új kártyát
  if (!mfrc522.PICC_IsNewCardPresent()) {
    return;
  }
  if (!mfrc522.PICC_ReadCardSerial()) {
    return;
  }
  //Beolvassuk a kártya egyedi gyártó által beleégetett azonosítóját (UID)
  ShortBeep(); //Csak teszteléshez
  String cardUID = "";
  for (int i = 0; i < mfrc522.uid.size; i++) {
    cardUID += String(mfrc522.uid.uidByte[i], HEX);
  }
  mfrc522.PICC_HaltA(); //Befelyzzük a kártya olvasását
  tx["type"] = "event"; //Megadjuk az adat típusát (event)
  tx["event"] = "card_detected"; //Megadjuk az eseményt (card_detected)
  tx["uid"] = cardUID; //Hozzáadjuk az adatszerkezethez az uid-t
  serializeJson(tx, Serial); //Szerializáljuk és továbbítjuk soros kommunikáción keresztül a JSON adatszerkezetünket
  Serial.println(); //Küldjünk egy sor végét is a soros kommunikáción keresztül
  tx.clear(); //Töröljük a json adatszerkezet tartalmát, mert már nincs szükségünk rá!
}
