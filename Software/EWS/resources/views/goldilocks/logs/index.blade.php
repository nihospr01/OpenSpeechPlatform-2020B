<html>


<head>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/popper.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js')}}"></script>

    <title>Logs</title>
    <style>
    .breadcrumb-item{
      font-size: 24;
    }
    .breadcrumb{
      font-size: 24;
    }

    .list-group{
      font-size: 24;
    }

    </style>
</head>


<body>

</body>

    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/goldilocks') }}">Goldilocks</a></li>
                <li class="breadcrumb-item active" aria-current="page">Download Logs</li>
            </ol>
        </nav>

        <div class="list-group">
            <a href="{{ url('/goldilocks/logs/researchers') }}" class="list-group-item list-group-item-action">Researchers CSV</a>
            <a href="{{ url('/goldilocks/logs/listeners') }}" class="list-group-item list-group-item-action">Listeners CSV</a>
            <a href="{{ url('/goldilocks/logs/programs') }}" class="list-group-item list-group-item-action">Programs CSV</a>
            <a href="{{ url('/goldilocks/logs/listener-logs') }}" class="list-group-item list-group-item-action">Listener Click Logs CSV (Old)</a>
            <a href="{{ url('/goldilocks/logs/adjustment-logs') }}" class="list-group-item list-group-item-action">Listener Adjustment Logs CSV (New)</a>
            <a href="{{ url('/goldilocks/logs/on-off-logs') }}" class="list-group-item list-group-item-action">Device On-Off Logs CSV</a>
        </div>

    </div>
</html>
