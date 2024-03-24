@component('mail::message')
# Hallo {{$fullname}},

{{ $participant->form->mail_top }}

# Deine Daten

@foreach($config->sections as $section)
## {{$section->name}}
@foreach ($section->fields as $field)
* {{$field->name}}: {{$field->presentRaw()}}
@endforeach
@endforeach

{{ $participant->form->mail_bottom }}

@endcomponent
