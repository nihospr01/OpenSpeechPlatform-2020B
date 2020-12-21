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

    <title>Feedback Management</title>

</head>

<body>

    <div class="container" style="margin-top: 10px">

        <nav class="nav nav-pills nav-justified" style="margin-bottom: 10px">
            <a class="nav-item nav-link text-info bg-white" href="{{ url('/researcher/amplification') }}">Amplification</a>
            <a class="nav-item nav-link text-info bg-white" href="{{ url('/researcher/noise') }}">Noise Management</a>
            <a class="nav-item nav-link text-white bg-info" href="#">Feedback Management</a>
        </nav>
        <a href="{{url('/')}}" id="exit" class="btn btn-outline-success" style="margin-bottom: 10px">
            Home
        </a>

        <div class="form-check">
            <input class="form-check-input" type="radio" name="afc_type" id="afc_0" value="0">
            <label class="form-check-label" for="afc_0">
                No Adaptation
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="afc_type" id="afc_1" value="1">
            <label class="form-check-label" for="afc_1">
                FxLMS
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="afc_type" id="afc_2" value="2">
            <label class="form-check-label" for="afc_2">
            IPNLMS
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="afc_type" id="afc_3" value="3" >
            <label class="form-check-label" for="afc_3">
                SLMS
            </label>
        </div>

        <form>
            <div class="form-group">
                    <label for="delay"><strong>delay (ms)</strong>
                    <input type="number" class="form-control" id="delay" value="0">
            </div>
            <div class="form-group">
                <label for="mu"><strong>mu</strong> - The step size parameter, which has to be set to a positive value. It controls the adaptation rate of the AFC filter, in the sense that a larger mu is more suitable for a highlty changin environment. Note, however, a larger mu also leads to a higher possibility of instability and more artifacts.</label>
                <input type="number" class="form-control" id="mu" value="0">
            </div>
            <div class="form-group">
                <label for="rho"><strong>rho</strong> - A forgetting factor for controlling the update of the signal power estimate. It ranges from 0-1. For speech signals, typical values of rho are around 0.9. If it is too large, the update of the power estimate would fail to catch up with speech variatios. If it is set too small, the variance of the estimate will be high, leading to more artifacts. </label>
                <input type="number" class="form-control" id="rho" value="0">
            </div>
    
        </form>

        <div>
          <h5> Troubleshooting 4AFC </h5>
          <p> When instability is detected by the user, he/she should press the "Undo” button below. This will (i) initialize AFC filter to its initial coefficients,  obtained from average of multiple feedback path measurements; and (ii)  reduce amplification gain in all bands by x dB. (Arthur to experiment and suggest value of x. Initially, x=10).</p>
        </div>

        <div class="row align-items-center" style="margin: 1px">
            <div class="col-6">
                <button id="resetButton" class="btn btn-info btn-block">Reset AFC filter</button>
            </div>
            <div class="col-6">
                <button id="transmitButton" class="btn btn-info btn-block">Transmit</button>
            </div>
        </div>

        <div>
          <img style="margin-top: 10px; width: 100%;" src="{{ asset('4afcBlock.png') }}">
        </div>

    </div>

    <script type="text/javascript">
        var parameters = {
            'left': {
                'afc_type': 0,
                'afc_reset': 0,
                'afc_mu': 0,
                'afc_rho': 0,
                'afc_delay':0
            },
            'right': {
                'afc_type': 0,
                'afc_reset': 0,
                'afc_mu': 0,
                'afc_rho': 0,
                'afc_delay':0
            }
        };

        //pull data on loading the page
        readMHA();

        //on clicking reset button, read from MHA
        $("#resetButton").click(function(){
            parameters['left']['afc_reset'] = 1;
            parameters['right']['afc_reset'] = 1;
            transmit();
            readMHA();
            // parameters['left']['afc_reset'] = 0;
            // parameters['right']['afc_reset'] = 0;
        });

        //on clicking transmit button, send data to MHA
        $("#transmitButton").click(function(){
            transmit();
        });

        $("#afc_0").click(function (){
            $("#afc_0").prop("checked", true)
            parameters['left']['afc_type'] = 0;
            parameters['right']['afc_type'] = 0;

        });
        $("#afc_1").click(function (){
            $("#afc_1").prop("checked", true)
            parameters['left']['afc_type'] = 1;
            parameters['right']['afc_type'] = 1;

        });
        $("#afc_2").click(function (){
            $("#afc_2").prop("checked", true)
            parameters['left']['afc_type'] = 2;
            parameters['right']['afc_type'] = 2;

        });
        $("#afc_3").click(function (){
            $("#afc_3").prop("checked", true)
            parameters['left']['afc_type'] = 3;
            parameters['right']['afc_type'] = 3;

        });

        $("#delay")
            .click(function(){
                $(this).select();
            })
            .blur(function(){
                if($(this).val() > 8){
                    alert("Number should be less than 8.0");
                    $(this).val(parameters['left']['afc_delay']);
                }
                else{
                    parameters['left']['afc_delay'] = Number($(this).val());
                    parameters['right']['afc_delay'] = Number($(this).val());
                }

            });

        $("#mu")
            .click(function(){
                $(this).select();
                console.log(document.getElementById("mu").value);
            })
            .blur(function(){
                if(document.getElementById("mu").value < 0){
                    alert("Number should be positive");
                    document.getElementById("mu").value = parameters['left']['afc_mu'];
                }
                else{
                    parameters['left']['afc_mu'] = +document.getElementById("mu").value;
                    parameters['right']['afc_mu'] = +document.getElementById("mu").value;
                }
            });

        $("#rho")
            .click(function(){
                $(this).select();
                console.log(document.getElementById("rho").value);
            })
            .blur(function(){
                if(document.getElementById("rho").value < 0 || document.getElementById("rho").value > 1){
                    alert("Number range should be in (0, 1)");
                    document.getElementById("rho").value = parameters['left']['afc_rho'];
                }
                else{
                    parameters['left']['afc_rho'] = +document.getElementById("rho").value;
                    parameters['right']['afc_rho'] = +document.getElementById("rho").value;
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
                            'afc_type': params['left']['afc_type'],
                            'afc_reset': params['left']['afc_reset'],
                            'afc_mu': params['left']['afc_mu'],
                            'afc_rho': params['left']['afc_rho'],
                            'afc_delay': params['left']['afc_delay']
                            
                        },
                        'right': {
                            'afc_type': params['right']['afc_type'],
                            'afc_reset': params['right']['afc_reset'],
                            'afc_mu': params['right']['afc_mu'],
                            'afc_rho': params['right']['afc_rho'],
                            'afc_delay':params['right']['afc_delay']    
                           
                        }
                    };
                    parameters = data;

                    //set noise estimation type
                    switch(parameters['left']['afc_type']){
                        case 0:
                            $("#afc_0").prop("checked", true);
                            break;
                        case 1:
                            $("#afc_1").prop("checked", true);
                            break;
                        case 2:


                        
                            $("#afc_2").prop("checked", true);
                            break;
                        case 3:
                            $("#afc_3").prop("checked", true);
                            break;
                        default:
                            break;
                    }

                    //set spectral subtraction parameters
                    document.getElementById("mu").value = parameters['left']['afc_mu'];
                    document.getElementById("rho").value = parameters['left']['afc_rho'];
                    $("#delay").val(parameters['left']['afc_delay']);
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
