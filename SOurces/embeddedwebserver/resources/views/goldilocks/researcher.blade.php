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

    </style>


    <title>Goldilocks Researcher</title>
</head>

<body>

    <div class="container">
        <div class="row align-items-center" style="margin-bottom: 10px">
            <div class="col-3">
                <div class="row align-items-center">
                    <h5 id="listener_text">Listener ID: {{$listener->listener}}</h5>
                </div>
                <div class="row align-items-center">
                    <h5>Tester ID: {{$researcher->researcher}}</h5>
                </div>
                <div class="row align-items-center">
                    <h5>Listener PIN: {{$listener->pin}}</h5>
                </div>
            </div>
            <div class="col-2 ">
                <div class="row align-items-center">
                    Channel:
                </div>
                <div class="row align-items-center">
                    <div id="control_channel" class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-light active">
                            <input type="radio" name="control_channel" id="channel_left" autocomplete="off"> Left
                        </label>
                        <label class="btn btn-light">
                            <input type="radio" name="control_channel" id="channel_right" autocomplete="off"> Right
                        </label>
                        <label class="btn btn-light">
                            <input type="radio" name="control_channel" id="channel_both" autocomplete="off"> Both
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-2">
                <div class="row align-items-center">
                    Control via:
                </div>
                <div class="row align-items-center">
                    <div id="control_via_buttons" class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-light active">
                            <input type="radio" name="control_via" id="cr_g65" autocomplete="off" checked> CR/G65
                        </label>
                        <label class="btn btn-light">
                            <input type="radio" name="control_via" id="g50_g80" autocomplete="off"> G50/G80
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-2 ">
                <div class="row align-items-center">
                    AFC:
                </div>
                <div class="row align-items-center">
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
            <div class="col-3">
                <div class="row palign-items-center" style="margin: 1px">
                    <button id="btnGroupDrop1" type="button" class="btn btn-info btn-block dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Read
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        @foreach($programs as $program)
                        <a id="program_{{$program->id}}" name="{{$program->name}}" class="dropdown-item" href="#" onclick="changeProgram( {{$program->id}} , this.name )">{{$program->name}}</a>
                        @endforeach

                        @if(count($programs) == 0)
                        <a class="dropdown-item" href="#">No programs to display</a>
                        @endif
                    </div>
                </div>

                <div class="row align-items-center" style="margin: 1px">
                    <button type="button" class="btn btn-info btn-block" onclick="save()">Save</button>
                </div>
                <div class="row align-items-center" style="margin: 1px">
                    <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#saveAsModal">Save-as</button>
                </div>

            </div>
        </div>



        <div class="row" id="param_table" style="background-color: #b0dfe5;">
            <div class="col-12">
                <table class="table table-striped table-bordered table-sm" style="border-color: white;border-collapse:collapse" ">
                    <thead>
                        <th class="text-center" style="font-size: 2.5rem; border-color: white"></th>
                        <th class="center-text" style="font-size: 2rem; border-color: white">0250</th>
                        <th class="center-text" style="font-size: 2rem; border-color: white">0500 </th>
                        <th class="center-text" style="font-size: 2rem; border-color: white">1000</th>
                        <th class="center-text" style="font-size: 2rem; border-color: white">2000</th>
                        <th class="center-text" style="font-size: 2rem; border-color: white">4000</th>
                        <th class="center-text" style="font-size: 2rem; border-color: white">8000</th>
                        <th class="center-text" style="font-size: 2rem; color:green; border-color: white">All</th>
                    </thead>

                    <tbody>
                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">CR</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="compression_ratio" data-index="0" id="cr_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="1.4"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="compression_ratio" data-index="1" id="cr_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="1.4"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="compression_ratio" data-index="2" id="cr_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="1.4"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="compression_ratio" data-index="3" id="cr_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="1.4"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="compression_ratio" data-index="4" id="cr_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="1.4"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="compression_ratio" data-index="5" id="cr_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="1.4"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="compression_ratio" data-index="6" id="cr_all" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">G50</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50" data-index="0" id="g50_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="19.3"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50" data-index="1" id="g50_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="7.3"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50" data-index="2" id="g50_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="9.3"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50" data-index="3" id="g50_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="22.3"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50" data-index="4" id="g50_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="23.3"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50" data-index="5" id="g50_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="11.3"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50" data-index="6" id="g50_all" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">G65</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g65" data-index="0" id="g65_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="15"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g65" data-index="1" id="g65_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="3"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g65" data-index="2" id="g65_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="5"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g65" data-index="3" id="g65_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="18"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g65" data-index="4" id="g65_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="19"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g65" data-index="5" id="g65_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="7"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g65" data-index="6" id="g65_all" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">G80</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g80" data-index="0" id="g80_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="10.7"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g80" data-index="1" id="g80_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="-1.3"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g80" data-index="2" id="g80_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="0.7"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g80" data-index="3" id="g80_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="13.7"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g80" data-index="4" id="g80_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="14.7"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g80" data-index="5" id="g80_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="2.7"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g80" data-index="6" id="g80_all" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">Knee</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="knee_low" data-index="0" id="kneelow_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="45"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="knee_low" data-index="1" id="kneelow_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="45"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="knee_low" data-index="2" id="kneelow_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="45"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="knee_low" data-index="3" id="kneelow_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="45"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="knee_low" data-index="4" id="kneelow_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="45"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="knee_low" data-index="5" id="kneelow_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="45"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="knee_low" data-index="6" id="kneelow_all" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>

                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">MPO</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="mpo_band" data-index="0" id="mpo_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="110"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="mpo_band" data-index="1" id="mpo_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="110"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="mpo_band" data-index="2" id="mpo_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="110"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="mpo_band" data-index="3" id="mpo_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="110"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="mpo_band" data-index="4" id="mpo_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="110"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="mpo_band" data-index="5" id="mpo_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="110"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="mpo_band" data-index="6" id="mpo_all" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>

                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">Attack</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="attack" data-index="0" id="attack_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="5"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="attack" data-index="1" id="attack_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="5"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="attack" data-index="2" id="attack_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="5"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="attack" data-index="3" id="attack_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="5"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="attack" data-index="4" id="attack_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="5"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="attack" data-index="5" id="attack_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="5"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="attack" data-index="6" id="attack_all" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>

                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">Release</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="release" data-index="0" id="release_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="20"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="release" data-index="1" id="release_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="20"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="release" data-index="2" id="release_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="20"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="release" data-index="3" id="release_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="20"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="release" data-index="4" id="release_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="20"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="release" data-index="5" id="release_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="20"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="release" data-index="6" id="release_all" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1.3rem; border-color: white">G50 Max</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50_max" data-index="0" id="g50_max_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="35"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50_max" data-index="1" id="g50_max_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="35"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50_max" data-index="2" id="g50_max_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="35"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50_max" data-index="3" id="g50_max_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="35"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50_max" data-index="4" id="g50_max_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="35"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50_max" data-index="5" id="g50_max_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="35"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50_max" data-index="6" id="g50_max_all" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">Targets</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="targets" data-index="0" id="targets_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="59"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="targets" data-index="1" id="targets_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="61"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="targets" data-index="2" id="targets_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="62"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="targets" data-index="3" id="targets_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="73"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="targets" data-index="4" id="targets_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="75"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="targets" data-index="5" id="targets_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="64"></td>
                            <td>
                                <button type="button" class="btn btn-info btn-block" onclick="calcg65()">Set</button>
                            </td>

                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">LTASS</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="ltass" data-index="0" id="ltass_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="44"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="ltass" data-index="1" id="ltass_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="58"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="ltass" data-index="2" id="ltass_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="57"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="ltass" data-index="3" id="ltass_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="55"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="ltass" data-index="4" id="ltass_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="56"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="ltass" data-index="5" id="ltass_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="57"></td>
                            <td>
                                <button type="button" class="btn btn-info btn-block" onclick="monitorValues()">Monitor</button>
                            </td>

                        </tr>
                        <tr style="border-bottom:3px solid black">
                            <th class="text-center" style="font-size: 1.5rem; border-color:white">Thresh</th>
                            <td ><input style="color: black; font-size: 1.2rem" name="hearing_loss" data-index="0" id="hl_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="25"></td>
                            <td ><input style="color: black; font-size: 1.2rem" name="hearing_loss" data-index="1" id="hl_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="30"></td>
                            <td ><input style="color: black; font-size: 1.2rem" name="hearing_loss" data-index="2" id="hl_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="35"></td>
                            <td ><input style="color: black; font-size: 1.2rem" name="hearing_loss" data-index="3" id="hl_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="45"></td>
                            <td ><input style="color: black; font-size: 1.2rem" name="hearing_loss" data-index="4" id="hl_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="55"></td>
                            <td ><input style="color: black; font-size: 1.2rem" name="hearing_loss" data-index="5" id="hl_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="85"></td>
                        </tr>

                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">L Mult</th>
                            <td ><input style="color: black; font-size: 1.2rem" name="multiplier_l" data-index="0" id="multiL_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="0"></td>
                            <td ><input style="color: black; font-size: 1.2rem" name="multiplier_l" data-index="1" id="multiL_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="3"></td>
                            <td ><input style="color: black; font-size: 1.2rem" name="multiplier_l" data-index="2" id="multiL_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="3"></td>
                            <td ><input style="color: black; font-size: 1.2rem" name="multiplier_l" data-index="3" id="multiL_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="0"></td>
                            <td ><input style="color: black; font-size: 1.2rem" name="multiplier_l" data-index="4" id="multiL_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="0"></td>
                            <td ><input style="color: black; font-size: 1.2rem" name="multiplier_l" data-index="5" id="multiL_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="0"></td>
                        </tr>

                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">H Mult</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="multiplier_h" data-index="0" id="multiH_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="0"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="multiplier_h" data-index="1" id="multiH_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="0"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="multiplier_h" data-index="2" id="multiH_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="0"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="multiplier_h" data-index="3" id="multiH_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="3"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="multiplier_h" data-index="4" id="multiH_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="3"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="multiplier_h" data-index="5" id="multiH_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="3"></td>
                        </tr>

                        <tr>
                            <th class="text-center" style="font-size: 1.3rem; border-color: white">Atten.</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="gain" id="gain" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="-20" onchange="gainChanged()"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="row">
            <div class="col-xs-9 col-sm-6">
                <table class="table table-striped table-bordered table-sm" style="border-color: white; ">
                    <thead>
                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white"></th>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">L</th>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">V</th>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">H</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td class="text-center" style="font-size: 2.0rem; border-color: white">Step</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="lvh_params" id="stepL" class="form-control form-control-sm table-field font-weight-bold" type="number" value="1" onchange="stepL()"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="lvh_params" id="stepV" class="form-control form-control-sm table-field font-weight-bold" type="number" value="3" onchange="stepV()"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="lvh_params" id="stepH" class="form-control form-control-sm table-field font-weight-bold" type="number" value="1" onchange="stepH()"></td>
                        </tr>
                        <tr>
                            <td class="text-center" style="font-size: 2.0rem; border-color: white">Min</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="lvh_params" id="minL" class="form-control form-control-sm table-field font-weight-bold" type="number" value="-40" onchange="minL()"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="lvh_params" id="minV" class="form-control form-control-sm table-field font-weight-bold" type="number" value="-40" onchange="minV()"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="lvh_params" id="minH" class="form-control form-control-sm table-field font-weight-bold" type="number" value="-40" onchange="minH()"></td>
                        </tr>
                        <tr>
                            <td class="text-center" style="font-size: 2.0rem; border-color: white">Max</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="lvh_params" id="maxL" class="form-control form-control-sm table-field font-weight-bold" type="number" value="40" onchange="maxL()"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="lvh_params" id="maxV" class="form-control form-control-sm table-field font-weight-bold" type="number" value="40" onchange="maxV()"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="lvh_params" id="maxH" class="form-control form-control-sm table-field font-weight-bold" type="number" value="40" onchange="maxH()"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="col-6" style="margin-top: 10px">
                <div class="row">
                    <h5 style="font-size: 2.0rem">Adjustments</h5>
                </div>

                <div class="row" style="margin-bottom: 15px">
                    <div class="col">
                        Num:
                        <div id="sequence_num_buttons" class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-light active" style="font-size: 1.5em">
                                <input type="radio" name="sequence_num" id="num_3" autocomplete="off" checked> 3
                            </label>
                            <label class="btn btn-light" style="font-size: 1.5em">
                                <input type="radio" name="sequence_num" id="num_2" autocomplete="off"> 2
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-bottom: 15px">
                    <div class="col">
                        First:
                        <div id="sequence_order_buttons" class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-light active" style="font-size: 1.5em">
                                <input type="radio" name="sequence_first" id="seq_v" autocomplete="off" checked> V
                            </label>
                            <label class="btn btn-light" style="font-size: 1.5em">
                                <input type="radio" name="sequence_first" id="seq_h" autocomplete="off"> H
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-bottom: 15px">
                    <div class="col">
                        Seq:
                        <div id="app_behavior_buttons" class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-light active" style="font-size: 1.5em">
                                <input type="radio" name="app_behavior" id="one_by_one" autocomplete="off" checked> Sequence
                            </label>
                            <label class="btn btn-light" style="font-size: 1.5em">
                                <input type="radio" name="app_behavior" id="volume_only" autocomplete="off"> Volume only
                            </label>
                            <label class="btn btn-light" style="font-size: 1.5em">
                                <input type="radio" name="app_behavior" id="all_3" autocomplete="off"> Compact
                            </label>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!--Modal-->
        <div class="modal fade" id="saveAsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Save New Program</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <label for="modalInput" style="padding-right: 5px">Program name </label>
                        <input id="modalInput" name="programName" type="text" placeholder="Quiet, Noisy, etc.">
                        <p style="color: red" id="modalInputError"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="saveAs()">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <br>

        <form method="post" onsubmit="return fillDataField()">
            {{ Form::token() }}
            <input type="hidden" id="dataField" name="data" value="{}">


            <div class="row" style="margin-bottom: 25px">
                <div class="col">
                    <button type="button" class="btn btn-info btn-block" onclick="transmit()">Transmit</button>
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-info btn-block">Continue</button>
                </div>
            </div>
        </form>

    </div>




    <script language="JavaScript">
        //store listener ID as a JS variable
        var listener = document.getElementById("listener_text").innerHTML;

        //store programs passed through php
        var programs = JSON.parse("{{$parameters}}".replace(/&quot;/g, '"'));
        console.log(programs);

        var genericProgram = JSON.parse("{{$genericProgram->parameters}}".replace(/&quot;/g, '"'));

        //set currently selected program to -1
        var selectedProgram = -1;

        //parameters to be refered to throughout processes
        var channels = ["left"];
        var twoChannelParams = ['targets', 'ltass', 'hearing_loss', 'compression_ratio', 'g50', 'g65', 'g80', 'g50_max', 'knee_low', 'mpo_band', 'attack', 'release'];
        var parametersTwoChannels = {
            left: {
                'targets': [59, 61, 62, 73, 75, 64],
                'ltass': [44, 58, 57, 55, 56, 57],
                'hearing_loss': [25, 30, 35, 45, 55, 85],
                'compression_ratio': [1.4, 1.4, 1.4, 1.4, 1.4, 1.4],
                'g50': [19.3, 7.3, 9.3, 22.3, 23.3, 11.3],
                'g65': [15, 3, 5, 18, 19, 7],
                'g80': [10.7, -1.3, 0.7, 13.7, 14.7, 2.7],
                'g50_max': [35, 35, 35, 35, 35, 35],
                'knee_low': [45, 45, 45, 45, 45, 45],
                'mpo_band': [110, 110, 110, 110, 110, 110],
                'attack': [5, 5, 5, 5, 5, 5],
                'release': [20, 20, 20, 20, 20, 20],
            },
            right: {
                'targets': [59, 61, 62, 73, 75, 64],
                'ltass': [44, 58, 57, 55, 56, 57],
                'hearing_loss': [25, 30, 35, 45, 55, 85],
                'compression_ratio': [1.4, 1.4, 1.4, 1.4, 1.4, 1.4],
                'g50': [19.3, 7.3, 9.3, 22.3, 23.3, 11.3],
                'g65': [15, 3, 5, 18, 19, 7],
                'g80': [10.7, -1.3, 0.7, 13.7, 14.7, 2.7],
                'g50_max': [35, 35, 35, 35, 35, 35],
                'knee_low': [45, 45, 45, 45, 45, 45],
                'mpo_band': [110, 110, 110, 110, 110, 110],
                'attack': [5, 5, 5, 5, 5, 5],
                'release': [20, 20, 20, 20, 20, 20],
            }
        }
        var parameters = {
            'multiplier_l': [3, 3, 0, 0, 0, 0],
            'multiplier_h': [0, 0, 0, 3, 3, 3],
            'gain': -20,
            'l_min': -40,
            'l_max': 40,
            'l_step': 1,
            'v_min': -40,
            'v_max': 40,
            'v_step': 3,
            'h_min': -40,
            'h_max': 40,
            'h_step': 1,
            'control_via': 0, //0 - CR/G65, 1 - G50/G80
            'afc': 1, //1 - on, 0 - off
            'sequence_num': 3,
            'sequence_order': 0, //0 - V, 1- H
            'app_behavior': 0 //0- one by one, 1 - volume only, 2 - all 3
        };

        prefillParameters();
        //start with G50/G80 deactivated
        deactivateG50G80();

        //load in program
        if ("{{$listener->current_program_id}}" !== "") {
            selectedProgram = Number("{{$listener->current_program_id}}");
            console.log(selectedProgram);
            changeProgram(selectedProgram, document.getElementById("program_" + selectedProgram).name);
        }

        //logic for changing the channel
        $("#control_channel :input[type=radio]").change(function() {
            switch (this.id) {
                case "channel_left":
                    channels = ["left"];
                    document.getElementById('param_table').style['background-color']="#b0dfe5";
                    break;
                case "channel_right":
                    channels = ["right"];
                    document.getElementById('param_table').style['background-color']="#ffa6a6";
                    break;
                case "channel_both":
                    channels = ["left", "right"];
                    document.getElementById('param_table').style['background-color']='#91f2ad';
                    break;
            }
            console.log('Switch channel to ' + channels);
            updateParameters();
            // updateCalibratedParameters();
            //  updateParamTable(channel);
            //  updateAllCorrections();

        });


        //logic to trigger Save As modal input
        $('#saveAsModal').on('shown.bs.modal', function() {
            $('#modalInput').trigger('focus')
        });

        //logic for controlling radio buttons
        $("#control_via_buttons :input").change(function() {
            switch (this.id) {
                case "g50_g80":
                    console.log("Controlling via g50/g80");
                    parameters['control_via'] = 1;
                    for (i = 0; i < 6; i++) {
                        controlParameters(i);
                    }
                    deactivateCRG65();
                    break;
                case "cr_g65":
                    console.log("Controlling via CR/g65");
                    parameters['control_via'] = 0;
                    for (i = 0; i < 6; i++) {
                        controlParameters(i);
                    }
                    deactivateG50G80();
                    break;
            }
        });

        //logic for switching afc
        $("#afc_buttons :input").change(function() {
            switch (this.id) {
                case "afc_on":
                    console.log("Switching to AFC: on");
                    parameters['afc'] = 1;
                    break;
                case "afc_off":
                    console.log("Switching to AFC: off");
                    parameters['afc'] = 0;
                    break;
            }
        });

        //logic for switching sequence number
        $("#sequence_num_buttons :input").change(function() {
            switch (this.id) {
                case "num_2":
                    console.log("Switching to sequence with 2 parameters");
                    parameters['sequence_num'] = 2;
                    break;
                case "num_3":
                    console.log("Switching to sequence with 3 parameters");
                    parameters['sequence_num'] = 3;
                    break;
            }
        });

        //logic for changing sequence order
        $("#sequence_order_buttons :input").change(function() {
            switch (this.id) {
                case "seq_v":
                    console.log("Switching to sequence V");
                    parameters['sequence_order'] = 0;
                    break;
                case "seq_h":
                    console.log("Switching to sequence H");
                    parameters['sequence_order'] = 1;
                    break;
            }
        });

        //logic for changing app behavior for listener
        $("#app_behavior_buttons :input").change(function() {
            switch (this.id) {
                case "one_by_one":
                    console.log("Switching to one by one");
                    parameters['app_behavior'] = 0;
                    break;
                case "volume_only":
                    console.log("Switching to volume only");
                    parameters['app_behavior'] = 1;
                    break;
                case "all_3":
                    console.log("Switching to all 3");
                    parameters['app_behavior'] = 2;
                    break;
            }
        });

        //input field control
        $('input[type=number]').keyup(function(event) {
            var elem = event.target;
            var value = Number(elem.value);
            value = fixPrecision(value) // round to 1 d.p.
            if (elem.name != "lvh_params" && elem.name != "gain") {
                if (elem.dataset.index != 6) {
                    console.log(elem.name, elem.dataset.index, value);
                    
                    // elem.style.backgroundColor = "#FEEEBA";
                    if (twoChannelParams.includes(elem.name)) {
                        for(i = 0; i< channels.length; i++){
                            parametersTwoChannels[channels[i]][elem.name][elem.dataset.index] = value;
                        }
                    } else {
                        parameters[elem.name][elem.dataset.index] = value;
                    }
                    if (elem.name == "compression_ratio" || elem.name == "g50" || elem.name == "g65" || elem.name == "g80") {
                        controlParameters(elem.dataset.index);
                    }

                    
                } else {
                    //"All" input field
                    var currentElemArray = document.getElementsByName(elem.name);
                    for (var i = 0; i < 6; i++) {
                        currentElemArray[i].value = value;
                        // currentElemArray[i].style.backgroundColor = "#FEEEBA";
                        if (twoChannelParams.includes(elem.name)) {
                            for(var j = 0; j< channels.length; j++){
                                parametersTwoChannels[channels[j]][elem.name][i] = value;
                            }
                        } else {
                            parameters[elem.name][i] = value;
                        }
                        if (elem.name == "compression_ratio" || elem.name == "g50" || elem.name == "g65" || elem.name == "g80") {
                            controlParameters(i);
                        }
                    }
                }
            }
        }).click(function() {
            $(this).select();
        });


        // set up with generic parameters
        function prefillParameters() {
            //this['parameters'] = JSON.parse(JSON.stringify(genericProgram));
            var generic = JSON.parse(JSON.stringify(genericProgram));
            Object.keys(parameters).forEach((key, index) => {
                this['parameters'][key] = generic[key];
            })
            Object.keys(parametersTwoChannels['left']).forEach((key, index) => {
                this['parametersTwoChannels']['left'][key] = generic[key];
            })
            Object.keys(parametersTwoChannels['right']).forEach((key, index) => {
                this['parametersTwoChannels']['right'][key] = generic[key];
            })

            // set the right toggles
            //control via
            if (this['parameters']['control_via'] == 0) {
                $('#cr_g65').closest('.btn').button('toggle');
                deactivateG50G80();
            } else {
                $('#g50_g80').closest('.btn').button('toggle');
                deactivateCRG65();
            }

            //afc
            if (this['parameters']['afc'] == 0) {
                $('#afc_off').closest('.btn').button('toggle');
            } else {
                $('#afc_on').closest('.btn').button('toggle');
            }

            //sequence_num
            if (this['parameters']['sequence_num'] == 3) {
                $('#num_3').closest('.btn').button('toggle');
            } else {
                $('#num_2').closest('.btn').button('toggle');
            }

            //sequence_order
            if (this['parameters']['sequence_order'] == 0) {
                $('#seq_v').closest('.btn').button('toggle');
            } else {
                $('#seq_h').closest('.btn').button('toggle');
            }

            //sequence_order
            if (this['parameters']['app_behavior'] == 0) {
                $('#one_by_one').closest('.btn').button('toggle');
            } else if (this['parameters']['app_behavior'] == 1) {
                $('#volume_only').closest('.btn').button('toggle');
            } else {
                $('#all_3').closest('.btn').button('toggle');
            }

            // fill in input fields
            // for (var i = 0; i < 6; i++) {
            //     document.getElementById("targets_" + i).value = parametersTwoChannels[channel]['targets'][i];
            //     document.getElementById("ltass_" + i).value = parametersTwoChannels[channel]['ltass'][i];
            //     document.getElementById("hl_" + i).value = parametersTwoChannels[channel]['hearing_loss'][i];
            //     document.getElementById("cr_" + i).value = parametersTwoChannels[channel]['compression_ratio'][i];
            //     document.getElementById("g50_" + i).value = parametersTwoChannels[channel]['g50'][i];
            //     document.getElementById("g65_" + i).value = parametersTwoChannels[channel]['g65'][i];
            //     document.getElementById("g80_" + i).value = parametersTwoChannels[channel]['g80'][i];
            //     document.getElementById("g50_max_" + i).value = parametersTwoChannels[channel]['g50_max'][i];
            //     document.getElementById("kneelow_" + i).value = parametersTwoChannels[channel]['knee_low'][i];
            //     document.getElementById("mpo_" + i).value = parametersTwoChannels[channel]['mpo_band'][i];
            //     document.getElementById("attack_" + i).value = parametersTwoChannels[channel]['attack'][i];
            //     document.getElementById("release_" + i).value = parametersTwoChannels[channel]['release'][i];
            //     document.getElementById("multiL_" + i).value = parameters['multiplier_l'][i];
            //     document.getElementById("multiH_" + i).value = parameters['multiplier_h'][i];
            // }
            updateParameters();
            document.getElementById("gain").value = parameters['gain'];
        }

        // update input value after switching channel
        function updateParameters() {
            for (var i = 0; i < 6; i++) {
                // use channels[0] to update 
                document.getElementById("targets_" + i).value = fixPrecision(parametersTwoChannels[channels[0]]['targets'][i]);
                document.getElementById("ltass_" + i).value = fixPrecision(parametersTwoChannels[channels[0]]['ltass'][i]);
                document.getElementById("hl_" + i).value = fixPrecision(parametersTwoChannels[channels[0]]['hearing_loss'][i]);
                document.getElementById("cr_" + i).value = fixPrecision(parametersTwoChannels[channels[0]]['compression_ratio'][i]);
                document.getElementById("g50_" + i).value = fixPrecision(parametersTwoChannels[channels[0]]['g50'][i]);
                document.getElementById("g65_" + i).value = fixPrecision(parametersTwoChannels[channels[0]]['g65'][i]);
                document.getElementById("g80_" + i).value = fixPrecision(parametersTwoChannels[channels[0]]['g80'][i]);
                document.getElementById("g50_max_" + i).value = fixPrecision(parametersTwoChannels[channels[0]]['g50_max'][i]);
                document.getElementById("kneelow_" + i).value = fixPrecision(parametersTwoChannels[channels[0]]['knee_low'][i]);
                document.getElementById("mpo_" + i).value = fixPrecision(parametersTwoChannels[channels[0]]['mpo_band'][i]);
                document.getElementById("attack_" + i).value = fixPrecision(parametersTwoChannels[channels[0]]['attack'][i]);
                document.getElementById("release_" + i).value = fixPrecision(parametersTwoChannels[channels[0]]['release'][i]);
                document.getElementById("multiL_" + i).value = fixPrecision(parameters['multiplier_l'][i]);
                document.getElementById("multiH_" + i).value = fixPrecision(parameters['multiplier_h'][i]);
                
            }
            document.getElementById("cr_all").value = "";
            document.getElementById("g50_all").value = "";
            document.getElementById("g65_all").value = "";
            document.getElementById("g80_all").value = "";
            document.getElementById("kneelow_all").value = "";
            document.getElementById("mpo_all").value = "";
            document.getElementById("attack_all").value = "";
            document.getElementById("release_all").value = "";
            document.getElementById("g50_max_all").value = "";
        }

        function fixPrecision(value){
            return  Math.round(value * 10) / 10;
        }


        function controlParameters(id) {
            //controlling via CR/G65
            if (parameters['control_via'] == 0) {
                var cr = parseFloat(document.getElementById("cr_" + id).value);
                if (cr != 0) {
                    slope = (1 - cr) / cr;
                    var g65 = parseFloat(document.getElementById("g65_" + id).value);
                    var g50 = g65 - (slope * 15);
                    var g80 = g65 + (slope * 15);
                    g50 = fixPrecision(g50);
                    g80 = fixPrecision(g80);

                    // parameters['g50'][id] = g50;
                    // parameters['g80'][id] = g80;
                    for(var i = 0; i < channels.length; i++){
                        parametersTwoChannels[channels[i]]['g50'][id] = g50;
                        parametersTwoChannels[channels[i]]['g80'][id] = g80;
                    }
                    document.getElementById("g50_" + id).value = g50;
                    // document.getElementById("g50_" + id).style.backgroundColor = "#FEEEBA";
                    document.getElementById("g80_" + id).value = g80;
                    // document.getElementById("g80_" + id).style.backgroundColor = "#FEEEBA";

                }
            }
            //controlling via G50/G80
            else if (parameters['control_via'] == 1) {
                var g50 = parseFloat(document.getElementById("g50_" + id).value);
                var g80 = parseFloat(document.getElementById("g80_" + id).value);
                var slope = (g80 - g50) / 30;
                var g65 = g50 + slope * 15;
                g65 = fixPrecision(g65);

                // parameters['g65'][id] = g65;
                for(var i = 0; i < channels.length; i++){
                    parametersTwoChannels[channels[i]]['g65'][id] = g65;
                }
                document.getElementById("g65_" + id).value = g65;
                // document.getElementById("g65_" + id).style.backgroundColor = "#FEEEBA";

                if (slope != -1) {
                    var cr = Math.round((1 / (1 + slope)) * 100) / 100;
                    //parameters['compression_ratio'][id] = cr;
                    for(var i = 0; i < channels.length; i++){
                        parametersTwoChannels[channels[i]]['compression_ratio'][id] = cr;
                    }
                    document.getElementById("cr_" + id).value = cr;
                    // document.getElementById("cr_" + id).style.backgroundColor = "#FEEEBA";
                }
            }
        }

        /**
         * Deactivates compression ratio and G65 input fields. Used when switching to G50/G80 control
         */
        function deactivateCRG65() {
            for (i = 0; i < 7; i++) {
                if (i !== 6) {
                    var cr_elem = document.getElementById("cr_" + i);
                    var g65_elem = document.getElementById("g65_" + i);
                    var g50_elem = document.getElementById("g50_" + i);
                    var g80_elem = document.getElementById("g80_" + i);
                } else {
                    var cr_elem = document.getElementById("cr_all");
                    var g65_elem = document.getElementById("g65_all");
                    var g50_elem = document.getElementById("g50_all");
                    var g80_elem = document.getElementById("g80_all");
                }

                cr_elem.style.backgroundColor = "#D3D3D3";
                cr_elem.disabled = true;

                g65_elem.style.backgroundColor = "#D3D3D3";
                g65_elem.disabled = true;

                //reactivate g50 g80
                g50_elem.style.backgroundColor = "";
                g50_elem.disabled = false;
                g80_elem.style.backgroundColor = "";
                g80_elem.disabled = false;

            }
        }


        /**
         * Deactivates G50 and G80 input fields. Used when switching to compression ratio/G65 control
         */
        function deactivateG50G80() {
            for (i = 0; i < 7; i++) {
                if (i !== 6) {
                    var cr_elem = document.getElementById("cr_" + i);
                    var g65_elem = document.getElementById("g65_" + i);
                    var g50_elem = document.getElementById("g50_" + i);
                    var g80_elem = document.getElementById("g80_" + i);
                } else {
                    var cr_elem = document.getElementById("cr_all");
                    var g65_elem = document.getElementById("g65_all");
                    var g50_elem = document.getElementById("g50_all");
                    var g80_elem = document.getElementById("g80_all");
                }

                g50_elem.style.backgroundColor = "#D3D3D3";
                g50_elem.disabled = true;

                g80_elem.style.backgroundColor = "#D3D3D3";
                g80_elem.disabled = true;

                //reactivate cr g65
                cr_elem.style.backgroundColor = "";
                cr_elem.disabled = false;
                g65_elem.style.backgroundColor = "";
                g65_elem.disabled = false;

            }
        }


        /**
         * Fills hidden data  in html that is read when transitioning to listener goldilocks app. If function returns
         * false, transition will not take place because no program has been selected.
         *
         * @returns {boolean}
         */
        function fillDataField() {
            if (this['selectedProgram'] != -1) {
                var param = {
                    parameters,
                    parametersTwoChannels
                }
                document.getElementById("dataField").value = JSON.stringify(param);
                
                updateMostRecentProgram(this['selectedProgram']);
                return true;
            } else {
                alert("No program has been selected. Please select a program.");
                return false;
            }
        }


        // /**
        //  * Calculates and fills G50/G80 parameters and input fields based on CR/G65 input fields. Only works if app is
        //  * set to be controlled via CR/G65
        //  *
        //  * @param id
        //  */
        // function crg65(id){
        //     if(this['parameters']['control_via'] == 0) {
        //         var cr = parseFloat(document.getElementById("cr_" + id).value);
        //         console.log(cr);
        //         if (cr != 0) {
        //             slope = (1 - cr) / cr;
        //             var g65 = parseFloat(document.getElementById("g65_" + id).value);
        //             var g50 = g65 - (slope * 15);
        //             var g80 = g65 + (slope * 15);
        //             this['parameters']['g50'][id] = g50;
        //             this['parameters']['g80'][id] = g80;
        //
        //             document.getElementById("g50_" + id).value = g50;
        //             document.getElementById("g80_" + id).value = g80;
        //         }
        //     }
        // }
        //
        //
        // /**
        //  * Calculates and fills CR/G65 parameters and input fields based on G50/G80 input fields. Only works if app is
        //  * set to be controlled via CR/G65
        //  *
        //  * @param id
        //  */
        function g50g80(id) {
            if (this['parameters']['control_via'] == 1) {
                var g50 = parseFloat(document.getElementById("g50_" + id).value);
                var g80 = parseFloat(document.getElementById("g80_" + id).value);
                var slope = (g80 - g50) / 30;
                console.log("slope: ", slope);
                var g65 = g50 + slope * 15;
                for(var i = 0; i< channels.length; i++){
                    this['parametersTwoChannels'][channels[i]]['g65'][id] = g65;
                    if (slope != -1) {
                        var cr = Math.round((1 / (1 + slope)) * 10) / 10;
                        this['parametersTwoChannels'][channels[i]]['compression_ratio'][id] = cr;
                        document.getElementById("cr_" + id).value = cr;
                    }
                }
                document.getElementById("g65_" + id).value = g65;
            }
        }


        /**
         * Calculates and fills G65 input fields based on the function: G65[i] = Targets[i] - LTASS[i]
         * After calculating G65, calculates and fills G50/G80, and sets program to be controlled via
         */
        function calcg65() {
            //if currently being controlled by g50g80, switch

            if (this['parameters']['control_via'] !== 0) {
                this['parameters']['control_via'] == 0;
                $('#cr_g65').closest('.btn').button('toggle');
                deactivateG50G80();
            }
            for (var i = 0; i < 6; i++) {
                for(var j = 0; j < channels.length; j++){
                    this['parametersTwoChannels'][channels[j]]['g65'][i] =  this['parametersTwoChannels'][channels[j]]['targets'][i] -  this['parametersTwoChannels'][channels[j]]['ltass'][i];
                    document.getElementById("g65_" + i).value = this['parametersTwoChannels'][channels[j]]['g65'][i];
                }
                
                controlParameters(i);

            }
        }


        /**
         * Update most recently used program in the database so that it gets loaded by the goldilocks app when the listener
         * logs in.
         *
         * @param programId
         */
        function updateMostRecentProgram(programId) {
            console.log(programId);
            if (this['selectedProgram'] != -1) {
                //post request
                $.ajax({
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    url: '/goldilocks/listener/programs/current',
                    data: JSON.stringify([
                        programId
                    ]),
                    success: function(response) {
                        console.log("Success updating most recent program", response);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log("Error updating most recent program: " + textStatus + ' : ' + errorThrown);
                    }
                });
            }
        }



        /** Upon updating the input field in HTML, the parameter element needs to be updated as well. **/

        // function targets(id){
        //     this['parameters']['targets'][id] = parseFloat(document.getElementById("targets_"+id).value);
        // }
        // function ltass(id){
        //     this['parameters']['ltass'][id] = parseFloat(document.getElementById("ltass_"+id).value);
        // }
        // function hl(id){
        //     this['parameters']['hearing_loss'][id] = parseFloat(document.getElementById("hl_"+id).value);
        // }
        // function cr(id){
        //     this['parameters']['compression_ratio'][id] = parseFloat(document.getElementById("cr_"+id).value);
        //     crg65(id);
        // }
        // function g50(id){
        //     this['parameters']['g50'][id] = parseFloat(document.getElementById("g50_"+id).value);
        //     g50g80(id);
        // }
        // function g65(id){
        //     this['parameters']['g65'][id] = parseFloat(document.getElementById("g65_"+id).value);
        //     crg65(id);
        // }
        // function g80(id){
        //     this['parameters']['g80'][id] = parseFloat(document.getElementById("g80_"+id).value);
        //     g50g80(id);
        // }
        // function multiL(id){
        //     this['parameters']['multiplier_l'][id] = parseFloat(document.getElementById("multiL_"+id).value);
        // }
        // function multiH(id){
        //     this['parameters']['multiplier_h'][id] = parseFloat(document.getElementById("multiH_"+id).value);
        // }
        // function kneelow(id){
        //     this['parameters']['knee_low'][id] = parseFloat(document.getElementById("kneelow_"+id).value);
        // }
        // function mpo(id){
        //     this['parameters']['mpo_limit'][id] = parseFloat(document.getElementById("mpo_"+id).value);
        // }
        // function attack(id){
        //     this['parameters']['attack'][id] = parseFloat(document.getElementById("attack_"+id).value);
        // }
        // function release(id){
        //     this['parameters']['release'][id] = parseFloat(document.getElementById("release_"+id).value);
        // }
        function minL() {
            this['parameters']['l_min'] = parseFloat(document.getElementById("minL").value);
        }

        function maxL() {
            this['parameters']['l_max'] = parseFloat(document.getElementById("maxL").value);
        }

        function stepL() {
            this['parameters']['l_step'] = parseFloat(document.getElementById("stepL").value);
        }

        function minV() {
            this['parameters']['v_min'] = parseFloat(document.getElementById("minV").value);
        }

        function maxV() {
            this['parameters']['v_max'] = parseFloat(document.getElementById("maxV").value);
        }

        function stepV() {
            this['parameters']['v_step'] = parseFloat(document.getElementById("stepV").value);
        }

        function minH() {
            this['parameters']['h_min'] = parseFloat(document.getElementById("minH").value);
        }

        function maxH() {
            this['parameters']['h_max'] = parseFloat(document.getElementById("maxH").value);
        }

        function stepH() {
            this['parameters']['h_step'] = parseFloat(document.getElementById("stepH").value);
        }

        function gainChanged() {
            this['parameters']['gain'] = parseFloat(document.getElementById("gain").value);
        }


        /**
         * Make a get request on /api/getParams to pull the parameters directly from the RTMHA. After getting the
         * RTMHA parameters, the function updates the javascript and inner html elements with the appropriate values.
         * It will also switch to control via G50/G80 automatically because RTMHA only sends G50/G80 parameters.
         */
        function monitorValues() {
            $.ajax({
                method: 'GET',
                url: '/api/getParams',
                success: function(response) {
                    console.log(response);
                    params = JSON.parse(response);

                    //update parameters to reflect RTMHA state
                    parametersTwoChannels['left']['g50'] = params['left']['g50'];
                    parametersTwoChannels['left']['g80'] = params['left']['g80'];
                    parametersTwoChannels['left']['knee_low'] = params['left']['knee_low'];
                    parametersTwoChannels['left']['mpo_band'] = params['left']['mpo_band'];
                    parametersTwoChannels['left']['afc'] = params['left']['afc'];
                    parametersTwoChannels['left']['attack'] = params['left']['attack'];
                    parametersTwoChannels['left']['release'] = params['left']['release'];
                    parametersTwoChannels['left']['gain'] = params['left']['gain'];

                    parametersTwoChannels['right']['g50'] = params['right']['g50'];
                    parametersTwoChannels['right']['g80'] = params['right']['g80'];
                    parametersTwoChannels['right']['knee_low'] = params['right']['knee_low'];
                    parametersTwoChannels['right']['mpo_band'] = params['right']['mpo_band'];
                    parametersTwoChannels['right']['afc'] = params['right']['afc'];
                    parametersTwoChannels['right']['attack'] = params['right']['attack'];
                    parametersTwoChannels['right']['release'] = params['right']['release'];
                    parametersTwoChannels['right']['gain'] = params['right']['gain'];

                    //update inner html to reflect RTMHA state
                    for (i = 0; i < 6; i++) {
                        //use channels[0] to update value since when control via both channels, param values should be the same
                        document.getElementById("g50_" + i).value = parametersTwoChannels[channels[0]]['g50'][i];
                        document.getElementById("g80_" + i).value = parametersTwoChannels[channels[0]]['g80'][i];
                        document.getElementById("kneelow_" + i).value = parametersTwoChannels[channels[0]]['knee_low'][i];
                        document.getElementById("mpo_" + i).value = parametersTwoChannels[channels[0]]['mpo_band'][i];
                        document.getElementById("attack_" + i).value = parametersTwoChannels[channels[0]]['attack'][i];
                        document.getElementById("release_" + i).value = parametersTwoChannels[channels[0]]['release'][i];
                    }

                    //change control to G50/G80 because RTMHA only sends G50/G80
                    parameters['control_via'] = 1;
                    $('#g50_g80').closest('.btn').button('toggle');
                    for (i = 0; i < 6; i++) {
                        g50g80(i);
                    }
                    deactivateCRG65();

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    alert("[ERROR] monitorValues(): " + errorThrown);
                }
            });
        }


        /**
         * Change the values of the inner HTML elements and javascript parameters based on the program selected from
         * the dropdown menu. Also calls updateMostRecentProgram() to set this as the program the listener will
         * use when logging into the Goldilocks app.
         *
         * @param id
         * @param progName
         */
        function changeProgram(id, progName) {
            console.log('change program:', id);
            document.getElementById("btnGroupDrop1").innerHTML = progName;
            // var newProgram = this['programs'][id];
            var newProgram = JSON.parse(JSON.stringify(programs[id]));
            this['parameters'] = newProgram['parameters'];
            this['parametersTwoChannels'] = newProgram['parametersTwoChannels'];
            this['selectedProgram'] = id;


            //control via
            if (this['parameters']['control_via'] == 0) {
                $('#cr_g65').closest('.btn').button('toggle');
                deactivateG50G80();
            } else {
                $('#g50_g80').closest('.btn').button('toggle');
                deactivateCRG65();
            }

            //afc
            if (this['parameters']['afc'] == 0) {
                $('#afc_off').closest('.btn').button('toggle');
            } else {
                $('#afc_on').closest('.btn').button('toggle');
            }

            //sequence_num
            if (this['parameters']['sequence_num'] == 3) {
                $('#num_3').closest('.btn').button('toggle');
            } else {
                $('#num_2').closest('.btn').button('toggle');
            }

            //sequence_order
            if (this['parameters']['sequence_order'] == 0) {
                $('#seq_v').closest('.btn').button('toggle');
            } else {
                $('#seq_h').closest('.btn').button('toggle');
            }

            //sequence_order
            if (this['parameters']['app_behavior'] == 0) {
                $('#one_by_one').closest('.btn').button('toggle');
            } else if (this['parameters']['app_behavior'] == 1) {
                $('#volume_only').closest('.btn').button('toggle');
            } else {
                $('#all_3').closest('.btn').button('toggle');
            }

            //fill in input fields
            updateParameters();

            document.getElementById("minL").value = fixPrecision(this['parameters']['l_min']);
            document.getElementById("maxL").value = fixPrecision(this['parameters']['l_max']);
            document.getElementById("stepL").value = fixPrecision(this['parameters']['l_step']);
            document.getElementById("minV").value = fixPrecision(this['parameters']['v_min']);
            document.getElementById("maxV").value = fixPrecision(this['parameters']['v_max']);
            document.getElementById("stepV").value = fixPrecision(this['parameters']['v_step']);
            document.getElementById("minH").value = fixPrecision(this['parameters']['h_min']);
            document.getElementById("maxH").value = fixPrecision(this['parameters']['h_max']);
            document.getElementById("stepH").value = fixPrecision(this['parameters']['h_step']);
            document.getElementById("gain").value = fixPrecision(this['parameters']['gain']);

            //store in database most recent program for listener
            updateMostRecentProgram(id);
        }


        /**
         * Overrite current program with new values. If no program has been selected user is prompted to Save As
         * instead.
         */
        function save() {
            console.log("Save");
            if (this['selectedProgram'] != -1) {
                //ajax request to save program
                $.ajax({
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    url: '/goldilocks/listener',
                    data: JSON.stringify([
                        this['selectedProgram'],
                        this['parameters'],
                        this['parametersTwoChannels']
                    ]),
                    success: function(response) {
                        console.log(response);
                        alert('Program saved.');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        alert('There was an error with your request');
                    }
                });
            } else {
                alert("No setting selected. Click Save As.");
            }
        }


        /**
         * Save current settings as a new program. Program name field must not be empty.
         *
         */
        function saveAs() {
            console.log("Save as");
            console.log(this['parametersTwoChannels']);
            console.log(this['parameters']);
            if (document.getElementById("modalInput").value == '') {
                document.getElementById("modalInputError").innerHTML = "Must include a name";
                console.log("Error: input is empty");
            } else {
                $.ajax({
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    url: '/goldilocks/listener',
                    data: JSON.stringify([
                        document.getElementById("modalInput").value,
                        this['parameters'],
                        this['parametersTwoChannels']
                    ]),
                    success: function(response) {
                        console.log(JSON.parse(response));
                        if (JSON.parse(response)['status'] == "failure") {
                            document.getElementById("modalInputError").innerHTML = "The name \"" + (document.getElementById("modalInput").value) + "\" is already taken.";
                        } else {
                            $('#saveAsModal').modal('hide');
                            alert("New program saved. Refreshing the web page.");
                            //location.reload();
                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        document.getElementById("modalInputError").innerHTML = "There was an error with your request.";
                    }
                });
            }
        }


        /**
         * Transmit the current program state by makign a POST request on api/params
         *
         */
        function transmit() {
            console.log("Attempting to transmit");
            console.log(parametersTwoChannels)
            $.ajax({
                method: 'POST',
                url: '/api/params',
                data: JSON.stringify({
                    user_id: this['listener'],
                    method: "set",
                    request_action: 1,
                    data: {
                        left: {
                            en_ha: 1,
                            afc: parameters['afc'],
                            rear_mics: 0,
                            g50: parametersTwoChannels['left']['g50'],
                            g80: parametersTwoChannels['left']['g80'],
                            knee_low: parametersTwoChannels['left']['knee_low'],
                            mpo_band: parametersTwoChannels['left']['mpo_band'],
                            attack: parametersTwoChannels['left']['attack'],
                            release:  parametersTwoChannels['left']['release'],
                            gain:  parameters['gain']
                        },
                        right: {
                            en_ha: 1,
                            afc:  parameters['afc'],
                            rear_mics: 0,
                            g50: parametersTwoChannels['right']['g50'],
                            g80: parametersTwoChannels['right']['g80'],
                            knee_low: parametersTwoChannels['right']['knee_low'],
                            mpo_band: parametersTwoChannels['right']['mpo_band'],
                            attack: parametersTwoChannels['right']['attack'],
                            release: parametersTwoChannels['right']['release'],
                            gain: parameters['gain']
                        }
                    }
                }),
                success: function(response) {
                    console.log(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    alert("[ERROR] transmit(): " + errorThrown);
                }
            });
        }
    </script>

</body>

</html>