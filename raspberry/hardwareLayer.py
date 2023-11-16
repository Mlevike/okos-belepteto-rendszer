7# -*- coding: utf-8 -*-

#Fontos azt megjegyezni, hogy az olvasótól kért muvelet csak a kártya hozzáérintése után fut le!

#Beimprtáljuk a szükséges könyvtárakat
import base64
import os
import json
import serial
import threading
import time
import sys
import RPi.GPIO as GPIO
from mfrc522 import SimpleMFRC522
import requests
from dotenv import load_dotenv
import subprocess

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
getMethodsUrl = 'https://mlevente.hu/belepteto/api/validation/get-methods'
validateUrl = "https://mlevente.hu/belepteto/api/validation/validate"
logUrl = "https://mlevente.hu/belepteto/log"
setupUrl = "https://mlevente.hu/belepteto/setup"
getCommandUrl = "https://mlevente.hu/belepteto/api/poll/get-command"
logCommandStateUrl = "https://mlevente.hu/belepteto/api/poll/log-command-state"

#Inicializájuk a Buzzer globális változóit 
buzzer = 7 #Kimeneti pin
buzzerTime = (1 / 2400) / 2 #Periódusidő/2

#Inicializáljuk a GPIO-t
GPIO.setmode(GPIO.BOARD) #BOARD pin kiosztás használata, azért, mert az idióta RFID könyvtár is ezt használja
GPIO.setup(buzzer,GPIO.OUT) #Buzzer pin kimenetre állítása
GPIO.setup(relay,GPIO.OUT) #Relé pin kimenetre állítása
GPIO.setup(redPin,GPIO.OUT) #Piros LED pin kimenetre állítása
GPIO.setup(greenPin,GPIO.OUT) #Zöld LED pin kimenetre állítása
GPIO.setup(bluePin,GPIO.OUT) #Kék LED pin kimenetre állítása
GPIO.output(relay, GPIO.HIGH) #Relé alapállapotba állítása

#Inicicializáljuk a webkamerát
filename = "photo.jpg" #Definiáljuk a fájnevet

#Létrehozunk egy globális változót jelenleg futtatott utasítás azonosítására
currentCommandRef = ""

def Authenticate(uid, entry, code, fingerprint, filename): #Az az authnetikációért felelős függvény
    URL = validateUrl
    try:
        print(entry)
        if(entry):
            if(filename != ""):
                file = open(filename, 'rb') #Megnyitjuk a képet
                print("Kép megnyitva")
                b64 = base64.b64encode(file.read()) #Átalakítjuk a képet BASE64 formátumúvá
                print("Kép átalakítva")
                data = {'access_token': os.getenv('ACCESS_TOKEN'), 'uid' : uid, 'code' : code, 'fingerprint' : fingerprint, 'entry': entry, 'picture' : b64}
                print(data)
                r = requests.post(URL, data = data)
            else:
                data = {'access_token': os.getenv('ACCESS_TOKEN'), 'uid' : uid, 'code' : code, 'fingerprint' : fingerprint, 'entry': entry}
                print(data)
                r = requests.post(URL, json = data)
        else:
             data = {'access_token': os.getenv('ACCESS_TOKEN'), 'uid' : uid, 'code' : code, 'fingerprint' : fingerprint, 'entry': entry}
             print(data)
             r = requests.post(URL, json = data)
        j = json.loads(json.dumps(r.json()))
        print(j)
        return j.get("success")
    except:
        print("kivétel történt!")
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

def TakePhoto(filename): #A fénykép készítésért felelős metódus
        webcamProcess = subprocess.run(["fswebcam", "--deinterlace", "-q", "-r", "640x480", filename]) #A returncode=0 azért kell, hogy kivételt emeljen a Python amennyiben valami hiba történne a kép készítesekkor
        if(webcamProcess.returncode == 0): #Ha a fswebcam futás során nullás kóddal lép ki!
            return True
        else:
            return False

def GetMethods(uid): #UID alapján megkapjuk az adott felhasználó hitelesítési módjait
    URL = getMethodsUrl
    print(URL)
    data = {'access_token': os.getenv('ACCESS_TOKEN'), 'uid' : uid}
    r = requests.post(URL, json = data)
    print("GetMethods(): " + str(r.status_code))
    #print the response text (the content of the requested file):
    print(r.status_code)
    if r.status_code == 200:
        try:
            j = json.loads(json.dumps(r.json()))
            return j
        except:
            return False
    else:
        return False

def GetCommand(): #UID alapján megkapjuk az adott felhasználó hitelesítési módjait
    URL = getCommandUrl
    print(URL)
    data = {'access_token': os.getenv('ACCESS_TOKEN')}
    r = requests.post(URL, json = data)
    print("GetCommand(): " + str(r.status_code))
    #print the response text (the content of the requested file):
    print(r.status_code)
    if r.status_code == 200:
        try:
            j = json.loads(json.dumps(r.json()))
            return j
        except:
            return False
    else:
        return False

def LogCommandState(reference_token, state, message): #A parancsok lefutásának sikereségét a szerver felé továbbító metódus
    URL = logCommandStateUrl
    print(URL)
    data = {'access_token': os.getenv('ACCESS_TOKEN'), 'reference_token': reference_token, 'state': state, 'message': message}
    r = requests.post(URL, json = data)
    print("LogCommandState(): " + str(r.status_code))
    #print the response text (the content of the requested file):
    print(r.status_code)
    if r.status_code == 200:
        return True #200-as kód esetén igaz értéket adjunk vissza
    else:
        return False


def ShortBeep(): #Rövid csippanást lejátszó metódus
    for i in range(600):
    	GPIO.output(buzzer, GPIO.LOW)
    	time.sleep(buzzerTime)
    	GPIO.output(buzzer, GPIO.HIGH)
    	time.sleep(buzzerTime)

"""def WaitForSerial(): #Soros porti válaszra váró metódus
    while True:
        if connection.inWaiting() != 0:
            break""" #Ez egyenlőre nem kell!

def LcdSendString(s): #LCD-re szöveget küldő metódus
    tx = {
                "key":"lcd_send_str",
                "str": s
              }
    connection.write(json.dumps(tx).encode())

def LcdClearScreen(): #LCD tartalmát törlő metódus
    tx = {
                 "key":"lcd_cls"
              }

    connection.write(json.dumps(tx).encode())

def LcdGoto(row, column): #LCD poziciót állító metódus
    tx = {
            "key":"lcd_goto",
            "row": row,
            "column": column
                          }
    connection.write(json.dumps(tx).encode())

def FP_GetImage():
    connection.flushInput()
    tx = {
        "key": "fp_get_image",
    }
    connection.write(json.dumps(tx).encode())
    while True:
        if connection.inWaiting != 0:
            break
    rx = json.loads(connection.readline().decode("utf-8")) #Beolvasunk a soros portról
    return(rx.get("status")) #Visszadjuk válaszként az ujj azonosítóját

def FP_GenerateTemplate(nr):
    connection.flushInput()
    tx = {
        "key": "fp_gen_template",
        "nr": nr,
    }
    connection.write(json.dumps(tx).encode())
    while True:
        if connection.inWaiting != 0:
            break
    rx = json.loads(connection.readline().decode("utf-8")) #Beolvasunk a soros portról
    return(rx.get("status")) #Visszadjuk válaszként az ujj azonosítóját

def FP_CreateModel():
    connection.flushInput()
    tx = {
        "key": "fp_create_model",
    }
    connection.write(json.dumps(tx).encode())
    while True:
        if connection.inWaiting() != 0:
            break
    rx = json.loads(connection.readline().decode("utf-8")) #Beolvasunk a soros portról
    return(rx.get("status")) #Visszadjuk válaszként az ujj azonosítóját

def FP_StoreModel(id):
    connection.flushInput()
    tx = {
        "key": "fp_store_model",
        "id": id,
    }
    connection.write(json.dumps(tx).encode())
    while True:
         if connection.inWaiting() != 0:
             break
    rx = json.loads(connection.readline().decode("utf-8")) #Beolvasunk a soros portról
    return(rx.get("status")) #Visszadjuk válaszként a státuszt

def FP_Search():
    connection.flushInput()
    tx = {
        "key": "fp_search",
    }
    connection.write(json.dumps(tx).encode())
    while True:
        if connection.inWaiting() != 0:
            break
    rx = json.loads(connection.readline().decode("utf-8")) #Beolvasunk a soros portról
    print(rx)
    return(rx.get("finger")) #Visszadjuk válaszként az ujj azonosítóját

def ExternalAuthentication(): #Kártya Authentikáció metódusa
    if(interface): # Ha a sorosport értéke "None", akkor ne próbáljuk megnyitni
        LcdClearScreen() #Töröljük az LCD kijelző tartalmát
        LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
        LcdSendString("Kerem a kartyat!") #LCD-re írunk
        time.sleep(0.2)
        timestamp = time.time() #Létrehozunk egy időbélyeget a megszakítás nélküli időzítéshez
        while True:
            if (time.time() - timestamp) > 3: #Három másodpercenként kérdezzen le a szerverről
                command = GetCommand() #Lekérdezzük a parancsot s szerverről
                if command: #Amennyiben érkezett parancs a szerverről
                    print("Van parancs!")
                    currentCommandRef = command.get("reference_token") #Elmentjük egy változóba a parancs-ra hivatkozó tokent
                    if command.get("command") == "register_fingerprint": #Ha ujjlenyomatolvasásról van szó
                        options = command.get("options")
                        id = json.loads(options).get("id")
                        print(id)
                        print("Ujjlenyomatot olvasunk...")
                        LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                        LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                        LcdSendString("Ujjlenyomat") #LCD-re írunk
                        LcdGoto(1, 0) 
                        LcdSendString("regisztracio") #LCD-re írunk
                        time.sleep(1) #Várunk egy keveset
                        LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                        LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                        LcdSendString("Kerem az ujjat!") #LCD-re írunk
                        if FP_GetImage() != 0:
                            LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                            LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                            LcdSendString("Olvasasi hiba!") #LCD-re írunk
                            LogCommandState(currentCommandRef, "failed", "Olvasási hiba!") #Logoljuk a művelet sikerességét
                            time.sleep(2)
                            break
                        if FP_GenerateTemplate(1) != 0:
                            LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                            LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                            LcdSendString("Sablon") #LCD-re írunk
                            LcdGoto(1, 0) 
                            LcdSendString("keszitesi hiba!") #LCD-re írunk
                            LogCommandState(currentCommandRef, "failed", "Sablon készítési hiba!") #Logoljuk a művelet sikerességét
                            time.sleep(2)
                            break
                        LcdClearScreen()
                        LcdGoto(0, 0)
                        LcdSendString("Ujra!") #LCD-re írunk
                        time.sleep(1)
                        if FP_GetImage() != 0:
                            LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                            LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                            LcdSendString("Olvasasi hiba!") #LCD-re írunk
                            LogCommandState(currentCommandRef, "failed", "Olvasási hiba!") #Logoljuk a művelet sikerességét
                            time.sleep(2)
                            break
                        if FP_GenerateTemplate(2) != 0:
                            LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                            LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                            LcdSendString("Sablon") #LCD-re írunk
                            LcdGoto(1, 0) 
                            LcdSendString("keszitesi hiba!") #LCD-re írunk
                            LogCommandState(currentCommandRef, "failed", "Sablon készítési hiba!") #Logoljuk a művelet sikerességét
                            time.sleep(2)
                            break
                        LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                        LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                        LcdSendString("Model alkotas...") #LCD-re írunk
                        if FP_CreateModel() != 0:
                            LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                            LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                            LcdSendString("Model") #LCD-re írunk
                            LcdGoto(1, 0) 
                            LcdSendString("alkotasi hiba!") #LCD-re írunk
                            LogCommandState(currentCommandRef, "failed", "Model alkotási hiba!") #Logoljuk a művelet sikerességét
                            time.sleep(2)
                            break
                        LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                        LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                        LcdSendString("Tarolas...") #LCD-re írunk
                        if FP_StoreModel(id) != 0:
                            LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                            LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                            LcdSendString("Tarolasi hiba!") #LCD-re írunk
                            LogCommandState(currentCommandRef, "failed", "Tárolási hiba!") #Logoljuk a művelet sikerességét
                            time.sleep(2)
                            break
                        LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                        LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                        LcdSendString("KESZ!") #LCD-re írunk
                        LogCommandState(currentCommandRef, "successful", "Teszt!") #Logoljuk a művelet sikerességét
                        time.sleep(1)
                        break
                timestamp = time.time() #"Nullázzuk" az időbélyeget
            if connection.inWaiting() != 0: #Ha van bejövő üzenet a soros porton, akkor azt beolvassuk
                data = connection.readline().decode("utf-8") #Pontosabban itt olvassuk be
                rx = json.loads(data) #Json belvasása
                if rx.get("key") == "card_detected": #Ha kártyát érintenek az olvasóhoz
                    LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                    LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                    LcdSendString("Kerem varjon...") #LCD-re írunk
                    time.sleep(0.2)
                    uid = rx.get("uid") #Kiolvassuk az uid-t
                    """isHere = GetIsHere(uid)
                    fetchedCode = GetCode(uid)
                     if ((fetchedCode == "") or (isHere == "1")): #Ha nem kapunk a szervertől kódot, akkor megtagadjuk a belépést
                        SendLog(uid, 0, 1) #Meghívjuk a logoló metódust
                        LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                        LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                        LcdSendString("Elutasitva") #LCD-re írunk
                        time.sleep(1) #Késleltetünk azért, hogy olvasható legyen a felirat
                        break"""
                    # innen kezdődik a hitelesítése a kártyának
                    methods = GetMethods(uid) #Lekérdezzük a hitelesítéshez jasználandó módszereket a szerverről
                    if methods == False:
                        LcdClearScreen()
                        LcdGoto(0, 0)
                        LcdSendString("Ismeretlen")
                        LcdGoto(1, 0)
                        LcdSendString("Kartya!")
                        time.sleep(1)
                        Authenticate(uid, True, '', '', '')
                        break
                    else:
                        print(methods)
                        code = ""
                        fingerprint = ""
                        uploadFilename = "" #Létrehozunk egy fájlnév változót, melyben a feltöltendő kép fájlnevét tároljuk, erre azért van szükség, hogy csak akkor töltsünk fel képet, mikor ténylegesen sikerült is csinálni
                        if methods.get("enabled"):
                            if methods.get("code"): #Ez így nem helyes, de nem 
                                LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                                LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                                LcdSendString("Kod: ") #LCD-re írunk
                                time.sleep(0.2)
                                tx = {
                               "key" : "get_code",
                                     }
                                connection.write(json.dumps(tx).encode()) #Kérünk kódot a felhasználótól
                                kodbeiras = True #Ez azért kell, hogy elkezdjük várni a kódot
                                while kodbeiras:  #Azért van itt, hogy megvárjuk a kódot
                                    if connection.inWaiting() != 0: #Ha van nejövő üzenet a soros porton, akkor azt beolvassuk
                                        data = connection.readline().decode("utf-8") #Pontosabban itt olvassuk be
                                        rx = json.loads(data) #Json belvasása  
                                        if rx.get("key") == "code_given": #Ha kód érkezik
                                            code = rx.get("code") #Kiolvassuk a kódot a json adatszerkezetből
                                            kodbeiras = False #Megjött a kód, már nem kell várni rá
                                time.sleep(0.2)

                            if methods.get("fingerprint"): #Ez lesz majd az ujjlenyomat olvasás rész
                                fingerprint = -1 #Ez csak ideiglenes, azért kell, hogy a rendszer mindeképpen csak helyes ujjlenyomat esetén engedjen be
                                numberOfTries = 1 #A próbálkozások számát rögzítő változó
                                id = -1 #Létrehozunk az ujjlenyomat tárolására egy id változót
                                while numberOfTries <= 3 and id == -1: #Egyenlőre a maximális próbálkozások száma legyen 3
                                    numberOfTries = numberOfTries + 1
                                    LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                                    LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                                    LcdSendString("Kerem az ujjat!") #LCD-re írunk
                                    #Majd a köztes lépések sikerességét is ellenőrizni kell
                                    time.sleep(0.2)
                                    FP_GetImage() #Rögzítünk egy ujjlenyomat képet
                                    time.sleep(0.2)
                                    FP_GenerateTemplate(1) #Generálunk belőle egy sablont
                                    time.sleep(0.2)
                                    id = FP_Search()
                                    print(str(id)) #Kikeressük az ujjlenyomathoz tartozó azonosítót
                                print("ID: " + str(id))
                                fingerprint = id #Itt lehet, hogy túl van biztosítva a program
                                time.sleep(1)
                            LcdClearScreen()
                            LcdGoto(0, 0)
                            LcdSendString("Hitelesites...")
                            time.sleep(0.2)
                            if(TakePhoto(filename)): #Képet készítünk az authetikációhoz
                                uploadFilename = filename
                            if Authenticate(uid, True, code, fingerprint, uploadFilename):
                                #SendLog(uid, 1, 1) #Meghívjuk a logoló metódust
                                LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                                LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                                LcdSendString("Elfogadva") #LCD-re írunk
                                TriggerRelay() #Kapcsoljuk a relét
                                time.sleep(1) #Késleltetünk azért, hogy olvasható legyen a felirat
                                break

                            else:
                                #SendLog(uid, 0, 1) #Meghívjuk a logoló metódust
                                LcdClearScreen() #Töröljük az LCD kijelző tartalmát
                                LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
                                LcdSendString("Elutasitva") #LCD-re írunk
                                time.sleep(1) #Késleltetünk azért, hogy olvasható legyen a felirat
                                break

def InternalAuthentication(): #Létrehozunk egy függvényt a belső kártyaolvasó figyeléséhez
    #Inicializájuk a belső olvasót
    internalReader = SimpleMFRC522() #Inicalizáljuk a belső RFID olvasót
    while True:
        SetLedColor("red") #Beállítjuk a LED színét pirosra
        id, text = internalReader.read() #Beolvassuk a belső kártyát
        uid = str(hex(id)[2:10]).replace('0', '')
        #isHere = GetIsHere(uid)
        ShortBeep() #Csippantunk jelezve a kártya beolvasást
        SetLedColor("none") #Kikapcsoljuk a LED-et
        if Authenticate(uid, False, "", "", filename): #A fingerprint és code helyett csak üres stringet írunk
            #SendLog(uid, 1, 0) #Meghívjuk a logoló metódust
            TriggerRelay() #Kapcsoljuk a relét
            SetLedColor("green") #Beállítjuk a LED színét zöldre
        else:
            #SendLog(uid, 0, 0) #Meghívjuk a logoló metódust
            SetLedColor ("red") #Beállítjuk a LED színét pirosra
        time.sleep(1) #Egy másodperces szünet

while True:  #Ez azért kell, hogy hiba esetén se álljon le
    SetLedColor("blue") #Csak tesztelésre
    ShortBeep() #Csak tesztelés miatt van itt!
    #TriggerRelay() #Csak tesztelés miatt van itt!
    #setupMode = True #Ez csak IDEIGLENES
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
                            print("Kommunikáció a szerverrel") #Kommunikálunk a felhasználóval
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
    except requests.exceptions.ConnectionError:
        LcdClearScreen() #Töröljük az LCD kijelző tartalmát
        LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
        LcdSendString("HALOZATI HIBA!") #LCD-re írunk
    except json.decoder.JSONDecodeError:
        LcdClearScreen() #Töröljük az LCD kijelző tartalmát
        LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
        LcdSendString("ROSSZ VALASZ!") #LCD-re írunk, ez a JSON felbontás sikertelenségére utal
        time.sleep(5) #5 másodperc múlva megpróbáljuk újraindítani
    except:
        LcdClearScreen() #Töröljük az LCD kijelző tartalmát
        LcdGoto(0, 0) #A kurzort visszaállítjuk a nulla pontra
        LcdSendString("NEM KEZELT") #LCD-re írunk
        LcdGoto(1, 0) #A kurzort visszaállítjuk a nulla pontra
        LcdSendString("KIVETEL!") #LCD-re írunk
        #Egyenlőre a kivételek nem működnek!
    finally:
        #LcdClearScreen()
        GPIO.cleanup() #Visszaállítjuk kiinduló állapotba a kimeneteket

