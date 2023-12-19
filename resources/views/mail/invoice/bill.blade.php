@component('mail::message')
# {{ $invoice->greeting }},

Im Anhang findet ihr die aktuelle Rechnung des Stammes Silva für das laufende Jahr. Bitte begleicht diese bis zum angegebenen Datum.

@component('mail::subcopy')

Herzliche Grüße und gut Pfad

Der Stammesvorstand
@endcomponent

@endcomponent
