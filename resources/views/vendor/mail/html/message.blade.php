@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => 'https://stamm-silva.de'])
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} Stamm Silva.
@endcomponent
@endslot
@endcomponent
