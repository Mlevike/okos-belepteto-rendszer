# okos-belepteto-rendszer
Okos, internetre kötött beléptető rendszer Raspberry Pi és Arduino alapokon.

# A rendszer leírása
A projekt céja egy hálózaton keresztül elérhető beléptető rendszer megvalósítása, amely engedélyezi a regisztrált felhasználók számára a belépést RFID kártya segítségével. A biztonság növelése érdekében az RFID kártya leovasását követően a felhasználóknak meg kell adni a saját 4 karakter hosszú PIN kódját és/vagy ujjlenyomat segítségével hitelesíteniük kell magukat. A sikeres belépést LED-ekkel, illetve egy 16x2-es karakteres LCD kijelzővel szemléltetjük. 

# Funkciók

- Az RFID jogosultságokat, illetve a felhasználók más releváns adatait egy adatbázisban tároljuk.
- A sikeres belépést az LCD megjelenő felirattal és hangjelzéssel jelezzük.
- Sikertelen belépésről az LCD tájékoztat minket a következő felirattal.
- A sikeres, illetve a sikertelen kilépési kísérletről egy LED, illetve hangjelzés segítségével tájékoztat a rendszer.

# A rendszer tervezett felépítése

![Belepteto rendszerterv](documentation/images/belepteto-rendszerterv.jpg)

# Felhasznált Hardware elemek
- 1 db Raspberry pi 1b rev.2
- 1 db SY-12W-K típusú relé
- 1 db 16x2-es karakteres LCD kijelzővel
- 1 db 4x4-es gomb mátrix
- 2 db RFID olvasó (MFRC-522)
- 1 db Arduino Nano
- 1 db 3,3-5V szintillesztő
- 1 db TM1650 típusú billenytűzet vezérlő IC
- 1 db RGB LED
- 2 db Csipogó hangszóró
- Egyéb kisebb alkatrészek


# A rendszer hardveres felépítése
A rendszer két egymástól jól elkülöníthető, ugyanakkor összekapcsolt hardveres részből áll, melyek a következők:

- A Rasperry Pi, illetve a hozzákapcsolt hardvare elemek, melynek feladata a teljes rendszert vezérlő Python alapú program futtatása, illetve a belső, a felhasználók kiengedéséért felelős RFID kártyaolvasó működtetése.
- Az Arduino mikrovezérlőre épülő, melynek felafata a felhasználók beengedéséért felelős külső RFID olvasó, illetve a hozzá tartozó LCD kijelző, ujjlenyomat olvasó, és számbillentyűzet vezérlése.

A kettő rendszer UART soros interfésszel kommunikál egymással, amely megvalósításához szükséges egy 3,3V-5V szintillesztő áramkör.

## Az Arduino alapú külső olvasó

### Az külső olvasó rövid leírása

A külső olvasó egy Arduino mikrokontroller által vezérelt panel, amelyre csatlakozik a 1602-es LCD kijelző, a kódbillentyűzet, a kártyaolvasó, illetve egy kis hangszóró, amely segítségével a felhasználónak visszajelzést adhatunk a kártya beolvasása során.
A kommunikáció a központi egység és a külső olvasó között soros interfészen valósul meg, az Ardunio, illetve a Raspberry Pi megfelelő lábainak felhasználásával, illetve egy szintillesztő modul közbeikatatásával. A szintillesztő modulra azért van szükség, mert az Arduino a kommunikáció során 5V-os jelszintet használ, míg a Raspberry Pi 3,3V-osat, ezért ha két eszközt közvetlenül csatlakozatnánk, akkor Rasberry Pi károsodna.

### Az olvasó felépítése (Csatlakozók nélkül)

![Beléptető külső olvasó felépítése](../documentation/images/belepteto_bb.jpg)

### Az olvasó kapcsolási rajza (Csatlakozók nélkül)

![Beléptető külső olvasó kapcsolási rajza](../documentation/images/belepteto_schem.jpg)

### Az olvasó nyomtatott áramköri lap terve

![Az olvasó nyomtatott áramköri lap terve](../arduino/pcb/belepteto_cropped.jpg)

### Az új ujjlenyomat felvételének folyamata

![Az új ujjlenyomat felvételének folyamata](../documentation/images/fp_enroll_process.jpg)

### Az ujjlenyomat keresésének folyamata

![Az ujjlenyomat keresésének folyamata](../documentation/images/fp_search_process.jpg)

### A külső olvasó kommunikációja a központi egységgel

A központi egységgel történő kommunikáláshoz [JSON](https://www.w3schools.com/js/js_json_intro.asp) adatszerkezetet használunk, ami így néz ki (ez az adatszerkezet csak minta, a program már nem tartalmazza):

    {
        "key": "show_unknown_card_message"
    }

#### Az adatszerkezetben használt mezők jelentése, rövid leírása:

A jelenlegi adatszerkezetben a "key" nevű mező adja meg a konkrét utasítást, illetve az olvasó felől bejövő adat típusát. A további mezők neve az adott üzenet tartalmától függ.

> **Megjegyzés:** A későbbiekben ez az adatszerkezet a kialakuló igényeknek megfelelően változhat. A változások is majd megtalálhatóak lesznek ebben a dokumentációban.

#### A kommunikáció során használható utasítások, események, a hozzájuk rendelt adatszerkezettel

##### Kártya detektálva üzenet

Ez az üzenet akkor érkezik az olvasó felől, ha egy RFID kártyát hozzá éritettek a leolvasóhoz és az olvasó sikeresen beolvasta a kártya egyedi azonosítóját (UID). Az adatszerkezet a következőképp néz ki:

    {
        "key":"card_detected",
        "uid":"d354ca2e"
    }

##### Kód megadva üzenet

Ez az üzenet akkor érkezik az olvasó felől, ha előzőleg kértük az olvasótól a kód bekérését, és a felhasználó beírta. Az adatszerkezet a következőképp néz ki:

    {
        "key":"code_given",
        "code":[a felhasználó által megadott kód]
    }

##### Az ujjlenyomatolvasással kapcsolatos válasz üzenet

Az ujjlenyomatolvasással kapcsolatos üzenetek válasz üzenete az ujjlenyomat keresés kivételével a következőképp néz ki:

    {
        "key":"fp_done",
        "status": [a művelet sikerességét kifejező numerikus érték]
    }

> **Az ujjlenyomat keresés esetén pedig a "status" helyett "finger" mezőt kapunk, amennyiben a keresés művelet sikeres, akkor megkapjuk az adott ujjlenyomat azonosítóját, egyébként "-1" értéket kapunk vissza.**

##### Kód bekérése

Ha ennek az üzenetnek küldjük az adatszerkezetét az olvasó felé, akkor az bekéri a felhasználótól a kódot, és válaszként visszaküldi egy ["Kód megadva üzenet"](#kód-megadva-esemény) formájában. Az üzenet adatszerkezete a következőképp néz ki:

    {
        "key":"get_code"
    }

##### Tetszőleges szöveg kiírása a kijelzőre

Ha ennek az üzenetnek küldjök az adatszerkezetét az olvasó felé, akkor az LCD kijelzőn tetszőleges feliratokat tudunk megjeleníteni (ékezetes karakterek nélkül!). Az üzenet adatszerkezete a következőképp néz ki:

    {
        "key":"lcd_send_str",
        "str":"[tetszőleges szöveg]"
    }
    
##### Kijelző tartalmának törlése

Ezzel az üzenettel a kijelző tartalmát tudjuk letörölni. Az adatszerkezete a következő:

    {
        "key":"lcd_cls"
    }

##### Tetszőleges helyre ugrás a kurzorral

Ezzel az üzenettel tetszőleges helyre pozicionálhatjuk a kijelző kurzorát. Az adatszerkezete a következő:

    {
        "key":"lcd_goto",
        "row":[sorszám]
        "column":[oszlopszám]
    }

> **Megjegyzés**: Az oszlopszámhoz, illetve a sorszámhoz integer típusú értéket kell írni, különben az üzenet nem fog működni!

##### Software reset végrehajtása

Ha ennek az üzenetnek küldjük az adatszerkezetét az olvasó felé, akkor az Arduino-n szoftveres újraindítást hajtunk végre. Ez a funkció akkor lehet hasznos, ha ha az Arduino valamilyen okból kifolyólag rendellenes tevékenységeket végez. Az üzenet adatszerkezete a következőképp néz ki:

    {
        "key":"sw_rst"
    }
    
##### Képalkotás ujjlenyomatolvasó használatával

Ezen üzenet elküldésével, egy ujjlenyomatképet tudunk rögzíteni.

    {     
        "key": "fp_get_image",
    }

##### Sablon generálás ujjlenyomatolvasó használatával

Ezen üzenet elküldésével, egy ujjlenyomat sablont tudunk generálni.

    {     
        "key": "fp_gen_template",
        "nr": [a sablont tároló buffer azonosítója],
    }

##### Model készítés ujjlenyomatolvasó használatával

Ezen üzenet elküldésével, egy ujjlenyomat modelt tudunk készíteni.

    {     
        "key": "fp_create_model",
    }

##### Model tárolása ujjlenyomatolvasó használatával

Ezen üzenet elküldésével, egy ujjlenyomat modelt tudunk tárolni.

    {     
        "key": "fp_store_model",
        "id": [az ujjlenyomat bejegyzés azonosítója],
    }

##### Ujjlenyomat azonosító keresése az ujjlenyomat olvasó használatával

Ezen üzenet elküldésével, egy ujjlenyomat modelt tudunk tárolni.

    {     
        "key": "fp_search",
    }




# A rendszer telepítése

## A web backend telepítése 

### Docker segítségével

1. Lépj a projekt `/backend/.docker` mappájába!
2. A megfelelő docker konténerek telepítéséhez add ki a következő parancsot:
```
docker-compose up
```
3. A `/backend` mappában a Docker Desktop segítségével futtasd a következő parancsot az adatbázis migrációk alkalmazásához:
```
php artisan migrate
```
4. Hozzuk létre a kezdeti beállításokat a következő paranccsal:
```
php artisan app:generate-default-settings
```
4. Hozzuk létre a kezdeti felhasználót a következő paranccsal:
```
php artisan app:create-first-user
```
**Megjegyzés:** Amennyiben paraméterként mást nem adunk meg, úgy az alapértelmezett belépési adatok a következők lesznek:
  - email: admin@admin.com
  - jelszó: jelszo

# Felhasznált software eszközök

- A Raspberry Pi-n található vezérlő szoftvert Python programozási nyelv segítségével fejlesztettük le, ez a szoftver kommunikál a Laravel alapú PHP backenddel REST API segítségével.
- pip
- gpio
- phpmyadmin
- Az Arudino programhoz felhasznált külső könyvtárak:

  - Arduino Keypad library - https://playground.arduino.cc/Code/Keypad/
  - HD44780_LCD_PCF8574 i2c LCD controller library - https://github.com/gavinlyonsrepo/HD44780_LCD_PCF8574
  - MFRC522v2 library - https://github.com/OSSLibraries/Arduino_MFRC522v2
  - ArdunioJSON - https://arduinojson.org/
  - Adafruit fingerprint libary - https://github.com/adafruit/Adafruit-Fingerprint-Sensor-Library

  A Python-hoz használt külső könyvtárak:

  - pyserial

