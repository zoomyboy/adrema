@component('mail::message')
# Liebe Familie {{ $repo->getFamilyName($repo->pages->first()) }},

Im Anhang findet ihr die aktuelle Rechnung des Stammes Silva für das laufende Jahr. Bitte begleicht diese bis zum angegebenen Datum.

@component('mail::subcopy')

Herzliche Grüße und gut Pfad

Der Stammesvorstand
@endcomponent

@endcomponent
