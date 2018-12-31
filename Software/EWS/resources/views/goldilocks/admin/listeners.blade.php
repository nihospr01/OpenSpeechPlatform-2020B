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
                <li class="breadcrumb-item active" aria-current="page">Listeners</li>
            </ol>
        </nav>

        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Listener</th>
                <th scope="col">Pin</th>
                <th scope="col">Created At</th>
                <th scope="col">Updated At</th>
            </tr>
            </thead>
            <tbody>
            @foreach($listeners as $listener)
                <tr>
                    <td scope="row">{{$listener->id}}</td>
                    <td>{{$listener->listener}}</td>
                    <td>{{$listener->pin}}</td>
                    <td>{{$listener->created_at}}</td>
                    <td>{{$listener->updated_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="row">
            <div class="col-3 offset-9">
                <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#newListenerModal">Create New</button>
            </div>
        </div>

    </div>

    <!-- Modal -->
    <div class="modal fade" id="newListenerModal" tabindex="-1" role="dialog" aria-labelledby="New Listener" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">New Listener</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                {{ Form::open(['url' => '/goldilocks/admin/listeners', 'method' => 'POST']) }}
                <div class="modal-body">
                    {{ Form::label('listener', 'Listener') }}
                    {{ Form::text('listener', '', array('class' => 'form-control', 'id' => 'listenerIdInput', 'style' => 'margin-bottom: 5px')) }}
                    {{ Form::label('pin', 'Pin') }}
                    {{ Form::text('pin', '', array('class' => 'form-control', 'id' => 'listenerPinInput')) }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
                {{ Form::close() }}

            </div>
        </div>
    </div>

    <script type="text/javascript">
        $('#newListenerModal').on('shown.bs.modal', function () {
            $('#listenerIdInput').trigger('focus')
        })
    </script>


</div>

</body>

</html>