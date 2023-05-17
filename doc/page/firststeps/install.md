---
layout: page
title: Installation
parent: Erste Schritte
nav_order: 1
---


# Installation

Adrema ist eine Web-Applikation, die auf einem Webserver installiert werden kann. Die Installation mit Docker wird empfohlen, da hier bereits alle notwendigen Dienste mit installiert werden.

{: .warning }
Für die Installation sind Grundkenntnisse im Umgang mit Docker und / oder Server-Umgebungen erforderlich. Wenn du hier Hilfe benötigst, [kontaktiere uns]({% link kontakt.md %}).

## Mindestanforderungen

Die Mindestanforderungen sind größtenteils die Anforderungen vom [Laravel Framework](https://laravel.com/docs/10.x/deployment#server-requirements). Diese (plus einige Extra-Anforderungen) sind hier kurz ausgeführt:


{: .block-title }
> Anforderungen
> 
> PHP >= 8.1  
> Ctype PHP Extension  
> cURL PHP Extension  
> DOM PHP Extension  
> Fileinfo PHP Extension  
> Filter PHP Extension  
> Hash PHP Extension  
> Mbstring PHP Extension  
> OpenSSL PHP Extension  
> PCRE PHP Extension  
> PDO PHP Extension  
> Session PHP Extension  
> Tokenizer PHP Extension  
> XML PHP Extensionnother paragraph  
> Texlive mit fonts-extra (pdflatex & xelatex)  
> rsync

## Installation mit Docker

```bash
git submodule update --init                                 # Submodules updaten
cp .app.env.example .app.env                                # Example env erstellen:
docker-compose build                                        # Container bauen
docker-compose run php php artisan key:generate --show      # Key generieren

# Ersetze nun "YOUR_APP_KEY" in .app.env mit dem generierten Key (base64:qzX....).  
# Führe nun den DB Container aus, um eine erste Version der Datenbank zu erstellen.   
docker-compose up db -d
docker-compose run php php artisan migrate --seed           # Migrations ausführen
docker-compose stop                                         # Alles stoppen, dann alles neu starten
docker-compose up -d
```

Nun kannst du auf localhost:8000 die App öffnen, einen LoadBalancer wie nginx verwenden, den Port mit CLI Optionen ändern, etc.

## Standard Login

Wenn du die Seeder ausführst (``--seed``, siehe oben), wird ein Benutzer mit folgenden Zugangsdaten erstellt:

* E-Mail-Adresse: admin@example.com
* Passwort: admin

