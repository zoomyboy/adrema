@component('mail::message')
# {{ $invoice->greeting }},

Hiermit möchten wir euch an die noch ausstehenden Mitgliedsbeiträge des Stammes Silva für das laufende Jahr erinnern. Bitte begleicht diese bis zum angegebenen Datum.

@component('mail::subcopy')

Herzliche Grüße und gut Pfad

Der Stammesvorstand
@endcomponent

@endcomponent
