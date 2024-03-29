
//Beimportáljuk a szükséges külső könyvtárakat
#include <ArduinoJson.h>
#include "HD44780_LCD_PCF8574.h"
#include <MFRC522v2.h>
#include <MFRC522DriverSPI.h>
#include <MFRC522DriverPinSimple.h>
#include <MFRC522Debug.h>
#include <Adafruit_Fingerprint.h> 
#include <TM1650.h>

//Definiáljuk a beállításokat

#define BUZZER_FREQUENCY 2400 //Megadjuk a frekvenciát Hz-ben
#define BUZZER_PIN A0 //Megadjuk a kimeneti pin-t
#define SHORT_TIME 500 //Megadjuk a rövid csippanás idejét (ms)
#define LONG_TIME 1000 //Megadjuk a hosszú csippanás idejét (ms)
#define MUTED true //Megadjuk, hogy le van-e némítva az eszköz?

#define MFRC522_SS_PIN 10 //Megadjuk a SPI SS lábának számát

#define FPR_SOFT_SERIAL_RX_PIN 2 //Megadjuk a softwareSerial RX lábának számát
#define FPR_SOFT_SERIAL_TX_PIN 3 //Megadjuk a softwareSerial TX lábának számát
#define FPR_SERIAL_BAUDRATE 115200 //Megadjuk a softwareSerial sebességét

#define TM1650_SDA_PIN 8 //Megadjuk a TM1650-es billenytű vezérlő IC felé vezető SDA lábnak számát
#define TM1650_SCL_PIN 9 //Megadjuk a TM1650-es billenytű vezérlő IC felé vezető SCL lábnak számát

#define LCD_ROW_NUMBER 2 //Megadjuk, hogy hány sor magas az LCD kijelzőnk
#define LCD_COLUMN_NUMBER 16 //Megadjuk, hogy hány oszlop széles az LCD kijelzőnk
#define LCD_ADDRESS 0x27 //Megadjuk az LCD kijelző címét
#define LCD_BACKLIGHT true //Megadjuk, hogy az inicializációkor bekapcsoljuk-e az LCD kijelző háttérvilágítását

#define STATIC_JSON_DOCUMENT_SIZE 200 //Megadjuk, hogy a JSON feldolgozó mekkora méretű területet foglalahat a memóriában (byte)
#define SERIAL_BAUDRATE 9600 //Megadjuk a soros kommunikáció sebességét

MFRC522DriverPinSimple ss_pin(MFRC522_SS_PIN); //Definiáljuk a SPI SS lábát
MFRC522DriverSPI driver{ss_pin}; //Inicializáljuk az SPI meghajtót

SoftwareSerial mySerial(FPR_SOFT_SERIAL_RX_PIN, FPR_SOFT_SERIAL_TX_PIN); //Inicializáljuk a szoftveres soros portot
Adafruit_Fingerprint finger = Adafruit_Fingerprint(&mySerial); //Inicializáljuk az ujjlenyomat olvasó könyvtárat

TM1650 module(TM1650_SDA_PIN, TM1650_SCL_PIN); //Inicializáljuk a keypad vezérlő modult, SDA=8; SCL=9

HD44780LCD myLCD(LCD_ROW_NUMBER, LCD_COLUMN_NUMBER, LCD_ADDRESS, &Wire); //Iniciálizáljuk az LCD kijelzőt
MFRC522 mfrc522{driver};  //Inicializáljuk az RFID olvasót
StaticJsonDocument<STATIC_JSON_DOCUMENT_SIZE> rx; //Definiáljuk a bemenő JSON adatszerkezetet
StaticJsonDocument<STATIC_JSON_DOCUMENT_SIZE> tx; //Definiáljuk a kimenő JSON adatszerkezetet

char TranslateKey(uint32_t keyValue){ //Billentyűkódból karakterre fordító metódus
  //Definiáljuk a billentyűzet kiosztását
  typedef struct key{
  uint32_t keyCode;
  char key;
  };

  /*key keys[1] = { //A billentyűk elrendezése
  {2, '1'}};*/
  
  key keys[10] = { //A billentyűk elrendezése
  {2, '1'}, {32, '2'}, {512, '3'}, 
  {4, '4'}, {64, '5'}, {1024, '6'}, 
  {8, '7'}, {128, '8'}, {2048, '9'}, 
  {16, '0'},
  };


  for(int i = 0; i < 10; i++){ //Társítsuk a kapott kódot a n
    if(keys[i].keyCode == keyValue){
      return keys[i].key;
    }
  }
  return ' '; //Amennyiben nem valós billenytűre hivatkoztunk, akkor addjunk ' ' karaktert
}

String GetCode(char mask){  //Kód kérő függvény
  String code = ""; //Létrehozunk egy sztringet a kód tárolására
  for(int i = 0; i < 4; i++){ //Azért hívjuk meg négyszer a karakterbekérő függvényt, mert négy számjegyből áll a kód
    char sample1 = ' '; //Létrehozunk egy változót az első mintavételnek
    char sample2 = ' '; //Létrehozunk egy változót az második mintavételnek
    while(!isDigit(sample1)){ 
      while((sample1 != sample2) || sample1 == ' '){ //Addig olvasunk be a billentyűzetről, amíg a két minta nem egyezik meg
        sample1 = TranslateKey(module.getButtons()); //Lekérdezzük a billenytűzet kezelő modultól a gombok állapotát
        delay(20); //Várunk egy picit a prell kivédése érdekében
        sample2 = TranslateKey(module.getButtons()); //Lekérdezzük a billenytűzet kezelő modultól a gombok állapotát
      }
      while(sample1 == sample2){ //Várunk arra, hogy a felhasználó felengedje a gombot
        sample2 = TranslateKey(module.getButtons());
      }; 
    }
    code = code + sample1; //A key változó tartalmát hozzáfűzzük a code változóhoz
    myLCD.PCF8574_LCDSendChar(mask); //Kiíratjuk a *-ot az LCD kijelzőre
  }
  return(code); //Visszaadjuk a kódot sztringként*/
}


void LcdClearScreen(){ //Az LCD tartaémának törléséért felelős metódus
  myLCD.PCF8574_LCDClearScreen(); //LCD letörlése
}

void ShortBeep(){ //A rövid csippanásért felelős metódus
  if(!MUTED){ //Akkor működjön csak, ha a MUTED értéke false
  pinMode(BUZZER_PIN, OUTPUT); //Kimeneti pin beálltása
  tone(BUZZER_PIN, BUZZER_FREQUENCY); //Hang generálása
  delay(SHORT_TIME); //Várakozás SHORT_TIME időtartamnyit
  noTone(BUZZER_PIN); //Hang generálásának befejezése
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
  asm volatile ("jmp 0"); //Assembly jmp 0 parancs 
}

int FingerprintGetImage(){ //Az ujjlenyomatolvasó általi lépkészítésért felelős metódus
  int p = -1; //Ennek a változónak az értéket adjuk vissza, hiba esetén marad -1
   while (p != FINGERPRINT_OK) { //Megróbálkozunk az ujjlenyomat felvétellel
    p = finger.getImage(); //Itt nincs hibakijelzés
   }
   return p;
}

int FingerprintGenerateTemplate(int nr){ //Az ujjenyomat olvasó általi sablongenerálásért felelős metódus
  return finger.image2Tz(nr); //Az ujjlenyomat olvasó által visszaadott értéket adjuk vissza
}

int FingerprintCreateModel(){ //Az ujjenyomat olvasó általi modelkészítésért felelős metódus
  return finger.createModel(); //Az ujjlenyomat olvasó által visszaadott értéket adjuk vissza
}

int FingerprintStoreModel(int id){ //Az ujjenyomat olvasó általi tárolásért felelős metódus
  return finger.storeModel(id); //Az ujjlenyomat olvasó által visszaadott értéket adjuk vissza
}

int FingerprintSearch(){ //Az ujjlenyomat alapján történő azonosító keresésért felelős metódus. Ez nem az állapotot adja vissza, hanem az azonosítót.
  int p = finger.fingerSearch();
  if (p == FINGERPRINT_OK) { //Hiba esetén -1-et adunk vissza
    return finger.fingerID;
  }else{
    return -1;  
  }
}

void setup(){
  bool fingerprintOK = false; //Létrehozunk egy változót az ujjlenyomat olvasó állapotának reprezentálásához, memóriatakaréskosság végett egyenlőre ide rakjuk
  ShortBeep(); //Csak teszteléshez
  delay(50);
  //Beállítjuk az LCD kijelzőt
  myLCD.PCF8574_LCDBackLightSet(LCD_BACKLIGHT); //Engedélyezzük az LCD háttérvilágítását
  myLCD.PCF8574_LCDInit(myLCD.LCDCursorTypeOff); //Ne jelenítsük meg a kurzort az LCD kijelzőn
  Serial.begin(SERIAL_BAUDRATE); //Elindítjuk a soros kommunikációt
  mfrc522.PCD_Init();  //Inicializáljuk az RFID olvasót
  finger.begin(FPR_SERIAL_BAUDRATE); //Beállítjuk az ujjlenyomat olvasó kapcsolatának sebességét
  if (finger.verifyPassword()) {
    fingerprintOK = true;
  }
}


void loop(){
  //Figyeljük a bejövő soros adatforgalmat
  if (Serial.available()){
    deserializeJson(rx, Serial);
      if(rx["key"] == "get_code"){
        //Elküldjük válaszként a kódot!
        tx["key"] = "code_given";
        tx["code"] = GetCode('*'); 
        serializeJson(tx, Serial); //Szerializáljuk és továbbítjuk soros kommunikáción keresztül a JSON adatszerkezetünket
        Serial.println(); //Küldjünk egy sor végét is a soros kommunikáción keresztül
        tx.clear(); //Töröljük a json adatszerkezet tartalmát, mert már nincs szükségünk rá!
      }
      if(rx["key"] == "fp_get_image"){
        int p = FingerprintGetImage();
        tx["key"] = "fp_done"; //Megadjuk az eseményt (card_detected)
        tx["status"] = p; //Hozzáadjuk az adatszerkezethez az uid-t
        serializeJson(tx, Serial); //Szerializáljuk és továbbítjuk soros kommunikáción keresztül a JSON adatszerkezetünket
        Serial.println(); //Küldjünk egy sor végét is a soros kommunikáción keresztül
        tx.clear(); //Töröljük a json adatszerkezet tartalmát, mert már nincs szükségünk rá!
      }

      if(rx["key"] == "fp_gen_template"){
        int p = FingerprintGenerateTemplate(rx["nr"]);
        tx["key"] = "fp_done"; //Megadjuk az eseményt (card_detected)
        tx["status"] = p; //Hozzáadjuk az adatszerkezethez az uid-t
        serializeJson(tx, Serial); //Szerializáljuk és továbbítjuk soros kommunikáción keresztül a JSON adatszerkezetünket
        Serial.println(); //Küldjünk egy sor végét is a soros kommunikáción keresztül
        tx.clear(); //Töröljük a json adatszerkezet tartalmát, mert már nincs szükségünk rá!
      }
      
      if(rx["key"] == "fp_create_model"){
        int p = FingerprintCreateModel();
        tx["key"] = "fp_done"; //Megadjuk az eseményt (card_detected)
        tx["status"] = p; //Hozzáadjuk az adatszerkezethez az uid-t
        serializeJson(tx, Serial); //Szerializáljuk és továbbítjuk soros kommunikáción keresztül a JSON adatszerkezetünket
        Serial.println(); //Küldjünk egy sor végét is a soros kommunikáción keresztül
        tx.clear(); //Töröljük a json adatszerkezet tartalmát, mert már nincs szükségünk rá!
      }

      if(rx["key"] == "fp_store_model"){
        int p = FingerprintStoreModel(rx["id"]);
        tx["key"] = "fp_done"; //Megadjuk az eseményt (card_detected)
        tx["status"] = p; //Hozzáadjuk az adatszerkezethez az uid-t
        serializeJson(tx, Serial); //Szerializáljuk és továbbítjuk soros kommunikáción keresztül a JSON adatszerkezetünket
        Serial.println(); //Küldjünk egy sor végét is a soros kommunikáción keresztül
        tx.clear(); //Töröljük a json adatszerkezet tartalmát, mert már nincs szükségünk rá!
      }

      if(rx["key"] == "fp_search"){
        int p = {FingerprintSearch()};
        tx["key"] = "fp_done"; //Megadjuk az eseményt
        tx["finger"] = p; 
        serializeJson(tx, Serial); //Szerializáljuk és továbbítjuk soros kommunikáción keresztül a JSON adatszerkezetünket
        Serial.println(); //Küldjünk egy sor végét is a soros kommunikáción keresztül
        tx.clear(); //Töröljük a json adatszerkezet tartalmát, mert már nincs szükségünk rá!
      }

      if(rx["key"] == "lcd_goto"){
        LcdGoto(rx["row"], rx["column"]);
      }

      if(rx["key"] == "get_status"){
        tx["key"] = "status"; //Megadjuk az eseményt
        tx["status"] = 0; 
        serializeJson(tx, Serial); //Szerializáljuk és továbbítjuk soros kommunikáción keresztül a JSON adatszerkezetünket
        Serial.println(); //Küldjünk egy sor végét is a soros kommunikáción keresztül
        tx.clear(); //Töröljük a json adatszerkezet tartalmát, mert már nincs szükségünk rá!
      }

      if(rx["key"] == "lcd_cls"){
        LcdClearScreen();
      }
      
      if(rx["key"] == "lcd_send_str"){
        LcdSendString(rx["str"]);
      }
      if(rx["key"] == "sw_rst"){
        SoftwareReset();
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
  mfrc522.PICC_HaltA(); //Befelyzzük a kártya olvasá
  tx["key"] = "card_detected"; //Megadjuk az eseményt (card_detected)
  tx["uid"] = cardUID; //Hozzáadjuk az adatszerkezethez az uid-t
  serializeJson(tx, Serial); //Szerializáljuk és továbbítjuk soros kommunikáción keresztül a JSON adatszerkezetünket
  Serial.println(); //Küldjünk egy sor végét is a soros kommunikáción keresztül
  tx.clear(); //Töröljük a json adatszerkezet tartalmát, mert már nincs szükségünk rá!
}