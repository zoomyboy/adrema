@component('mail::message')
# Hallo {{ $preventable->getMailRecipient()->name }},

<x-mail-view::editor :content="$bodyText->toArray()['blocks']"></x-mail-view::editor>

@component('mail::subcopy')

Herzliche Grüße und gut Pfad

{{$settings->from_long}}
@endcomponent

@endcomponent
