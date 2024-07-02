@component('mail::message')
# Hallo {{ $preventable->member->fullname }},

Du hast dich für die Veranstaltung __{{$preventable->form->name}}__ angemeldet.

Damit du an der Veranstaltung als leitende oder helfende Person teilnehmen kannst, ist noch folgendes einzureichen oder zu beachten.

{!! $documents !!}

@component('mail::subcopy')

Herzliche Grüße und gut Pfad

{{$settings->from_long}}
@endcomponent

@endcomponent
