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
                <li class="breadcrumb-item active" aria-current="page">Listener Logs</li>
            </ol>
        </nav>

        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Listener ID</th>
                <th scope="col">Listener</th>
                <th scope="col">Researcher ID</th>
                <th scope="col">Researcher</th>
                <th scope="col">Starting Program ID</th>
                <th scope="col">Starting Program Name</th>
                <th scope="col">Ending Program ID</th>
                <th scope="col">Ending Program Name</th>
                <th scope="col">Timestamp</th>
                <th scope="col">Timezone</th>
                <th scope="col">Final LVH</th>
                <th scope="col">Steps</th>
                <th scope="col">Seconds Elapsed</th>
                <th scope="col">Step-By-Step Changes</th>
                <th scope="col">Starting G65</th>
                <th scope="col">Compression Ratio</th>
                <th scope="col">Low Multipliers</th>
                <th scope="col">High Multipliers</th>
                <th scope="col">Ending G65</th>
            </tr>
            </thead>
            <tbody>
            @foreach($logs as $log)
                <tr>
                    <td scope="row">{{$log->id}}</td>
                    <td>{{$log->listener_id}}</td>

                    @if (empty($log->listener))
                        <td>deleted</td>
                    @else
                        <td>{{$log->listener->listener}}</td>
                    @endif

                    <td>{{$log->researcher_id}}</td>
                    @if (empty($log->researcher))
                        <td>deleted</td>
                    @else
                        <td>{{$log->researcher->researcher}}</td>
                    @endif

                    <td>{{$log->start_program_id}}</td>
                    @if (empty($log->startProgram))
                        <td>deleted</td>
                    @else
                        <td>{{$log->startProgram->name}}</td>
                    @endif

                    <td>{{$log->end_program_id}}</td>
                    @if (empty($log->endProgram))
                        <td>deleted</td>
                    @else
                        <td>{{$log->endProgram->name}}</td>
                    @endif

                    <td>{{$log->client_finish_local}}</td>
                    <td>{{$log->client_timezone}}</td>
                    <td>{{$log->final_lvh}}</td>
                    <td>{{$log->steps}}</td>
                    <td>{{$log->seconds_elapsed}}</td>
                    <td>{{$log->change_string}}</td>
                    <td>{{$log->starting_g65}}</td>
                    <td>{{$log->compression_ratio}}</td>
                    <td>{{$log->l_multipliers}}</td>
                    <td>{{$log->h_multipliers}}</td>
                    <td>{{$log->ending_g65}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>


</body>

</html>