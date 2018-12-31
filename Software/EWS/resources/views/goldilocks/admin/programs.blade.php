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
            <li class="breadcrumb-item active" aria-current="page">Programs</li>
        </ol>
    </nav>

    <table class="table table-hover">
        <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Listener ID</th>
            <th scope="col">Name</th>
            <th scope="col">Parameters</th>
            <th scope="col">Created At</th>
            <th scope="col">Updated At</th>
        </tr>
        </thead>
        <tbody>
        @foreach($programs as $program)
            <tr>
                <td scope="row">{{$program->id}}</td>
                <td>{{$program->listener_id}}</td>
                <td>{{$program->name}}</td>
                <td>{{$program->parameters}}</td>
                <td>{{$program->created_at}}</td>
                <td>{{$program->updated_at}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>


</body>

</html>