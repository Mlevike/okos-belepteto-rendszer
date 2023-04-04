# okos-belepteto-rendszer
Okos, internetre kötött beléptető rendszer Raspberri Pi és Arduino alapokon.

## Telepítés
### Laravel Backend telepítése

0. Telepítsd a Docker Desktop alkalmazást, amyennyiben nem található meg a számítógépeden, az alkalmazás innen tölthető le [https://www.docker.com/products/docker-desktop/](https://www.docker.com/products/docker-desktop/)

1. Indísd el a Docker Desktop alkalmazást amennyiben nem futna.

2. Menj a repository .docker mappájába:

```
cd .docker
```
3. Indísd el a Docker konténert a következő paranccsal:

```
docker-compose exec myapp bash 
```

4. Telepísd a `laravel/ui` csomagot:
```
composer require laravel/ui
```

5. Telepísd a Bootstrap keretkörnyezetet::
```
php artisan ui bootstrap
php artisan ui bootstrap --auth
```
6. Futtasd az `npm install` parancsot annyak érdekében, hogy minden szükséges csomag feltelepüljön:
```
npm install
```
7. Futtasd az `npm run` parancsot annak érdekében, hogy minden szükséges fájl leforduljon:
```
npm run dev
```

