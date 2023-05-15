# Adrema

__Schön, dass du den Weg hierhin gefunden hast!__

Da du diese Seite besuchst, gehörst du sicherlich zu den Leuten, die möglichst einfach die Daten ihrer Mitglieder pfelgen wollen. Das ist offiziell in der DPSG nur mit NaMi möglich.

Die AdReMa (= "AddRessManagement") macht das auch, nur einfacher, schöner und intuitiver als es NaMi tut.

![Mitglieder-Übersicht](https://git.zoomyboy.de/silva/adrema/raw/branch/master/doc/assets/member.jpg)

AdReMa kann von jedem und jeder genutzt werden, die einen NaMi-Account besitzt und Schreibrechte hat (i.d.R. sind das Stammesvorstände, e.V.-Mitglieder und andere, die Mitgliederdaten und deren Abrechungen und Beiträge pflegen müssen).

## Was kann ich mit AdReMa machen?

* Basisdaten von Mitgliedern anzeigen und bearbeiten
* Einfacher Filter nach Gruppierung, Tätigkeit, etc
* Detailansichten mit allen zugehörigen Daten
* Führungszeugnisse und Präventionssulungen nachhalten
* Beitragszahlungen eintragen
* Automatisches Rechunungssystem
* Eigene Beiträge hinterlegen (z.B. interner Stammes-Jahresbeitrag)
* Generieren von Zuschusslisten (aktuell RdP NRW)
* Einpflegen von internen Tätigkeiten, die nicht in NaMi vorhanden sind (um z.B. stammes-interne AGs / AKs zu verwalten)
* Automatisches Erstellen und managen von E-Mail-Verteilern mittels Mailman 3.0
* eFz-Bescheinigung abrufen für alle Leitenden (das kann normalerweise nur jede*r einzelne für sich selbst)
* Ausbildungen eintragen (WBK-Bausteine)
* Abrufen von Kontakten ins eigene Telefonbuch (mittels CardDAV)

Ziel dieses Projektes ist es, viele Dinge, die man normalerweise manuell zu tun hat so gut es geht zu automatisieren oder zumindest zu vereinfachen. So kann man sich als Leitende*r / Vorstand auf die wichtigeren Dinge konzentrieren wie Gruppenstunden, Lager, Leiterrunden, etc.

Außerdem ist AdReMa auch problemlos auf Handys und Tablets bedienbar ("mobiles Design")

![Mobile Ansicht](https://git.zoomyboy.de/silva/adrema/raw/branch/master/doc/assets/member-mobile.jpg)
# Installation

Submodules updaten:

```
git submodule update --init
```

Example env erstellen:

```
cp .app.env.example .app.env
```

Container bauen

```
docker-compose build
```

Key generieren

```
docker-compose run php php artisan key:generate --show
```

Ersetze nun "YOUR_APP_KEY" in .app.env mit dem generierten Key (base64:qzX....).

Führe nun den DB Container aus, um eine erste Version der Datenbank zu erstellen. 

```
docker-compose up db -d
```

Migrations ausführen

```
docker-compose run php php artisan migrate --seed
```

Alles stoppen, dann alles neu starten

```
docker-compose stop
docker-compose up -d
```

Nun kannst du auf localhost:8000 die App öffnen, einen LB verwenden, den Port mit CLI Optionen ändern, etc.

## Standard Login

Wenn du die Seeder ausführst ("--seed", siehe oben), wird ein Benutzer mit folgenden Zugangsdaten erstellt:

* E-Mail-Adresse: admin@example.com
* Passwort: admin

