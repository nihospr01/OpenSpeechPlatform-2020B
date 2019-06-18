<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width" height="device-height" initial-scale="1">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
  <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/popper.min.js')}}"></script>
  <script type="text/javascript" src="{{ asset('js/bootstrap.min.js')}}"></script>


  <style>
  .circleBtn {
  width: 130px;
  height: 130px;
  border-radius: 50%;
  font-size: 30px;
  color: #fff;
  line-height: 130px;
  text-align: center;
  background: green;
}
.back {
  background-color: #e8ecf1;
  height: 800px;
  width: 100%;
  align-content: center;
  align-items: center;

}
.footer {
    position: fixed;
    left: 0;
    bottom: 10%;
    width: 100%;
    background-color: white;
    color: white;
    text-align: center;
}
.save{
  padding: .5rem;
  font-size:1.4rem;
  line-height:1.2;
  border-radius:.2rem;
  align-self: left;
  width:110px;
  height: 70px;
  margin-left: 10%;
  margin-bottom: 0;
}
.saveAs{
  padding: .5rem;
  font-size:1.4rem;
  line-height:1.2;
  border-radius:.2rem;
  right:0;
  width:110px;
  height: 70px;
align-items: center;
align-self: center;
margin-bottom: 0;
}

.btn-light{
    background: #blue;
} .btn-light:focus{
    background: green;
}

  </style>
</head>


<body>
  <div class="container-fluid" style="background-color: #e8ecf1;">
    <div class="row">
        <div  class="col-sm menu"><button type="button" class="btn btn-outline-dark btn-block" data-toggle="modal" data-target="#infoModal">{{ $listener->listener }}</button></div>
      </div>
    </div>

<div class= "container-fluid back"style="background-color: #e8ecf1;" >
  <div class="row">
    <div class="col">
      <h3 style="background-color: #e8ecf1; text-align:center"> My Programs </h3>
    </div>
  </div>


    <div class= "row">
      <div class="col" >
        <table class="table">
        @foreach($programs as $program)
        <tr>
          <td>
            <button type="button" id="program_{{$program->id}}" class="btn btn-light btn-lg btn-block prgrm" onclick="changeProgram({{$program->id}})">{{$program->name}}</button>
          </td>
          <!--
          Delete button for each listener's program
          <td width="12em">
            {!! Form::button('Delete', array('class' => 'btn btn-danger btn-lg btn-block', 'onClick' => 'confirmDelete(' . $program . ')')) !!}
          </td>
           -->
        </tr>
        @endforeach
        </table>
        @if(count($programs) == 0)
          <h4 style="color: blue; text-align: center; padding-top: 20px;">No programs to display</h4>
        @endif
        <p id="none_selected" style="color: red; padding-top: 15px"></p>
      </div>

      <div class="footer" style="background-color: #e8ecf1;">
        <div class= "row">
        <div class="col">
          <form method="POST" onsubmit="return validateForm()">
              {{ Form::token() }}
              <input type="hidden" id="program_id" name="program_id" value="-1">
              <button type="submit"  id="modify" class="btn btn-lg btn-info saveas" > Modify </button>
          </form>
        </div>
      </div>
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

                {{ Form::open(['id' => 'delProgramForm', 'route' => array('programs.delete', $program)]) }}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!--Modal for viewing hearing aid state-->
    <div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoModalLabel">HA Values</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="infoModalText">No info to show</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" style="float: left;" onclick="location='{{ url("goldilocks/listener/logout") }}'">Logout</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

      <script language="JavaScript">

        var selected;

        var programs = JSON.parse("{{$parameters}}".replace(/&quot;/g,'"'));

        //load in program
        if("{{$listener->current_program_id}}" !== ""){
            selectedProgram = Number("{{$listener->current_program_id}}");
            $('#program_'+selectedProgram).focus();
            changeProgram(selectedProgram);
        }

        function changeProgram(id){

            this['selected'] = id;
            document.getElementById("program_id").value = id;

            $.ajax({
                method: 'POST',
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                url: '/goldilocks/listener/programs/transmit',
                data: JSON.stringify({
                    program_id: this['selected']
                }),
                success: function(response){
                    console.log(response, "program changed");
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }

        function validateForm(){
            if(document.getElementById("program_id").value == -1){
                document.getElementById("none_selected").innerHTML = "Please select a program";
                return false;
            }
            else{
                document.getElementById("none_selected").innerHTML = "";
                return true;
            }
        }

        function confirmDelete(data) {
            var url = 'http://localhost:8000/goldilocks/admin/programs/' + data.id;
            $('#delProgramForm').attr('action', url);
            $('#delProgramModal').modal();
        }
      </script>




</body>
