<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/popper.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js')}}"></script>
    <style>
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] {
            -moz-appearance: textfield;
        }
        input#q156 {
            transform: scale(2);
        }

        .nav-pills>a {
            border-right: 1px solid #16A2B7;
            border-top: 1px solid #16A2B7;
            border-bottom: 1px solid #16A2B7;
            border-left: 1px solid #16A2B7;
        }
    </style>

    <title>Amplification Parameters</title>

</head>

<body>

    <div class="container" style="margin-top: 10px">

        <nav class="nav nav-pills nav-justified" style="margin-bottom: 10px">
            <a class="nav-item nav-link text-info bg-white" href="{{ url('/researcher/amplification') }}">Amplification</a>
            <a class="nav-item nav-link text-white bg-info" href="#">Noise Management</a>
            <a class="nav-item nav-link text-info bg-white" href="{{ url('/researcher/feedback') }}">Feedback Management</a>
        </nav>


        <div class="form-check">
            <input class="form-check-input" type="radio" name="noise_estimation_type" id="type_1" value="1">
            <label class="form-check-label" for="type_1">
                Arslan power averaging procedure
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="noise_estimation_type" id="type_2" value="2">
            <label class="form-check-label" for="type_2">
                Hirsch and Ehrlicher weighted noise averaging procedure
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="noise_estimation_type" id="type_3" value="3">
            <label class="form-check-label" for="type_3">
                Cohen and Berdugo MCRA Procedure
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="noise_estimation_type" id="type_0" value="0" checked>
            <label class="form-check-label" for="type_0">
                None
            </label>
        </div>

        <form>
            <div class="form-group">
                <label for="spectral_subtraction">Spectral Subtraction Parameter (0 to 1) </label>
                <input type="number" class="form-control" id="spectral_subtraction_input" value="0">
            </div>

        </form>


        <div class="row align-items-center" style="margin: 1px">
            <div class="col-6">
                <button id="resetButton" class="btn btn-info btn-block">Undo</button>
            </div>
            <div class="col-6">
                <button id="transmitButton" class="btn btn-info btn-block">Transmit</button>
            </div>
        </div>

        <p id="text"></p>

    </div>

    <script type="text/javascript">
        var parameters = {
            'left': {
                'noise_estimation_type': 0,
                'spectral_type': 0,
                'spectral_subtraction': 0
            },
            'right': {
                'noise_estimation_type': 0,
                'spectral_type': 0,
                'spectral_subtraction': 0
            }
        };


        //pull data on loading the page
        readMHA();


        //on clicking reset button, read from MHA
        $("#resetButton").click(function(){
            readMHA();
        });

        //on clicking transmit button, send data to MHA
        $("#transmitButton").click(function(){
            transmit();
        });

        $("#type_0").click(function (){
            $("#type_0").prop("checked", true)
            parameters['left']['noise_estimation_type'] = 0;
            parameters['right']['noise_estimation_type'] = 0;

        });
        $("#type_1").click(function (){
            $("#type_1").prop("checked", true)
            parameters['left']['noise_estimation_type'] = 1;
            parameters['right']['noise_estimation_type'] = 1;

        });
        $("#type_2").click(function (){
            $("#type_2").prop("checked", true)
            parameters['left']['noise_estimation_type'] = 2;
            parameters['right']['noise_estimation_type'] = 2;

        });
        $("#type_3").click(function (){
            $("#type_3").prop("checked", true)
            parameters['left']['noise_estimation_type'] = 3;
            parameters['right']['noise_estimation_type'] = 3;

        });

        $("#spectral_subtraction_input")
            .click(function(){
                $(this).select();
                console.log(document.getElementById("spectral_subtraction_input").value);
            })
            .blur(function(){
                if(document.getElementById("spectral_subtraction_input").value < 0 || document.getElementById("spectral_subtraction_input").value > 1){
                    alert("Number range should be in (0, 1)");
                    document.getElementById("spectral_subtraction_input").value = parameters['left']['spectral_subtraction'];
                }
                else{
                    parameters['left']['spectral_subtraction'] = +document.getElementById("spectral_subtraction_input").value;
                    parameters['right']['spectral_subtraction'] = +document.getElementById("spectral_subtraction_input").value;
                }
            });



        /**
         * Transmit the current program state by making a POST request on api/params
         *
         */
        function transmit(){
            console.log("Attempting to transmit");
            $.ajax({
                method: 'POST',
                url: '/api/params',
                data: JSON.stringify({
                    user_id: -1,
                    method: "set",
                    request_action: 1,
                    data: this['parameters']
                }),
                success: function(response){
                    console.log(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // console.log(JSON.stringify(jqXHR));
                    // console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    console.log("Parameters were not sent to the MHA. Make sure the software is running and try again.");
                }
            });
        }

        function readMHA(){
            $.ajax({
                method: 'GET',
                url: '/api/getParams',
                success: function(response){
                    console.log("Parameters successfully pulled from MHA");
                    var params = JSON.parse(response);

                    var data = {
                        'left': {
                            'noise_estimation_type': params['left']['noise_estimation_type'],
                            'spectral_type': params['left']['spectral_type'],
                            'spectral_subtraction': params['left']['spectral_subtraction']
                        },
                        'right': {
                            'noise_estimation_type': params['right']['noise_estimation_type'],
                            'spectral_type': params['right']['spectral_type'],
                            'spectral_subtraction': params['right']['spectral_subtraction']
                        }
                    };
                    parameters = data;

                    //set noise estimation type
                    switch(parameters['left']['noise_estimation_type']){
                        case 0:
                            $("#type_0").prop("checked", true);
                            break;
                        case 1:
                            $("#type_1").prop("checked", true);
                            break;
                        case 2:
                            $("#type_2").prop("checked", true);
                            break;
                        case 3:
                            $("#type_3").prop("checked", true);
                            break;
                    }

                    //set spectral subtraction parameters
                    document.getElementById("spectral_subtraction_input").value = parameters['left']['spectral_subtraction'];
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // console.log(JSON.stringify(jqXHR));
                    // console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    alert("Parameters were not passed in through the MHA. Check to make sure it is on and refresh the page.");
                }
            });
        }
    </script>

</body>
</html>
