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
            <th scope="col">Delete Program</th>
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
                <td>
                    {!! Form::button('Delete', array('class' => 'btn btn-danger btn-block', 'onClick' => 'confirmDelete(' . $program . ')')) !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

    <!-- Modal -->
    <div class="modal fade" id="delProgramModal" tabindex="-1" role="dialog" aria-labelledby="Delete Program" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Delete Program?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                {{ Form::open(['id' => 'delProgramForm', 'route' => array('programs.destroy', $program)]) }}
                {{ method_field('DELETE') }}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

</body>

    <script>
        function confirmDelete(data) {
            var url = window.location.origin + '/goldilocks/admin/programs/' + data.id;
            $('#delProgramForm').attr('action', url);
            $('#delProgramModal').modal();
        }
    </script>

</html>
