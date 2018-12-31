<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Researcher Page</title>  
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
</head>
    <body>
        <div id="container" class="m-3"></div>
        <script src="{{mix('js/researcher/app.js')}}" ></script>
    </body>
</html>