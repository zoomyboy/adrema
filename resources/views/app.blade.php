<!DOCTYPE html>
<html class="h-full">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
        <meta name="socketport" content="{{env('SOCKET_PORT')}}" />
        <meta name="adrema_base_url" content="/">
        @if(auth()->id())
        <meta name="meilisearch_key" content="{{config('scout.meilisearch.key')}}" />
        @endif
        @vite('resources/js/app.js')
    </head>
    <body class="min-h-full flex flex-col">
        @inertia('app" class="bg-gray-900 font-sans flex flex-col grow"')
    </body>
</html>
