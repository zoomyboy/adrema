# Adrema

**Schön, dass du den Weg hierhin gefunden hast!**

Da du diese Seite besuchst, gehörst du sicherlich zu den Leuten, die möglichst einfach die Daten ihrer Mitglieder pfelgen wollen. Das ist offiziell in der DPSG nur mit NaMi möglich.

Die AdReMa (= "AddRessManagement") macht das auch, nur einfacher, schöner und intuitiver als es NaMi tut.

![Mitglieder-Übersicht](https://git.zoomyboy.de/silva/adrema/raw/branch/master/doc/page/assets/img/member.jpg)

AdReMa kann von jedem und jeder genutzt werden, die einen NaMi-Account besitzt und Schreibrechte hat (i.d.R. sind das Stammesvorstände, e.V.-Mitglieder und andere, die Mitgliederdaten und deren Abrechungen und Beiträge pflegen müssen).

## Was kann ich mit AdReMa machen?

-   Basisdaten von Mitgliedern anzeigen und bearbeiten
-   Einfacher Filter nach Gruppierung, Tätigkeit, etc
-   Detailansichten mit allen zugehörigen Daten
-   Führungszeugnisse und Präventionssulungen nachhalten
-   Beitragszahlungen eintragen
-   Automatische Rechunungserstellung
-   Eigenen Beitragssatz hinterlegen (z.B. interner Stammes-Jahresbeitrag)
-   Generieren von Zuschusslisten (aktuell RdP NRW)
-   Einpflegen von internen Tätigkeiten, die nicht in NaMi vorhanden sind (um z.B. stammes-interne AGs / AKs zu verwalten)
-   Automatisches Erstellen und Managen von E-Mail-Verteilern mittels Mailman 3.0
-   eFz-Bescheinigung abrufen für alle Leitenden (das kann in NaMi nur jede\*r für sich selbst)
-   Ausbildungen eintragen (WBK-Bausteine)
-   Abrufen von Kontakten ins eigene Telefonbuch (mittels CardDAV)

Ziel dieses Projektes ist es, viele Dinge, die man normalerweise manuell zu tun hat so gut es geht zu automatisieren oder zumindest zu vereinfachen. So kann man sich als Leitende\*r / Vorstand auf die wichtigeren Dinge konzentrieren wie Gruppenstunden, Lager, Leiterrunden, etc.

Außerdem ist AdReMa auch problemlos auf Handys und Tablets bedienbar ("mobiles Design")

## Installation des Produktivsystems

1. Herunterladen der Beispiel Docker-Compose

    ```cmd
    curl https://git.zoomyboy.de/silva/adrema/raw/branch/master/docker-compose.prod.yml -o docker-compose.yml
    ```

2. Herunterladen der Beispiel Environmentvariablen-Datei

    ```cmd
    curl https://git.zoomyboy.de/silva/adrema/raw/branch/master/.app.env.example -o .app.env
    ```

3. In der `.app.env` notwendige Einstellungen vornehmen:

    - `APP_URL`: Hier sollte die URL (mit HTTPS) stehen, unter der Adrema erreichbar sein soll (z.B. `https://adrema.stamm-bipi.de`)
    - Mail-Server Einstellungen `MAIL_PORT`, `MAIL_HOST`, `MAIL_USERNAME`, `MAIL_PASSWORD` und `MAIL_ENCRYPTION` anpassen
    - `MAIL_FROM_NAME`: Der Name, der als Absender von E-Mails gesetzt wird (z.B. `Stamm Bipi Service`)
    - `MAIL_FROM_ADDRESS`: Die dazu gehörige E-Mail-Adresse, die natürlich für antworten erreichbar sein sollte (z.B. `vorstand@stamm-bipi.de`)
    - `DB_PASSWORD` und `MYSQL_PASSWORD`: Mit dem selben sicheren Passwort für die Datenbank versehen
    - `USER_EMAIL` und `USER_PASSWORD`: Einstellen des standard Adrema Logins

4. Container zur Gennerierung des App-Key starten

    ```cmd
    docker compose up php
    ```

    Nach einiger zeit wird ein App-Key generiert:

    ```cmd
    Keinen APP KEY gefunden. Key wird generiert: base64:xxx
    ```

    Container herunterfahren und entfernen

    ```cmd
    docker compose down
    ```

5. Der generierte App-Key muss als Environmentvariable (`APP_KEY`) mit in den Docker-Container gegeben werden. Kopiere den App-Key in die Datei `.app.env`

    ```env
    APP_KEY=base64:xxx
    ```

6. Alle Container starten

    ```cmd
    docker compose up -d
    ```

7. Nach kurzer Zeit ist AdReMa über <http://localhost:8000> erreichbar und es kann sich mit dem zuvor festgelegten Login eingeloggt werden

## Nutzen des Entwicklungssystmes

1. Klonen des Reposetories

    ```cmd
    git clone https://git.zoomyboy.de/silva/adrema.git
    ```

2. Kopieren der Beispiel Docker-Compose für das Entwickeln und nach Wünschen anpassen

    ```cmd
    cp docker-compose.dev.yml docker-compose.yml
    ```

3. Kopieren der Beispiel Environmentvariablen-Datei

    ```cmd
    cp .app.env.example .app.env
    ```

4. Submodule aktuallisieren

    ```cmd
    git submodule update --init
    ```

5. Container erstellen

    ```cmd
    docker compose build
    ```

6. Mit Schritt 3 und den folgenden der [Installation des Produktivsystems](#installation-des-produktivsystems) fortfahren
