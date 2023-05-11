---
layout: page
title: Installation
parent: Erste Schritte
---

# Versionierung

## Ein Beispiel

> Eine Stammesvorsitzende - nennen wir sie Petra - öffnet ein Mitglied in NaMi, um eine Änderung der Adresse vorzunehmen
> 
> Währenddessen ändert jemand anderes - nennen wir ihn Bob - die Telefonnummer des gleichen Mitglieds
> 
> Petra speichert nun das Mitglied mit der __neuen Adresse__ ab, obwohl bei ihr im "bearbeiten-Formular" noch die __alte Telefonnummer__ steht.
>
> Resultat: Die Änderung der Adresse (Petras Änderung) wurde übernommen. Die Änderung von Bob (die Änderung der Telefonnummer) wurde aber überschrieben.
> Das Mitglied hat also nun die neue Adresse, aber noch die alte Telefonnummer.

Da auch übergeordnete Ebenen auf die Mitglieder der DPSG Zugriff haben und diese häufig auch bearbeiten können, ist dieses Szenario durchaus denkbar.

Die NaMi löst dieses Problem intern mit einer Versionsnummer, die jedes Mal um 1 erhöht wird, wenn jemand Änderungen an den Basisdaten vornimmt. So lässt sich feststellen, dass zwischenzeitlich eine Änderung durch eine\*n dritte\*n erfolgt ist.

## Versionen in Adrema

Die Adrema macht sich dieses System zunutze. Vor einem Update wird geprüft, ob zwischenzeitlich ein Update in NaMi vorgenommen wurde. Ist das der Fall, wird ein Hinweis angezeigt:

Du hast hier nun zwei Optionen:

> 1. Du aktualisierst das Mitglied in Adrema. Dadurch werden deine Änderungen rückgängig gemacht und das Mitglied erneut aus NaMi abgerufen. Danach kannst du deine Änderung erneut vornehmen.
> 
> 2. Du aktualisierst das Mitglied. Dabei spielt der aktuelle NaMi-Stand keine Rolle. __Dabei kann es allerdings zu Datenverlust kommen__ (wie oben beschrieben).

Auf diese Art und Weise ist sichergestellt, dass Änderungen sich nicht gegenseitig überschreiben.
