<html>


<head>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/popper.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js')}}"></script>

    <title>Goldilocks Admin</title>
</head>


<body>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/goldilocks') }}">Goldilocks</a></li>
            <li class="breadcrumb-item active" aria-current="page">Admin</li>
        </ol>
    </nav>

    <div class="list-group">
        <a href="{{ url('/goldilocks/admin/researchers') }}" class="list-group-item list-group-item-action">Researchers</a>
        <a href="{{ url('/goldilocks/admin/listeners') }}" class="list-group-item list-group-item-action">Listeners</a>
        <a href="{{ url('/goldilocks/admin/logs') }}" class="list-group-item list-group-item-action">Listener Logs</a>
        <a href="{{ url('/goldilocks/admin/programs') }}" class="list-group-item list-group-item-action">Listener Programs</a>
    </div>


</div>

</body>

</html>