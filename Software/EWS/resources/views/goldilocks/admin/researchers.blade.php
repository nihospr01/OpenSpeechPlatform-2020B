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
                <li class="breadcrumb-item active" aria-current="page">Researchers</li>
            </ol>
        </nav>

        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Researcher</th>
                <th scope="col">Created At</th>
                <th scope="col">Updated At</th>
            </tr>
            </thead>
            <tbody>
                @foreach($researchers as $researcher)
                    <tr>
                        <td scope="row">{{$researcher->id}}</td>
                        <td>{{$researcher->researcher}}</td>
                        <td>{{$researcher->created_at}}</td>
                        <td>{{$researcher->updated_at}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="row">
            <div class="col-3 offset-9">
                <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#newResearcherModal">Create New</button>
            </div>
        </div>

    </div>

    <!-- Modal -->
    <div class="modal fade" id="newResearcherModal" tabindex="-1" role="dialog" aria-labelledby="New Researcher" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">New Researcher</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                {{ Form::open(['url' => '/goldilocks/admin/researchers', 'method' => 'POST']) }}
                    <div class="modal-body">
                        {{ Form::label('researcher', 'Researcher') }}
                        {{ Form::text('researcher', '', array('class' => 'form-control', 'id' => 'researcherIdInput')) }}
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
        $('#newResearcherModal').on('shown.bs.modal', function () {
            $('#researcherIdInput').trigger('focus')
        })
    </script>


</body>

</html>