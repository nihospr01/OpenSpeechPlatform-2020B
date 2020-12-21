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



    </style>
</head>


<body>

    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/goldilocks') }}">Goldilocks</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/goldilocks/admin') }}">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Listener Logs</li>
            </ol>
        </nav>

        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Listener ID</th>
                <th scope="col">Action</th>
                <th scope="col">LVH</th>
                <th scope="col">Program State</th>
                <th scope="col">Created At</th>
                <th scope="col">Updated At</th>
            </tr>
            </thead>
            <tbody>
            @foreach($logs as $log)
                <tr>
                    <td scope="row">{{$log->id}}</td>
                    <td>{{$log->listener_id}}</td>
                    <td>{{$log->action}}</td>
                    <td>{{$log->lvh_values}}</td>
                    <td>{{$log->program_state}}</td>
                    <td>{{$log->created_at}}</td>
                    <td>{{$log->updated_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>


</body>

</html>
