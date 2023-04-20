# okos-belepteto-rendszer

Okos, internetre kötött beléptető rendszer Raspberri Pi és Arduino alapokon.

## Telepítés

### Laravel Backend telepítése

0. Telepítsd a Docker Desktop alkalmazást, amyennyiben nem található meg a számítógépeden, az alkalmazás innen tölthető le [https://www.docker.com/products/docker-desktop/](https://www.docker.com/products/docker-desktop/)

1. Indísd el a Docker Desktop alkalmazást amennyiben nem futna.

2. Indísd el a Docker konténert a következő paranccsal:

```
docker-compose -f .docker/docker-compose.yml up
```
Sikeres telepítés után a projekt a `127.0.0.1:8000` címen lesz elérhető.

### A dokumentáció írása során felhasznált források:

- [https://hub.docker.com/r/bitnami/laravel](https://hub.docker.com/r/bitnami/laravel)
