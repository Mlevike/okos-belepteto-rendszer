# Laravel PHP backend

## Telepítés

### Laravel Backend telepítése

0. Telepítsd a Docker Desktop alkalmazást, amennyiben nem található meg a számítógépeden, az alkalmazás innen tölthető le [https://www.docker.com/products/docker-desktop/](https://www.docker.com/products/docker-desktop/)

1. Indítsd el a Docker Desktop alkalmazást amennyiben nem futna.

2. Indítsd el a Docker konténert a következő paranccsal:

```
docker-compose -f .docker/docker-compose.yml up
```
Sikeres telepítés után a projekt a `127.0.0.1:8000` címen lesz elérhető.

### A dokumentáció írása során felhasznált források:

- [https://hub.docker.com/r/bitnami/laravel](https://hub.docker.com/r/bitnami/laravel)

## A projekt során használt Laravel modulok
- laravel/fortify a felhasználók webes felületen történő hitelesítéséhez.

## A projekt adatbázis modelljének felépítése
### History modell

A History modell a következő attribútumokat tartalmazza:
- arriveTime: A felhasználó beléptetőrenszeren keresztüli objektumba való belépésének időpontja.
- succesful: A felhasználó objektumba történő belépésének sikersségét rögzítő attribútum.
- leaveTime: A felhasználó beléptetőrenszeren keresztüli objektumból való kilépésének időpontja.
- workTime: A felhasználó objetumban töltött ideje, az arriveTime és leaveTime különbsége.
- direction: A ki/be léptetés iránya.
- user_id: Az érintett felhasználó azonosítója.

### User modell

A User modell a következő attribútumokat tartalmazza:
- name: Az érintett felhasználó neve.
- picture: Az érintett felhasználó profilképe.
- code: Az érintett felhsználó belépési kódja.
- fingerprint: Az érintett felhasználó ujjlenyomata.
- language: Az érintett felhasználó nyelvi beállítása.
- profile: Az adott felhasználó profilja.
- isAdmin: Az adott felhasználó rendelkezik-e admin jogosultsággal?
- isWebEnabled: Az adott felhasználó rendelkezik-e webes belépési jogosultsággal?
- isEntryEnabled: Az adott felhasználó rendelkezik-e az objektumba történő belépés jogosultsággával?
- isEmpoyee: Az adott felhasználó rendelkezik-e alkalmazotti jogosultságokkal?
- email: Az érintett felhasználó email címe.
- password: Az érintett felhasználó jelszava titkosítva.
- cardId: Az érintett felhasználó beléppését biztosító kártya azonosítója.

## A projektben használt útvonalak
- `/`: Az oldal kiindulópontja, a dashboard.blade.php nézetet hívja meg.
- `/logs`: Az oldalon, illetve a beléptetés során történő esetleges események visszanézhetőségét biztosító oldal, a logs.blade.php nézetet hívja meg.
- `/users`: Az oldal felhasználó kezezelését biztosító oldala, az UsersViewController index() metódusát hívja meg.
- `/users/add`: Az oldal felhasználók hozzáadását biztosító oldala, az UsersViewController add() metódusát hívja meg.
- `/users/edit`: Az oldal felhasználók módosítását biztosító oldala, az UsersViewController edit() metódusát hívja meg.
- `/users/delete`: Az oldal felhasználók hozzáadását biztosító oldala, az UsersViewController add() metódusát hívja meg.


## A projektben használt Laravel Controllerek
- UsersViewController: A felhasználókkal kapcsolatos műveletekért felelős vezérlő.

## A projekt frontetndjének kialakítására használt küső könyvtárak
-https://github.com/lipis/flag-icons
-Bootstrap
-Bootstrap icons
