<x-mail::message>

# Hallo {{$fullname}},

<x-mail-view::editor :content="$topText"></x-mail-view::editor>

# Deine Daten

@foreach($config->sections as $section)
## {{$section->name}}
@foreach ($section->fields as $field)
* {{$field->name}}: {{$field->presentRaw()}}
@endforeach
@endforeach

<x-mail-view::editor :content="$bottomText"></x-mail-view::editor>

</x-mail::message>
