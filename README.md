# Adrema

__Schön, dass du den Weg hierhin gefunden hast!__

Da du diese Seite besuchst, gehörst du sicherlich zu den Leuten, die möglichst einfach die Daten ihrer Mitglieder pfelgen wollen. Das ist offiziell in der DPSG nur mit NaMi möglich.

Die AdReMa (= "AddRessManagement") macht das auch, nur einfacher, schöner und intuitiver als es NaMi tut.

![Mitglieder-Übersicht](https://git.zoomyboy.de/silva/adrema/raw/branch/master/doc/page/assets/img/member.jpg)

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

# Installation

## App Key generieren

Kopiere .app.env.example nach .app.env

```
cp .app.env.example .app.env
```

Services starten:

```
docker compose up
```

Es wird die ein App Key generiert: ``Keinen APP KEY gefunden. Key wird generiert: base64:..........``

Kopiere diesen App key und setze in in .app.env als APP_KEY ein (APP_KEY=base64:........).

## Einstellungen

Passe in der .app.env dann folgende Einstellungen an:

### APP_URL

Hier sollte die URL (mit HTTPS) stehen, unter der Adrema erreichbar sein soll (z.B. https://adrema.stamm-bipi.de)

### Mail

Setze nun die Einstellungen für den Mail-Versand ein. Du solltest mindestens MAIL_PORT, MAIL_HOST, MAIL_USERNAME, MAIL_PASSWORD und MAIL_ENCRYPTION setzen.

MAIL_FROM_NAME ist der Name, der als Absender von E-Mails gesetzt wird. z.B. "Stamm Bipi Service".

MAIL_FROM_ADDRESS die dazu gehörige E-Mail-Adresse, die natürlich erreichbar sein sollte (z.B. "vorstand@stamm-bipi.de").

### DB Passwort

Setze die beiden letzten Variablen (da wo "secret_db_password" steht) auf ein generiertes sicheres Passwort. Bei beiden Variablen muss der gleiche Wert eingestellt werden (also so wie vorher, nur sicherer :D )

## Starten

Führe nun den DB Container aus, um eine erste Version der Datenbank zu erstellen. 

```
docker-compose up db -d
```

Nun kannst du auf localhost:8000 die App öffnen, einen LB verwenden, den Port mit CLI Optionen ändern, etc.

## Standard Login

Beim ersten Starten wird ein Benutzer mit folgenden Zugangsdaten erstellt:

* E-Mail-Adresse: admin@example.com
* Passwort: admin

