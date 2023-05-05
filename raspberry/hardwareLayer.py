# -*- coding: utf-8 -*-

#Fontos azt megjegyezni, hogy az olvasótól kért muvelet csak a kártya hozzáérintése után fut le!

#Beimprtáljuk a szükséges könyvtárakat
import os
import json
import serial
import threading
import time
import sys
import RPi.GPIO as GPIO
from mfrc522 import SimpleMFRC522
import requests
from argon2 import PasswordHasher
from dotenv import load_dotenv

#Betöltjük a .env file-t

load_dotenv()

#internalCardDetected = False #Létrehozunk egy globális változót a belső kártyaérintés érzékelésére

#Ellenőrizzük azt, hogy telepítési módban indul-e az olvasó?
setupMode = False #Ha a telapítési módhoz szükséges argumentumot megkapjuk, akkor ezt átállítjuk True-ra
if len(sys.argv) == 2: #Ha az argumentumok száma 2
    if sys.argv[1] == "setup": #Ha a 2. argumentum értéke setup, akkor... 
        setupMode = True #Átállítjuk a változó értékét True-ra

#Definiáljuk a LED pineket

redPin = 12
greenPin = 13
bluePin = 15

#Beaálítjuk a relé portot tartalmazó globális változót

relay = 11 #Kimeneti pin

#Inicializáljuk a soros kapcsolatot
interface = "/dev/ttyAMA0" #Sorosport neve
connection = serial.Serial(port=interface, baudrate=9600) #Létrehozunk egy sorosportos kapcsolatot
connection.reset_input_buffer() #Töröljük a soros buffert a tiszta indulás érdekében

#Beállítjuk a kártya validálás linkjét
validateUrl = "https://mlevente.hu/belepteto/public/validate/"
logUrl = "https://mlevente.hu/belepteto/public/log"
setupUrl = "https://mlevente.hu/belepteto/public/setup"


#Inicializájuk a Buzzer globális változóit 
buzzer = 7 #Kimeneti pin
buzzerTime = 0.000416666667 / 2 #Periódusidő/2

#Inicializáljuk a GPIO-t
GPIO.setmode(GPIO.BOARD) #BOARD pin kiosztás használata, azért, mert az idióta RFID könyvtár is ezt használja
GPIO.setup(buzzer,GPIO.OUT) #Buzzer pin kimenetre állítása
GPIO.setup(relay,GPIO.OUT) #Relé pin kimenetre állítása
GPIO.setup(redPin,GPIO.OUT) #Piros LED pin kimenetre állítása
GPIO.setup(greenPin,GPIO.OUT) #Zöld LED pin kimenetre állítása
GPIO.setup(bluePin,GPIO.OUT) #Kék LED pin kimenetre állítása
GPIO.output(relay, GPIO.HIGH) #Relé alapállapotba állítása

#Inicializáljuk a jelszó hashelőt
ph = PasswordHasher()

def Authenticate(fetchedCode, code): #Ez az argon2 hash alapú autentikációért felelős függvény
    try:
        return ph.verify(fetchedCode, str(code))
    except:
        return False

def SetLedColor(color):
    if color == "red": #Piros szín esetén
        GPIO.output(redPin, GPIO.HIGH)
        GPIO.output(greenPin, GPIO.LOW)
        GPIO.output(bluePin, GPIO.LOW)
    if color == "green": #Zöld szín esetén
        GPIO.output(redPin, GPIO.LOW)
        GPIO.output(greenPin, GPIO.HIGH)
        GPIO.output(bluePin, GPIO.LOW)
    if color == "blue": #Kék szín esetén
        GPIO.output(redPin, GPIO.LOW)
        GPIO.output(greenPin, GPIO.LOW)
        GPIO.output(bluePin, GPIO.HIGH)
    if color == "none": #Kikapcsolt állapot esetén
        GPIO.output(redPin, GPIO.LOW)
        GPIO.output(greenPin, GPIO.LOW)
        GPIO.output(bluePin, GPIO.LOW)

def TriggerRelay(): #Relét kapcsoló metódus
    GPIO.output(relay, GPIO.LOW)
    time.sleep(0.1)
    GPIO.output(relay, GPIO.HIGH)

def GetCode(uid): #UID alapján kódot lekérő metódus
    URL = validateUrl + uid
    print(URL)
    r = requests.get(URL, auth=(os.getenv('SERVER_USERNAME'), os.getenv('SERVER_PW')))
    print("GetCode(): " + str(r.status_code))
    j = json.loads(json.dumps(r.json()))
    return j.get("code")

def GetIsHere(uid): #UID alapján itt létet lekérő metódus
    URL = validateUrl + uid
    r = requests.get(URL, auth=(os.getenv('SERVER_USERNAME'), os.getenv('SERVER_PW')))
    print("GetIsHere(): " + str(r.status_code))
    j = json.loads(json.dumps(r.json()))
    return j.get("isHere")


def SendLog(uid, successful, entry): #Logot mentő metódus
    URL = logUrl + "?uid=" + str(uid) + "&successful=" + str(successful) + "&entry=" + str(entry)
    print(URL) #Teszteléshez
    r = requests.get(URL, auth=(os.getenv('SERVER_USERNAME'), os.getenv('SERVER_PW')))
    print("SendLog(): " + str(r.status_code))
    return r.status_code

def ShortBeep(): #Rövid csippanást lejátszó metódus
    for i in range(600):
    	GPIO.output(buzzer, GPIO.LOW)
    	time.sleep(buzzerTime)
    	GPIO.output(buzzer, GPIO.HIGH)
    	time.sleep(buzzerTime)

def LcdSendString(s): #LCD-re szöveget küldő metódus
    tx = {
                "type":"action",
                "action":"lcd_send_string",
                "string": s
              }
    connection.write(json.dumps(tx).encode())

def LcdClearScreen(): #LCD tartalmát törlő metódus
    tx = {
                 "type":"action",
                 "action":"lcd_clear_screen"
              }

    connection.write(json.dumps(tx).encode())

def LcdGoto(row, column): #LCD poziciót állító metódus
    tx = {
           "type":"action",
            "action":"lcd_goto",
            "row": row,
            "column": column
                          }
    connection.write(json.dumps(tx).encode())

def ExternalAuthentication(): #Kártya Authentikáció metódusa
    if(interface): # Ha a sorosport értéke "None", akkor ne próbáljuk megnyitni
        LcdClearScreen() #Töröljük az LCD kijelző tartalmát
        LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
        LcdSendString("Kerem a kartyat!") #LCD-re írunk
        while True:
            if connection.inWaiting() != 0: #Ha van bejövő üzenet a soros porton, akkor azt beolvassuk
                data = connection.readline().decode("utf-8") #Pontosabban itt olvassuk be
                rx = json.loads(data) #Json belvasása
                if rx.get("type") == "event": #Ha történik valamilyen esemény a külső olvasón
                    if rx.get("event") == "card_detected": #Ha kártyát érintenek az olvasóhoz
                        uid = rx.get("uid") #Kiolvassuk az uid-t
                        isHere = GetIsHere(uid)
                        fetchedCode = GetCode(uid)
                        if ((fetchedCode == "") or (isHere == "1")): #Ha nem kapunk a szervertől kódot, akkor megtagadjuk a belépést
                            SendLog(uid, 0, 1) #Meghívjuk a logoló metódust
                            LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                            LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                            LcdSendString("Elutasitva") #LCD-re írunk
                            time.sleep(1) #Késleltetünk azért, hogy olvasható legyen a felirat
                            break
                        # innen kezdődik a hitelesítése a kártyának
                        LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                        LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                        LcdSendString("Kod: ") #LCD-re írunk
                        tx = {
                                    "type" : "action",
                                    "action" : "get_code",
                             }
                        connection.write(json.dumps(tx).encode()) #Kérünk kódot a felhasználótól
                        kodbeiras = True #Ez azért kell, hogy elkezdjük várni a kódot
                        code = ""
                        while kodbeiras:  #Azért van itt, hogy megvárjuk a kódot
                            if connection.inWaiting() != 0: #Ha van nejövő üzenet a soros porton, akkor azt beolvassuk
                                data = connection.readline().decode("utf-8") #Pontosabban itt olvassuk be
                                rx = json.loads(data) #Json belvasása  
                                if rx.get("type") == "event": #Ha esemény érkezik
                                    if rx.get("event") == "code_given": #Ha kód érkezik
                                        code = rx.get("code") #Kiolvassuk a kódot a json adatszerkezetből
                                        kodbeiras = False #Megjött a kód, már nem kell várni rá
                        if Authenticate(fetchedCode, code):
                            SendLog(uid, 1, 1) #Meghívjuk a logoló metódust
                            LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                            LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                            LcdSendString("Elfogadva") #LCD-re írunk
                            TriggerRelay() #Kapcsoljuk a relét
                            time.sleep(1) #Késleltetünk azért, hogy olvasható legyen a felirat
                            break
                        else:
                            SendLog(uid, 0, 1) #Meghívjuk a logoló metódust
                            LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                            LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                            LcdSendString("Elutasitva") #LCD-re írunk
                            time.sleep(1) #Késleltetünk azért, hogy olvasható legyen a felirat
                            break

def InternalAuthentication(): #Létrehozunk egy függvényt a belső kártyaolvasó figyeléséhez
    #Inicializájuk a belső olvasót
    SetLedColor("red") #LED színét pirosra állítjuk
    internalReader = SimpleMFRC522() #Inicalizáljuk a belső RFID olvasót
    while True:
        id, text = internalReader.read() #Beolvassuk a belső kártyát
        uid = str(hex(id)[2:10]).replace('0', '')
        isHere = GetIsHere(uid)
        ShortBeep() #Csippantunk jelezve a kártya beolvasást
        SetLedColor("none") #Kikapcsoljuk a LED-et
        if (GetCode(uid) != "") and (isHere != "0"): #Ha kapunk vissza kódot
            SendLog(uid, 1, 0) #Meghívjuk a logoló metódust
            TriggerRelay() #Kapcsoljuk a relét
            SetLedColor("green") #Beállítjuk a LED színét zöldre
        else:
            SendLog(uid, 0, 0) #Meghívjuk a logoló metódust
            SetLedColor ("red") #Beállítjuk a LED színét pirosra
        time.sleep(1) #Egy másodperces szünet
        SetLedColor("red") #Beállítjuk a LED színét pirosra

SetLedColor("blue") #Csak tesztelésre
print(GetCode("16722ba2")) #Csak tesztelés miatt van itt!
ShortBeep() #Csak tesztelés miatt van itt!
TriggerRelay() #Csak tesztelés miatt van itt!

try:
    if not(setupMode):
        internalReadThread = threading.Thread(target=InternalAuthentication) #Létrehozunk egy háttér folyamatot a belső olvasó kártyadetektálásához
        internalReadThread.start() #Elindítjuk a belső olvasó háttérfolyamatát
        while True:
            ExternalAuthentication() #Elindítjuk az Authentikáció
    else:
        print("Telepítési mód:")
        print("--------------")
        LcdClearScreen() #Töröljük az LCD kijelző tartalmát
        LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
        LcdSendString("TELEPITESI MOD") #LCD-re írunk
        LcdGoto(1, 0) #A kurzort a második sor első pontjára állítjuk
        LcdSendString("Kerem a kartyat!") #LCD-re írunk
        while True:
            if connection.inWaiting() != 0: #Ha van bejövő üzenet a soros porton, akkor azt beolvassuk
                data = connection.readline().decode("utf-8") #Pontosabban itt olvassuk be
                rx = json.loads(data) #Json belvasása
                if rx.get("type") == "event": #Ha történik valamilyen esemény a külső olvasón
                    if rx.get("event") == "card_detected": #Ha kártyát érintenek az olvasóhoz
                        uid = rx.get("uid") #Kiolvassuk az uid-t
                        LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                        LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                        LcdSendString("TELEPITESI MOD") #LCD-re írunk
                        LcdGoto(1, 0) #A kurzort a második sor első pontjára állítjuk
                        LcdSendString("Kommunikacio...") #LCD-re írunk
                        URL = setupUrl + "?cardId=" + uid
                        r = requests.get(URL, auth=(os.getenv('SERVER_USERNAME'), os.getenv('SERVER_PW'))) #Végrehajtjuk a lekérdezést
                        print("Kommunikáció a szerverrel", end=' ') #Kommunikálunk a felhasználóval
                        if r.status_code == 200:
                            print("[OK] (" + str(r.status_code) + ")") #Siker esetén
                            LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                            LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                            LcdSendString("TELEPITESI MOD") #LCD-re írunk
                            LcdGoto(1, 0) #A kurzort a második sor első pontjára állítjuk
                            LcdSendString("OK (" + str(r.status_code) + ")" ) #LCD-re írunk
                        else:
                            print("[HIBA] (" + str(r.status_code) + ")") #Sikertelenség esetén
                            LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                            LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                            LcdSendString("TELEPITESI MOD") #LCD-re írunk
                            LcdGoto(1, 0) #A kurzort a második sor első pontjára állítjuk
                            LcdSendString("HIBA (" + str(r.status_code) + ")") #LCD-re írunk
finally:
    LcdClearScreen()
    GPIO.cleanup() #Visszaállítjuk kiinduló állapotba a kimeneteket
