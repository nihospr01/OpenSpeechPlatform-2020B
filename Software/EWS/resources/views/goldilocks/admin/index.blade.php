<html>


<head>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/popper.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js')}}"></script>

    <title>Goldilocks Admin</title>

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
        <a href="{{ url('/goldilocks/admin/logs') }}" class="list-group-item list-group-item-action">Listener Click Logs (Old)</a>
        <a href="{{ url('/goldilocks/admin/adjustment_logs') }}" class="list-group-item list-group-item-action">Listener Adjustment Logs (New)</a>
        <a href="{{ url('/goldilocks/admin/on_off_logs') }}" class="list-group-item list-group-item-action">Device On-Off Logs</a>
        <a href="{{ url('/goldilocks/admin/programs') }}" class="list-group-item list-group-item-action">Listener Programs</a>
        <a href="{{ url('/goldilocks/admin/generic') }}" class="list-group-item list-group-item-action">Modify Global Generic Program</a>
    </div>
</div>

</body>

</html>
