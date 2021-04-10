<!DOCTYPE html>
<html class="h-full">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
        <meta name="socketport" content="{{env('SOCKET_PORT')}}" />
        <link href="{{ mix('/css/app.css') }}" rel="stylesheet" />
        <script src="{{ mix('/js/app.js') }}" defer></script>
    </head>
    <body class="min-h-full flex flex-col">
        @inertia
    </body>
</html>
