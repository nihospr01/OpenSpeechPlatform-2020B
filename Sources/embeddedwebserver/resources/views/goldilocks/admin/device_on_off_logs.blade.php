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
                <li class="breadcrumb-item"><a href="{{ url('/goldilocks/admin') }}">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Device On-Off Logs</li>
            </ol>
        </nav>

        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">On Time</th>
                <th scope="col">Off Time</th>
            </tr>
            </thead>
            <tbody>
            @foreach($logs as $log)
                <tr>
                    <td scope="row">{{$log->id}}</td>
                    <td>{{$log->on_time}}</td>
                    <td>{{$log->off_time}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>


</body>

</html>