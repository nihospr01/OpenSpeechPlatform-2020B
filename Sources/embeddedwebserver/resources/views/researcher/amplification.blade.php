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
        .mha-input-field{
            font-size: 1.2rem;
            border-color: white;
            text-align: center;

        }
        .mha-td{
            background-color: #f8f9fa;
            border-color: white;
            border-width: thick
        }
        .col-align {
            display: flex;
            align-items: center;
        }
        .mha-th {
            font-size: 1rem; 
            border-color: white;
            display:flex; 
            align-items:center;
            justify-content: center;
        }
    </style>
    <script type="text/javascript">

        $(document).ready(function() {
            readMHA();

            var parameters = {
                'left': {
                    'compression_ratio': [1, 1, 1, 1, 1, 1],
                    'g50': [0, 0, 0, 0, 0, 0],
                    'g65': [0, 0, 0, 0, 0, 0],
                    'g80': [0, 0, 0, 0, 0, 0],
                    'knee_low': [45, 45, 45, 45, 45, 45],
                    'mpo_band': [120, 120, 120, 120, 120, 120],
                    'attack': [5, 5, 5, 5, 5, 5],
                    'release': [20, 20, 20, 20, 20, 20],
                    'afc': 0, //1 - on, 0 - off
                    'afc_type': 0,
                    'global_mpo': 120,
                    'freping_alpha':[0, 0, 0, 0, 0, 0],
                },
                'right': {
                    'compression_ratio': [1, 1, 1, 1, 1, 1],
                    'g50': [0, 0, 0, 0, 0, 0],
                    'g65': [0, 0, 0, 0, 0, 0],
                    'g80': [0, 0, 0, 0, 0, 0],
                    'knee_low': [45, 45, 45, 45, 45, 45],
                    'mpo_band': [120, 120, 120, 120, 120, 120],
                    'attack': [5, 5, 5, 5, 5, 5],
                    'release': [20, 20, 20, 20, 20, 20],
                    'afc': 0, //1 - on, 0 - off
                    'afc_type': 0,
                    'global_mpo': 120,
                    'freping_alpha':[0, 0, 0, 0, 0, 0]
                },
                'control_via': 1
            };

            var channel = "both";
            var band = 6;

            // updateParamTable(channel);

            function readMHA() {
                $.ajax({
                    method: 'GET',
                    url: '/api/getParams',
                    success: function(response){
                        const params = JSON.parse(response);
                        //update parameters to reflect RTMHA state
                        parameters['left']['g50'] = params['left']['g50'];
                        parameters['left']['g80'] = params['left']['g80'];
                        parameters['left']['knee_low'] = params['left']['knee_low'];
                        parameters['left']['mpo_band'] = params['left']['mpo_band'];
                        parameters['left']['afc'] = params['left']['afc'];
                        parameters['left']['attack'] = params['left']['attack'];
                        parameters['left']['release'] = params['left']['release'];
                        parameters['left']['global_mpo'] = params['left']['global_mpo'];

                        parameters['right']['g50'] = params['right']['g50'];
                        parameters['right']['g80'] = params['right']['g80'];
                        parameters['right']['knee_low'] = params['right']['knee_low'];
                        parameters['right']['mpo_band'] = params['right']['mpo_band'];
                        parameters['right']['afc'] = params['right']['afc'];
                        parameters['right']['attack'] = params['right']['attack'];
                        parameters['right']['release'] = params['right']['release'];
                        parameters['right']['global_mpo'] = params['right']['global_mpo'];
                        
                        if (channel == "both") {
                            updateParamTable("left");
                        }
                        else {
                            updateParamTable(channel);
                        }
                        
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert("Parameters were not passed in through the MHA. Check to make sure it is on and refresh the page.");
                    }
                });
            }

            //pull data on loading the page


            $("#global_mpo").keyup(function(event) {
                parameters[channel]["global_mpo"] = Number(event.target.value);
            })

            $('#6_band_table input[type=number]').keyup(function(event){
                var elem = event.target;
                var param = elem.name

                if(elem.dataset.index != band) {
                    if (channel == "both") {
                        parameters["left"][elem.name][elem.dataset.index] = Number(elem.value);
                        parameters["right"][elem.name][elem.dataset.index] = Number(elem.value);
                    }
                    else {
                        parameters[channel][elem.name][elem.dataset.index] = Number(elem.value);
                    }
                    
                    elem.style.backgroundColor = "#FEEEBA";
                    if(elem.name == "compression_ratio" || elem.name == "g50" || elem.name == "g65" || elem.name == "g80" ){
                        controlParameters(elem.dataset.index);
                    }
                }
                else{
                    //"All" input field
                    var value = Number(elem.value);
                    for (var i = 0; i < band; i++) {
                        var param_id = "";
                        if (param == "knee_low") {
                            param_id = "#"+"kneelow"+"_"+i+"_"+band;
                        }
                        else if (param == "mpo_band") {
                            param_id = "#"+"mpo"+"_"+i+"_"+band;
                        }
                        else if (param == "compression_ratio") {
                            param_id = "#"+"cr"+"_"+i+"_"+band;
                        }
                        else {
                            param_id = "#"+param+"_"+i+"_"+band;
                        }
                        
                        $(param_id).val(value);
                        $(param_id).css("background-color", "#FEEEBA")

                        if (channel == "both") {
                            parameters["left"][elem.name][i] = value;
                            parameters["right"][elem.name][i] = value;
                        }
                        else {
                            parameters[channel][elem.name][i] = value;
                        }
                        if (elem.name == "compression_ratio" || elem.name == "g50" || elem.name == "g65" || elem.name == "g80") {
                            controlParameters(i);
                        }
                    }
                }
            }).click(function(){
                    $(this).select();
            });


            //on clicking reset button, read from MHA
            $("#resetButton").click(function(){
                readMHA();
            });

            //on clicking transmit button, send data to MHA
            $("#transmitButton").click(function(){
                transmit();
            });

            //logic for controlling radio buttons
            $("#control_via_buttons :input").change(function() {
                switch (this.id) {
                    case "g50_g80":
                        parameters['control_via'] = 1;
                        break;
                    case "cr_g65":
                        parameters['control_via'] = 0;
                        break;
                }
            });

            //logic for switching afc
            $("#afc_buttons :input").change(function() {
                switch (this.id) {
                    case "afc_on":
                        console.log("Switching to AFC: on");
                        parameters['left']['afc'] = 1;
                        parameters['right']['afc'] = 1;
                        break;
                    case "afc_off":
                        console.log("Switching to AFC: off");
                        parameters['left']['afc'] = 0;
                        parameters['right']['afc'] = 0;
                        break;
                }
            });

            $("#control_channel :input[type=radio]").change(function() {
                switch (this.id) {
                    case "channel_left":
                        channel = "left"
                        updateParamTable(channel)
                        break;
                    case "channel_right":
                        channel = "right"
                        updateParamTable(channel);
                        break;
                    case "channel_both":
                        channel = "both";
                        updateParamTable("left");
                        break;
                }
            });

            function controlParameters(id) {
                //controlling via CR/G65
                if (parameters['control_via'] === 0) {
                    var cr = parseFloat($("#cr_"+id+"_"+band).val())
                    if (cr != 0) {
                        slope = (1 - cr) / cr;
                        var g65 = parseFloat($("#g65_"+id+"_"+band).val());
                        var g50 = g65 - (slope * 15);
                        var g80 = g65 + (slope * 15);
                        if (channel == "both") {
                            parameters["left"]['g50'][id] = g50;
                            parameters["right"]['g50'][id] = g50;
                            parameters["left"]['g80'][id] = g80;
                            parameters["right"]["g80"][id] = g80;
                        }
                        else {
                            parameters[channel]['g50'][id] = g50;
                            parameters[channel]['g80'][id] = g80;
                        }
                        $("#g50_"+id+"_"+band).val(g50);
                        document.getElementById("g50_" + id+"_"+band).style.backgroundColor = "#FEEEBA";
                        $("#g80_"+id+"_"+band).val(g80)
                        document.getElementById("g80_" + id+"_"+band).style.backgroundColor = "#FEEEBA";
                    }
                }
                //controlling via G50/G80
                else if (parameters['control_via'] === 1) {
                    var g50 = parseFloat($("#g50_"+id+"_"+band).val());
                    var g80 = parseFloat($("#g80_"+id+"_"+band).val());
                    var slope = (g80 - g50)/30;
                    var g65 = g50 + slope * 15;
                    if (channel == "both") {
                        parameters["left"]['g65'][id] = g65;
                        parameters["right"]['g65'][id] = g65;
                    }
                    else {
                        parameters[channel]['g65'][id] = g65;
                    }
                    $("#g65_"+id+"_"+band).val(g65)
                    document.getElementById("g65_" + id+"_"+band).style.backgroundColor = "#FEEEBA";

                    if(slope != -1) {
                        var cr = Math.round( (1 / (1 + slope)) * 100 ) / 100;
                        if (channel == "both") {
                            parameters["left"]['compression_ratio'][id] = cr;
                            parameters["right"]['compression_ratio'][id] = cr;
                        }
                        else {
                            parameters[channel]['compression_ratio'][id] = cr;
                        }
                        $("#cr_"+id+"_"+band).val(cr)
                        document.getElementById("cr_" + id+"_"+band).style.backgroundColor = "#FEEEBA";
                    }
                }
            }

            /**
            * Transmit the current program state by making a POST request on api/params
            *
            */
            function transmit(){
                $.ajax({
                    method: 'POST',
                    url: '/api/params',
                    data: JSON.stringify({
                        user_id: -1,
                        method: "set",
                        request_action: 1,
                        data: {
                            left: {
                                en_ha: 1,
                                afc: parameters['left']['afc'],
                                rear_mics: 0,
                                g50: parameters['left']['g50'],
                                g80: parameters['left']['g80'],
                                knee_low: parameters['left']['knee_low'],
                                mpo_band: parameters['left']['mpo_band'],
                                attack: parameters['left']['attack'],
                                release: parameters['left']['release'],
                                global_mpo: parameters['left']['global_mpo'],
                            },
                            right: {
                                en_ha: 1,
                                afc: parameters['right']['afc'],
                                rear_mics: 0,
                                g50: parameters['right']['g50'],
                                g80: parameters['right']['g80'],
                                knee_low: parameters['right']['knee_low'],
                                mpo_band: parameters['right']['mpo_band'],
                                attack: parameters['right']['attack'],
                                release: parameters['right']['release'],
                                global_mpo: parameters['right']['global_mpo'],
                            }
                        }
                    }),
                    success: function(response){
                        console.log(response);
                        $('input[type=number]').css("background-color", "");
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log("Parameters were not sent to the MHA. Make sure the software is running and try again.");
                    }
                });
            }

            function updateParamTable(channel) {
                for (var i = 0; i < band; i++) {
                    $("#cr_"+i+"_"+band).val(parameters[channel]['compression_ratio'][i]);
                    $("#g50_"+i+"_"+band).val(parameters[channel]['g50'][i]);
                    $("#g65_"+i+"_"+band).val(parameters[channel]['g65'][i]);
                    $("#g80_"+i+"_"+band).val(parameters[channel]['g80'][i]);
                    $("#kneelow_"+i+"_"+band).val(parameters[channel]['knee_low'][i]);
                    $("#mpo_"+i+"_"+band).val(parameters[channel]['mpo_band'][i]);
                    $("#attack_"+i+"_"+band).val(parameters[channel]['attack'][i]);
                    $("#release_"+i+"_"+band).val(parameters[channel]['release'][i]);
                    $("#alpha_"+i+"_"+band).val(parameters[channel]['freping_alpha'][i]);
                    controlParameters(i)
                }

                // Update global mpo
                $('#global_mpo').val(parameters[channel]['global_mpo']);

                // update AFC
                if(parameters['left']['afc'] == 0 || parameters['right']['afc'] == 0){
                    $('#afc_off').closest('.btn').button('toggle');
                    parameters['left']['afc'] = 0;
                    parameters['right']['afc'] = 0;
                }
                else{
                    $('#afc_on').closest('.btn').button('toggle');
                }
                $('input[type=number]').css("background-color", "");
            }
        });
        
    </script>

    <title>Amplification Parameters</title>
</head>
<body>


    <div class="container" style="margin-top: 10px">
        <nav class="nav nav-pills nav-justified" style="margin-bottom: 10px">
            <a class="nav-item nav-link text-white bg-info" href="#">Amplification</a>
            <a class="nav-item nav-link text-info bg-white" href="{{ url('/researcher/noise') }}">Noise Management</a>
            <a class="nav-item nav-link text-info bg-white" href="{{ url('/researcher/feedback') }}">Feedback Management</a>
        </nav>
        <a href="{{url('/')}}" id="exit" class="btn btn-outline-success" style="margin-bottom: 10px">
            Home
        </a>

        <div class="row align-items-center">
            <div class="col-6">
                <div class="card" style="width: 100%">
                        <div class="card-body">
                            <h5 class="card-title">Controls</h5>
                            <div class="row" style="margin-bottom: 10px">
                                <div class="col-12 col-md-4 col-align">
                                    Control Via:
                                </div>
                                <div class="col-12 col-md-8 col-align">
                                    <div id="control_via_buttons" class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-light active">
                                            <input type="radio" name="control_via" id="g50_g80"  autocomplete="off" value=0 checked> G50/G80
                                        </label>
                                        <label class="btn btn-light">
                                            <input type="radio" name="control_via" id="cr_g65"  autocomplete="off" value=1 > CR/G65
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 10px">
                                <div class="col-12 col-md-4 col-align">
                                    AFC:
                                </div>
                                <div class="col-12 col-md-8 col-align">
                                    <div id="afc_buttons" class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-light active">
                                            <input type="radio" name="afc" id="afc_on" autocomplete="off" checked> On
                                        </label>
                                        <label class="btn btn-light">
                                            <input type="radio" name="afc" id="afc_off" autocomplete="off"> Off
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 10px">
                                <div class="col-12 col-md-4 col-align">
                                    Global MPO:
                                </div>
                                <div class="col-12 col-md-8 col-align">
                                    <div class="input-group">
                                        <input style="color: black; font-size: 1.2rem" name="GlobalMPO" id="global_mpo" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0">
                                    </div>                                  
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 10px">
                                <div class="col-12 col-md-4 col-align">
                                    Channel:
                                </div>
                                <div class="col-12 col-md-8 col-align">
                                    <div id="control_channel" class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-light active">
                                            <input type="radio" name="control_channel" id="channel_both" autocomplete="off"> Both 
                                        </label>
                                        <label class="btn btn-light">
                                            <input type="radio" name="control_channel" id="channel_left"  autocomplete="off"> Left
                                        </label>
                                        <label class="btn btn-light">
                                            <input type="radio" name="control_channel" id="channel_right"  autocomplete="off"> Right
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="col-6">
                <div class="row align-items-center" style="margin: 1px">
                    <button id="btnGroupDrop1" type="button" class="btn btn-secondary btn-block dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Read
                    </button>
                </div>

                <div class="row align-items-center" style="margin: 1px">
                    <button type="button" class="btn btn-secondary btn-block">Save</button>
                </div>
                <div class="row align-items-center" style="margin: 1px">
                    <button type="button" class="btn btn-secondary btn-block" data-toggle="modal" data-target="#saveAsModal">Save as</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container" style="margin-top:20px; display:flex; justify-content:center">
        <div class="row">
            <div class="col-12" id="6_band_table">
                    <table class="table table-sm" style="border-color: white" id="6_band_table">
                        <thead>
                            <tr>
                                <th class="text-center" style="font-size: 1rem; border-color: white">Parameter</th>
                                <th class="text-center" style="font-size: 1rem; border-color: white">250</th>
                                <th class="text-center" style="font-size: 1rem; border-color: white">500 </th>
                                <th class="text-center" style="font-size: 1rem; border-color: white">1000</th>
                                <th class="text-center" style="font-size: 1rem; border-color: white">2000</th>
                                <th class="text-center" style="font-size: 1rem; border-color: white">4000</th>
                                <th class="text-center" style="font-size: 1rem; border-color: white">8000</th>
   
                                <th class="text-center" style="font-size: 1rem; color:green; border-color: white">All</th>

                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th class="text-center" style="font-size: 1rem; border-color: white">CR</th>
                                <td class="mha-td table-success"><input name="compression_ratio" data-index="0" id="cr_0_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-success"><input name="compression_ratio" data-index="1" id="cr_1_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-success"><input name="compression_ratio" data-index="2" id="cr_2_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-success"><input name="compression_ratio" data-index="3" id="cr_3_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-success"><input name="compression_ratio" data-index="4" id="cr_4_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-success"><input name="compression_ratio" data-index="5" id="cr_5_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-success"><input name="compression_ratio" data-index="6" id="cr_all_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number"></td>
                            </tr>
                            <tr>
                                <th class="text-center" style="font-size: 1rem; border-color: white">G50</th>
                                <td class="mha-td"><input name="g50" data-index="0" id="g50_0_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="g50" data-index="1" id="g50_1_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="g50" data-index="2" id="g50_2_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="g50" data-index="3" id="g50_3_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="g50" data-index="4" id="g50_4_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="g50" data-index="5" id="g50_5_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="g50" data-index="6" id="g50_all_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                            </tr>
                            <tr>
                                <th class="text-center" style="font-size: 1rem; border-color: white">G65</th>

                                <td class="mha-td table-info"><input name="g65" data-index="0" id="g65_0_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-info"><input name="g65" data-index="1" id="g65_1_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-info"><input name="g65" data-index="2" id="g65_2_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-info"><input name="g65" data-index="3" id="g65_3_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-info"><input name="g65" data-index="4" id="g65_4_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-info"><input name="g65" data-index="5" id="g65_5_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-info"><input name="g65" data-index="6" id="g65_all_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                            </tr>
                            <tr>
                                <th class="text-center" style="font-size: 1rem; border-color: white">G80</th>

                                <td class="mha-td"><input name="g80" data-index="0" id="g80_0_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="g80" data-index="1" id="g80_1_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="g80" data-index="2" id="g80_2_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="g80" data-index="3" id="g80_3_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="g80" data-index="4" id="g80_4_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="g80" data-index="5" id="g80_5_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="g80" data-index="6" id="g80_all_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                            </tr>
                            <tr>                       
                                <th class="text-center" style="font-size: 1rem; border-color: white">Knee</th>

                                <td class="mha-td table-info"><input name="knee_low" data-index="0" id="kneelow_0_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-info"><input name="knee_low" data-index="1" id="kneelow_1_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-info"><input name="knee_low" data-index="2" id="kneelow_2_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-info"><input name="knee_low" data-index="3" id="kneelow_3_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-info"><input name="knee_low" data-index="4" id="kneelow_4_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-info"><input name="knee_low" data-index="5" id="kneelow_5_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-info"><input name="knee_low" data-index="6" id="kneelow_all_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                            </tr>
                            <tr>
                                <th class="text-center" style="font-size: 1rem; border-color: white">MPO</th>

                                <td class="mha-td"><input name="mpo_band" data-index="0" id="mpo_0_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="mpo_band" data-index="1" id="mpo_1_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="mpo_band" data-index="2" id="mpo_2_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="mpo_band" data-index="3" id="mpo_3_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="mpo_band" data-index="4" id="mpo_4_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="mpo_band" data-index="5" id="mpo_5_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="mpo_band" data-index="6" id="mpo_all_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                            </tr>
                            <tr>
                                <th class="text-center" style="font-size: 1rem; border-color: white">Attack</th>

                                <td class="mha-td table-info"><input name="attack" data-index="0" id="attack_0_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-info"><input name="attack" data-index="1" id="attack_1_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-info"><input name="attack" data-index="2" id="attack_2_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-info"><input name="attack" data-index="3" id="attack_3_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-info"><input name="attack" data-index="4" id="attack_4_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-info"><input name="attack" data-index="5" id="attack_5_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td table-info"><input name="attack" data-index="6" id="attack_all_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
            
                            </tr>
                            <tr>
                                <th class="text-center" style="font-size: 1rem; border-color: white">Release</th>
                                <td class="mha-td"><input name="release" data-index="0" id="release_0_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="release" data-index="1" id="release_1_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="release" data-index="2" id="release_2_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="release" data-index="3" id="release_3_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="release" data-index="4" id="release_4_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="release" data-index="5" id="release_5_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="release" data-index="6" id="release_all_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                            </tr>
                            <tr>
                                <th class="text-center" style="font-size: 1rem; border-color: white">Alpha</th>
                                <td class="mha-td"><input name="alpha" data-index="0" id="alpha_0_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="alpha" data-index="1" id="alpha_1_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="alpha" data-index="2" id="alpha_2_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="alpha" data-index="3" id="alpha_3_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="alpha" data-index="4" id="alpha_4_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="alpha" data-index="5" id="alpha_5_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                                <td class="mha-td"><input name="alpha" data-index="6" id="alpha_all_6" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" ></td>
                            </tr>
                        </tbody>
                    </table>
            </div>
        </div>
    </div>

    <div class="container" style="margin-top: 10px">
        <div class="row align-items-center" style="margin: 1px">
            <div class="col-6">
                <button id="resetButton" class="btn btn-info btn-block">Undo</button>
            </div>
            <div class="col-6">
                <button id="transmitButton" class="btn btn-info btn-block">Transmit</button>
            </div>
        </div>
    </div>
</body>
</html>
