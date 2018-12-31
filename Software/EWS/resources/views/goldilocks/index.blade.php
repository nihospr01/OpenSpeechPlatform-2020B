<html>


<head>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/popper.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js')}}"></script>

    <title>Goldilocks v1.0</title>
</head>


<body>

    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">Goldilocks</li>
            </ol>
        </nav>

        <div class="list-group">
            <a href="{{ url('/goldilocks/admin') }}" class="list-group-item list-group-item-action">Admin</a>
            <a href="{{ url('/goldilocks/researcher/login') }}" class="list-group-item list-group-item-action">Researcher Page</a>
            <a href="{{ url('/goldilocks/listener') }}" class="list-group-item list-group-item-action">Self Adjustment</a>
            <a href="{{ url('/goldilocks/logs') }}" class="list-group-item list-group-item-action">Download Logs</a>
        </div>


    </div>

</body>

</html>