@component('mail::message')
# Hallo {{$fullname}},

{{ $participant->form->mail_top }}
{{ $participant->form->mail_bottom }}

@foreach($config->sections as $section)
## {{$section->name}}
@foreach ($section->fields as $field)
* {{$field->name}}: {{$field->presentRaw()}}
@endforeach
@endforeach

Im Anhang findet ihr die aktuelle Rechnung des Stammes Silva für das laufende Jahr. Bitte begleicht diese bis zum angegebenen Datum.

@component('mail::subcopy')

Herzliche Grüße und gut Pfad

Der Stammesvorstand
@endcomponent

@endcomponent
