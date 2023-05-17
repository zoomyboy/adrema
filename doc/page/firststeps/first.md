---
layout: page
title: Grundeinstellungen
parent: Erste Schritte
nav_order: 2
---

Dieses Dokument beschreibt die ersten Grundeinstellungen nach der Installation von Adrema.

## Anmeldung in NaMi

Als erstes solltest du deine Zugangsdaten zu NaMi (deine Mitgliedsnummer und dein Passwort) eingeben, um dich einmalig anzumelden. Deine Zugangsdaten werden in Adrema gespeichert und müssen nur bei der Ersteinrichtung einmalig angegeben werden.

{: .info }
Der NaMi Benutzer sollte Schreibrechte auf der gewünschten Gruppierung haben. Grundsätzlich lässt sich Adrema auch mit NaMi-Accounts nutzen, die nur Leserechte haben. Dann kann man aber keine NaMi-Daten aktualisieren, bzw nur Adrema-interne Änderungen vornehmen (was langweilig ist :D )

{% include imgcap.html img='init-login' caption="Login in NaMi" %}

## Suchparameter definieren

Nun solltest du Parameter für die NaMi-Suche definieren, die beim täglichen Abruf angewendet werden.

Dabei ist es notwendig, zuerst die Diözesan-Gruppierungsnummer anzugeben. Wenn du diese nicht weißt, gehe in NaMi auf den Reiter "Suche" und wähle bei "1. Ebene (Diözese)" deine Gruppierung aus. Dort erscheint dann die Gruppierungsnummer (eine 6-Stellige Zahl). Diese ist hier einzugeben.

Danach kannst du mit der Bezirks-Ebene weiter verfahren (analog "2. Ebene (Bezirk)" in NaMi).

Du kannst beim Mitglieds-Status auswählen, ob du nur aktive Mitglieder, nur inaktive Mitglieder oder beides ("kein") abrufen willst.

Du bekommst im unteren Bereich eine Vorschau eingeblendet, welche Mitglieder abgerufen werden würden. Dies ist lediglich eine Vorschau eines Live-Abrufs aus NaMi - es handelt sich also nicht um einen vollständigen Adrema-Datensatz, da wichtige Infos wie z.B. Geburtsdaten in diesem Prozess noch nicht abgerufen werden.

{% include imgcap.html img='init-members' caption="Suchparameter" %}

Wenn du mit der Vorschau zufrieden bist, klicke auf "weiter".

## Standard-Gruppierungsnummer

Für einige Prozesse benötigt Adrema die Standard-Gruppierungsnummer. Dies ist i.d.R. die Gruppierungsnummer deiner lokalen Gruppierung die du verwalten willst (z.B. dein Stamm).

{% include imgcap.html img='init-default-groupid' caption="Standard-Gruppierungsnummer" %}

## 4. Initialisierung starten

Danach führt Adrema im Hintergrund selbstständig einen ersten Abgleich durch. Dies kann je nach Datenmenge einige Minuten bis Stunden dauern.

{% include imgcap.html img='init-confirm' caption="Einrichtung abschließen" %}

Du wirst danach ins Dashboard weitergeleitet. Nach und nach wird sich die Mitgliederliste dann mit den Mitgliedern füllen, solange bis alles abgerufen ist.
