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
    </style>


    <title>Amplification Parameters</title>
</head>


<body>


    <div class="container" style="margin-top: 10px">
        {{--<div class="row">--}}
            {{--<div class="col-12 btn-group">--}}
                {{--<a class="btn btn-info btn-block" href="{{ url('/researcher/amplification') }}" role="button">Amplification</a>--}}
                {{--<a class="btn btn-light btn-block" href="{{ url('/researcher/noise') }}" role="button">Noise Management</a>--}}
                {{--<a class="btn btn-light btn-block" role="button" href="{{ url('/researcher/feedback') }}">Feedback Management </a>--}}
            {{--</div>--}}
        {{--</div>--}}
        <nav class="nav nav-pills nav-justified" style="margin-bottom: 10px">
            <a class="nav-item nav-link text-white bg-info" href="#">Amplification</a>
            <a class="nav-item nav-link text-info bg-white" href="{{ url('/researcher/noise') }}">Noise Management</a>
            <a class="nav-item nav-link text-info bg-white" href="{{ url('/researcher/feedback') }}">Feedback Management</a>
        </nav>



        {{--<div class="row align-items-center" style="margin-top: 10px; margin-bottom: 10px">--}}
              {{--<div class="col-4">--}}
                  {{--<button type="button" class="btn btn-info btn-block" onclick="save()">Save</button>--}}
              {{--</div>--}}
              {{--<div class="col-4">--}}
                  {{--<button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#saveAsModal">Save-as</button>--}}
              {{--</div>--}}
        {{--</div>--}}



        {{--<div class="row align-content-end">--}}
            {{--<div class="col-2 offset-2">--}}
                {{--Control via:--}}

            {{--</div>--}}
            {{--<div class="col-2 offset-4">--}}
                {{--AFC:--}}
            {{--</div>--}}
        {{--</div>--}}

        {{--<div class="row align-items-center" style="margin-bottom: 10px">--}}
            {{--<div class="col-2 offset-2">--}}
                {{--<div id="control_via_buttons" class="btn-group btn-group-toggle" data-toggle="buttons">--}}
                    {{--<label class="btn btn-info active">--}}
                        {{--<input type="radio" name="control_via" id="cr_g65"  autocomplete="off" checked> CR/G65--}}
                    {{--</label>--}}
                    {{--<label class="btn btn-info">--}}
                        {{--<input type="radio" name="control_via" id="g50_g80"  autocomplete="off"> G50/G80--}}
                    {{--</label>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="col-2 offset-4">--}}
                {{--<div id="afc_buttons" class="btn-group btn-group-toggle" data-toggle="buttons">--}}
                    {{--<label class="btn btn-info active">--}}
                        {{--<input type="radio" name="afc" id="afc_on" autocomplete="off" checked> On--}}
                    {{--</label>--}}
                    {{--<label class="btn btn-info">--}}
                        {{--<input type="radio" name="afc" id="afc_off" autocomplete="off"> Off--}}
                    {{--</label>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}



        <div class="row align-items-center" style="margin-bottom: 10px">
            {{--<div class="col-6">--}}
                {{--<div class="row align-items-center">--}}
                    {{--Control via:--}}
                {{--</div>--}}
                {{--<div class="row align-items-center">--}}
                    {{--<div id="control_via_buttons" class="btn-group btn-group-toggle" data-toggle="buttons">--}}
                        {{--<label class="btn btn-light active">--}}
                            {{--<input type="radio" name="control_via" id="cr_g65"  autocomplete="off" checked> CR/G65--}}
                        {{--</label>--}}
                        {{--<label class="btn btn-light">--}}
                            {{--<input type="radio" name="control_via" id="g50_g80"  autocomplete="off"> G50/G80--}}
                        {{--</label>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="col-6">--}}
                {{--<div class="row align-items-center">--}}
                    {{--Which ear?:--}}
                {{--</div>--}}
                {{--<div class="row align-items-center">--}}
                    {{--<div id="ear_buttons" class="btn-group btn-group-toggle" data-toggle="buttons">--}}
                        {{--<label class="btn btn-light active">--}}
                            {{--<input type="radio" name="afc" id="left_on"  checked> Left--}}
                        {{--</label>--}}
                        {{--<label class="btn btn-light">--}}
                            {{--<input type="radio" name="afc" id="right_on" > Right--}}
                        {{--</label>--}}
                        {{--<label class="btn btn-light">--}}
                            {{--<input type="radio" name="afc" id="both_on" > Both--}}
                        {{--</label>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="col-6">--}}
                {{--<div class="row align-items-center">--}}
                    {{--AFC:--}}
                {{--</div>--}}
                {{--<div class="row align-items-center">--}}
                    {{--<div id="afc_buttons" class="btn-group btn-group-toggle" data-toggle="buttons">--}}
                        {{--<label class="btn btn-light active">--}}
                            {{--<input type="radio" name="afc" id="afc_on" autocomplete="off" checked> On--}}
                        {{--</label>--}}
                        {{--<label class="btn btn-light">--}}
                            {{--<input type="radio" name="afc" id="afc_off" autocomplete="off"> Off--}}
                        {{--</label>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}

            <div class="col-6">
                <div class="row">
                    <div class="col-6">
                        <div class="row align-items-center">
                            Control via:
                        </div>
                        <div class="row align-items-center">
                            <div id="control_via_buttons" class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-light active">
                                    <input type="radio" name="control_via" id="g50_g80"  autocomplete="off" checked> G50/G80
                                </label>
                                <label class="btn btn-light">
                                    <input type="radio" name="control_via" id="cr_g65"  autocomplete="off"> CR/G65
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <div class="row align-items-center">
                            Global MPO:
                        </div>
                        <div class="row align-items-center" style="width:100px">

                        <input style="color: black; font-size: 1.2rem" name="GlobalMPO" id="global_mpo" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                    <div class="row align-items-center">
                            AFC Delay:
                        </div>
                        <div class="row align-items-center" style="width:100px">
                        <input style="color: black; font-size: 1.2rem" name="AfcDelay" id="afc_delay" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0">
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


    <div style="margin-left: 20px; margin-right: 20px">
        <div class="row">
            <div class="col-12">
                {{--<table class="table table-striped table-bordered table-sm" style="border-color: white">--}}
                    {{--<thead>--}}
                        {{--<th class="text-center" style="font-size: 2.5rem; border-color: white"></th>--}}
                        {{--<th class="center-text" style="font-size: 2rem; border-color: white">250</th>--}}
                        {{--<th class="center-text" style="font-size: 2rem; border-color: white">500 </th>--}}
                        {{--<th class="center-text" style="font-size: 2rem; border-color: white">1000</th>--}}
                        {{--<th class="center-text" style="font-size: 2rem; border-color: white">2000</th>--}}
                        {{--<th class="center-text" style="font-size: 2rem; border-color: white">4000</th>--}}
                        {{--<th class="center-text" style="font-size: 2rem; border-color: white">8000</th>--}}
                        {{--<th class="center-text" style="font-size: 2rem; color:green; border-color: white">All</th>--}}
                    {{--</thead>--}}

                    {{--<tbody>--}}
                        {{--<tr>--}}
                            {{--<th class="text-center" style="font-size: 1.5rem; border-color: white">CR</th>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="compression_ratio" data-index="0" id="cr_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="1"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="compression_ratio" data-index="1" id="cr_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="1"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="compression_ratio" data-index="2" id="cr_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="1"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="compression_ratio" data-index="3" id="cr_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="1"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="compression_ratio" data-index="4" id="cr_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="1"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="compression_ratio" data-index="5" id="cr_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="1"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="compression_ratio" data-index="6" id="cr_all" class="form-control form-control-sm table-field font-weight-bold" type="number" value="1"></td>--}}
                        {{--</tr>--}}
                        {{--<tr>--}}
                            {{--<th class="text-center" style="font-size: 1.5rem; border-color: white">G50</th>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50" data-index="0" id="g50_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50" data-index="1" id="g50_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50" data-index="2" id="g50_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50" data-index="3" id="g50_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50" data-index="4" id="g50_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50" data-index="5" id="g50_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g50" data-index="6" id="g50_all" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                        {{--</tr>--}}
                        {{--<tr>--}}
                            {{--<th class="text-center" style="font-size: 1.5rem; border-color: white">G65</th>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g65" data-index="0" id="g65_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g65" data-index="1" id="g65_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g65" data-index="2" id="g65_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g65" data-index="3" id="g65_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g65" data-index="4" id="g65_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g65" data-index="5" id="g65_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g65" data-index="6" id="g65_all" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                        {{--</tr>--}}
                        {{--<tr>--}}
                            {{--<th class="text-center" style="font-size: 1.5rem; border-color: white">G80</th>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g80" data-index="0" id="g80_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g80" data-index="1" id="g80_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g80" data-index="2" id="g80_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g80" data-index="3" id="g80_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g80" data-index="4" id="g80_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g80" data-index="5" id="g80_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="g80" data-index="6" id="g80_all" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0"></td>--}}
                        {{--</tr>--}}
                        {{--<tr>--}}
                            {{--<th class="text-center" style="font-size: 1.5rem; border-color: white">Knee</th>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="knee_low" data-index="0" id="kneelow_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="45"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="knee_low" data-index="1" id="kneelow_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="45"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="knee_low" data-index="2" id="kneelow_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="45"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="knee_low" data-index="3" id="kneelow_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="45"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="knee_low" data-index="4" id="kneelow_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="45"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="knee_low" data-index="5" id="kneelow_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="45"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="knee_low" data-index="6" id="kneelow_all" class="form-control form-control-sm table-field font-weight-bold" type="number" value="45"></td>--}}

                        {{--</tr>--}}
                        {{--<tr>--}}
                            {{--<th class="text-center" style="font-size: 1.5rem; border-color: white">MPO</th>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="mpo_band" data-index="0" id="mpo_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="120"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="mpo_band" data-index="1" id="mpo_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="120"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="mpo_band" data-index="2" id="mpo_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="120"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="mpo_band" data-index="3" id="mpo_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="120"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="mpo_band" data-index="4" id="mpo_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="120"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="mpo_band" data-index="5" id="mpo_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="120"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="mpo_band" data-index="6" id="mpo_all" class="form-control form-control-sm table-field font-weight-bold" type="number" value="120"></td>--}}

                        {{--</tr>--}}
                        {{--<tr>--}}
                            {{--<th class="text-center" style="font-size: 1.5rem; border-color: white">Attack</th>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="attack" data-index="0" id="attack_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="5"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="attack" data-index="1" id="attack_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="5"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="attack" data-index="2" id="attack_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="5"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="attack" data-index="3" id="attack_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="5"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="attack" data-index="4" id="attack_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="5"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="attack" data-index="5" id="attack_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="5"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="attack" data-index="6" id="attack_all" class="form-control form-control-sm table-field font-weight-bold" type="number" value="5"></td>--}}

                        {{--</tr>--}}

                        {{--<tr>--}}
                            {{--<th class="text-center" style="font-size: 1.5rem; border-color: white">Release</th>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="release" data-index="0" id="release_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="20"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="release" data-index="1" id="release_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="20"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="release" data-index="2" id="release_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="20"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="release" data-index="3" id="release_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="20"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="release" data-index="4" id="release_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="20"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="release" data-index="5" id="release_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="20"></td>--}}
                            {{--<td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" name="release" data-index="6" id="release_all" class="form-control form-control-sm table-field font-weight-bold" type="number" value="20"></td>--}}
                        {{--</tr>--}}
                    {{--</tbody>--}}
                {{--</table>--}}

                <table class="table table-sm" style="border-color: white">
                    <thead>
                        <tr>
                            <th class="text-center" style="font-size: 1rem; border-color: white">L250</th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">L500 </th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">L1000</th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">L2000</th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">L4000</th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">L8000</th>
                            <th class="text-center" style="font-size: 1rem; color:green; border-color: white">L-All</th>

                            <th class="text-center" style="font-size: 1rem; border-color: white"></th>

                            <th class="text-center" style="font-size: 1rem; border-color: white">R250</th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">R500 </th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">R1000</th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">R2000</th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">R4000</th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">R8000</th>
                            <th class="text-center" style="font-size: 1rem; color:green; border-color: white">R-All</th>
                        </tr>
                    </thead>

                    <tbody>
                    <tr>
                        <td class="mha-td table-success"><input name="compression_ratio" data-side="left" data-index="0" id="cr_0_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="1"></td>
                        <td class="mha-td table-success"><input name="compression_ratio" data-side="left" data-index="1" id="cr_1_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="1"></td>
                        <td class="mha-td table-success"><input name="compression_ratio" data-side="left" data-index="2" id="cr_2_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="1"></td>
                        <td class="mha-td table-success"><input name="compression_ratio" data-side="left" data-index="3" id="cr_3_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="1"></td>
                        <td class="mha-td table-success"><input name="compression_ratio" data-side="left" data-index="4" id="cr_4_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="1"></td>
                        <td class="mha-td table-success"><input name="compression_ratio" data-side="left" data-index="5" id="cr_5_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="1"></td>
                        <td class="mha-td table-success"><input name="compression_ratio" data-side="left" data-index="6" id="cr_all_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value=""></td>

                        <th class="text-center" style="font-size: 1rem; border-color: white">CR</th>

                        <td class="mha-td table-info"><input name="compression_ratio" data-side="right" data-index="0" id="cr_0_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="1"></td>
                        <td class="mha-td table-info"><input name="compression_ratio" data-side="right" data-index="1" id="cr_1_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="1"></td>
                        <td class="mha-td table-info"><input name="compression_ratio" data-side="right" data-index="2" id="cr_2_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="1"></td>
                        <td class="mha-td table-info"><input name="compression_ratio" data-side="right" data-index="3" id="cr_3_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="1"></td>
                        <td class="mha-td table-info"><input name="compression_ratio" data-side="right" data-index="4" id="cr_4_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="1"></td>
                        <td class="mha-td table-info"><input name="compression_ratio" data-side="right" data-index="5" id="cr_5_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="1"></td>
                        <td class="mha-td table-info"><input name="compression_ratio" data-side="right" data-index="6" id="cr_all_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value=""></td>
                    </tr>
                    <tr>
                        <td class="mha-td"><input name="g50" data-side="left" data-index="0" id="g50_0_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g50" data-side="left" data-index="1" id="g50_1_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g50" data-side="left" data-index="2" id="g50_2_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g50" data-side="left" data-index="3" id="g50_3_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g50" data-side="left" data-index="4" id="g50_4_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g50" data-side="left" data-index="5" id="g50_5_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g50" data-side="left" data-index="6" id="g50_all_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value=""></td>

                        <th class="text-center" style="font-size: 1rem; border-color: white">G50</th>

                        <td class="mha-td"><input name="g50" data-side="right" data-index="0" id="g50_0_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g50" data-side="right" data-index="1" id="g50_1_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g50" data-side="right" data-index="2" id="g50_2_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g50" data-side="right" data-index="3" id="g50_3_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g50" data-side="right" data-index="4" id="g50_4_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g50" data-side="right" data-index="5" id="g50_5_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g50" data-side="right" data-index="6" id="g50_all_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value=""></td>
                    </tr>
                    <tr>
                        <td class="mha-td table-success"><input name="g65" data-side="left" data-index="0" id="g65_0_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td table-success"><input name="g65" data-side="left" data-index="1" id="g65_1_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td table-success"><input name="g65" data-side="left" data-index="2" id="g65_2_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td table-success"><input name="g65" data-side="left" data-index="3" id="g65_3_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td table-success"><input name="g65" data-side="left" data-index="4" id="g65_4_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td table-success"><input name="g65" data-side="left" data-index="5" id="g65_5_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td table-success"><input name="g65" data-side="left" data-index="6" id="g65_all_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value=""></td>

                        <th class="text-center" style="font-size: 1rem; border-color: white">G65</th>

                        <td class="mha-td table-info"><input name="g65" data-side="right" data-index="0" id="g65_0_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td table-info"><input name="g65" data-side="right" data-index="1" id="g65_1_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td table-info"><input name="g65" data-side="right" data-index="2" id="g65_2_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td table-info"><input name="g65" data-side="right" data-index="3" id="g65_3_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td table-info"><input name="g65" data-side="right" data-index="4" id="g65_4_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td table-info"><input name="g65" data-side="right" data-index="5" id="g65_5_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td table-info"><input name="g65" data-side="right" data-index="6" id="g65_all_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value=""></td>
                    </tr>
                    <tr>
                        <td class="mha-td"><input name="g80" data-side="left" data-index="0" id="g80_0_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g80" data-side="left" data-index="1" id="g80_1_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g80" data-side="left" data-index="2" id="g80_2_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g80" data-side="left" data-index="3" id="g80_3_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g80" data-side="left" data-index="4" id="g80_4_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g80" data-side="left" data-index="5" id="g80_5_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g80" data-side="left" data-index="6" id="g80_all_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value=""></td>

                        <th class="text-center" style="font-size: 1rem; border-color: white">G80</th>

                        <td class="mha-td"><input name="g80" data-side="right" data-index="0" id="g80_0_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g80" data-side="right" data-index="1" id="g80_1_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g80" data-side="right" data-index="2" id="g80_2_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g80" data-side="right" data-index="3" id="g80_3_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g80" data-side="right" data-index="4" id="g80_4_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g80" data-side="right" data-index="5" id="g80_5_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="0"></td>
                        <td class="mha-td"><input name="g80" data-side="right" data-index="6" id="g80_all_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value=""></td>


                    </tr>
                    <tr>
                        <td class="mha-td table-success"><input name="knee_low" data-side="left" data-index="0" id="kneelow_0_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="45"></td>
                        <td class="mha-td table-success"><input name="knee_low" data-side="left" data-index="1" id="kneelow_1_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="45"></td>
                        <td class="mha-td table-success"><input name="knee_low" data-side="left" data-index="2" id="kneelow_2_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="45"></td>
                        <td class="mha-td table-success"><input name="knee_low" data-side="left" data-index="3" id="kneelow_3_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="45"></td>
                        <td class="mha-td table-success"><input name="knee_low" data-side="left" data-index="4" id="kneelow_4_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="45"></td>
                        <td class="mha-td table-success"><input name="knee_low" data-side="left" data-index="5" id="kneelow_5_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="45"></td>
                        <td class="mha-td table-success"><input name="knee_low" data-side="left" data-index="6" id="kneelow_all_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value=""></td>

                        <th class="text-center" style="font-size: 1rem; border-color: white">Knee</th>

                        <td class="mha-td table-info"><input name="knee_low" data-side="right" data-index="0" id="kneelow_0_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="45"></td>
                        <td class="mha-td table-info"><input name="knee_low" data-side="right" data-index="1" id="kneelow_1_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="45"></td>
                        <td class="mha-td table-info"><input name="knee_low" data-side="right" data-index="2" id="kneelow_2_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="45"></td>
                        <td class="mha-td table-info"><input name="knee_low" data-side="right" data-index="3" id="kneelow_3_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="45"></td>
                        <td class="mha-td table-info"><input name="knee_low" data-side="right" data-index="4" id="kneelow_4_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="45"></td>
                        <td class="mha-td table-info"><input name="knee_low" data-side="right" data-index="5" id="kneelow_5_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="45"></td>
                        <td class="mha-td table-info"><input name="knee_low" data-side="right" data-index="6" id="kneelow_all_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value=""></td>

                    </tr>
                    <tr>
                        <td class="mha-td"><input name="mpo_band" data-side="left" data-index="0" id="mpo_0_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="120"></td>
                        <td class="mha-td"><input name="mpo_band" data-side="left" data-index="1" id="mpo_1_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="120"></td>
                        <td class="mha-td"><input name="mpo_band" data-side="left" data-index="2" id="mpo_2_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="120"></td>
                        <td class="mha-td"><input name="mpo_band" data-side="left" data-index="3" id="mpo_3_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="120"></td>
                        <td class="mha-td"><input name="mpo_band" data-side="left" data-index="4" id="mpo_4_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="120"></td>
                        <td class="mha-td"><input name="mpo_band" data-side="left" data-index="5" id="mpo_5_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="120"></td>
                        <td class="mha-td"><input name="mpo_band" data-side="left" data-index="6" id="mpo_all_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value=""></td>

                        <th class="text-center" style="font-size: 1rem; border-color: white">MPO</th>

                        <td class="mha-td"><input name="mpo_band" data-side="right" data-index="0" id="mpo_0_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="120"></td>
                        <td class="mha-td"><input name="mpo_band" data-side="right" data-index="1" id="mpo_1_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="120"></td>
                        <td class="mha-td"><input name="mpo_band" data-side="right" data-index="2" id="mpo_2_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="120"></td>
                        <td class="mha-td"><input name="mpo_band" data-side="right" data-index="3" id="mpo_3_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="120"></td>
                        <td class="mha-td"><input name="mpo_band" data-side="right" data-index="4" id="mpo_4_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="120"></td>
                        <td class="mha-td"><input name="mpo_band" data-side="right" data-index="5" id="mpo_5_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="120"></td>
                        <td class="mha-td"><input name="mpo_band" data-side="right" data-index="6" id="mpo_all_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value=""></td>

                    </tr>
                    <tr>
                        <td class="mha-td table-success"><input name="attack" data-side="left" data-index="0" id="attack_0_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="5"></td>
                        <td class="mha-td table-success"><input name="attack" data-side="left" data-index="1" id="attack_1_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="5"></td>
                        <td class="mha-td table-success"><input name="attack" data-side="left" data-index="2" id="attack_2_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="5"></td>
                        <td class="mha-td table-success"><input name="attack" data-side="left" data-index="3" id="attack_3_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="5"></td>
                        <td class="mha-td table-success"><input name="attack" data-side="left" data-index="4" id="attack_4_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="5"></td>
                        <td class="mha-td table-success"><input name="attack" data-side="left" data-index="5" id="attack_5_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="5"></td>
                        <td class="mha-td table-success"><input name="attack" data-side="left" data-index="6" id="attack_all_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value=""></td>

                        <th class="text-center" style="font-size: 1rem; border-color: white">Attack</th>

                        <td class="mha-td table-info"><input name="attack" data-side="right" data-index="0" id="attack_0_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="5"></td>
                        <td class="mha-td table-info"><input name="attack" data-side="right" data-index="1" id="attack_1_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="5"></td>
                        <td class="mha-td table-info"><input name="attack" data-side="right" data-index="2" id="attack_2_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="5"></td>
                        <td class="mha-td table-info"><input name="attack" data-side="right" data-index="3" id="attack_3_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="5"></td>
                        <td class="mha-td table-info"><input name="attack" data-side="right" data-index="4" id="attack_4_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="5"></td>
                        <td class="mha-td table-info"><input name="attack" data-side="right" data-index="5" id="attack_5_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="5"></td>
                        <td class="mha-td table-info"><input name="attack" data-side="right" data-index="6" id="attack_all_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value=""></td>

                    </tr>

                    <tr>
                        <td class="mha-td"><input name="release" data-side="left" data-index="0" id="release_0_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="20"></td>
                        <td class="mha-td"><input name="release" data-side="left" data-index="1" id="release_1_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="20"></td>
                        <td class="mha-td"><input name="release" data-side="left" data-index="2" id="release_2_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="20"></td>
                        <td class="mha-td"><input name="release" data-side="left" data-index="3" id="release_3_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="20"></td>
                        <td class="mha-td"><input name="release" data-side="left" data-index="4" id="release_4_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="20"></td>
                        <td class="mha-td"><input name="release" data-side="left" data-index="5" id="release_5_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="20"></td>
                        <td class="mha-td"><input name="release" data-side="left" data-index="6" id="release_all_l" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value=""></td>

                        <th class="text-center" style="font-size: 1rem; border-color: white">Release</th>

                        <td class="mha-td"><input name="release" data-side="right" data-index="0" id="release_0_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="20"></td>
                        <td class="mha-td"><input name="release" data-side="right" data-index="1" id="release_1_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="20"></td>
                        <td class="mha-td"><input name="release" data-side="right" data-index="2" id="release_2_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="20"></td>
                        <td class="mha-td"><input name="release" data-side="right" data-index="3" id="release_3_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="20"></td>
                        <td class="mha-td"><input name="release" data-side="right" data-index="4" id="release_4_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="20"></td>
                        <td class="mha-td"><input name="release" data-side="right" data-index="5" id="release_5_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value="20"></td>
                        <td class="mha-td"><input name="release" data-side="right" data-index="6" id="release_all_r" class="form-control form-control-sm table-field font-weight-bold mha-input-field" type="number" value=""></td>

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

    <script type="text/javascript">
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
                'afc': 1, //1 - on, 0 - off
                'afc_type': 0,
                'global_mpo': 0,
                'afc_delay': 0,
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
                'afc': 1, //1 - on, 0 - off
                'afc_type': 0,
                'global_mpo': 0,
                'afc_delay': 0
    
            },
            'control_via': 1
        }


        //pull data on loading the page
        readMHA();

        //on changing input field, update parameters and highlight input field
        $('input[type=number]').keyup(function(event){
            var elem = event.target;
            if(elem.name == "GlobalMPO"){
                parameters['left']['global_mpo']  = Number(elem.value);
                parameters['right']['global_mpo'] = Number(elem.value);
                return;
            }
            if(elem.name == 'AfcDelay'){
                parameters['left']['afc_delay'] = Number(elem.value);
                parameters['right']['afc_delay'] = Number(elem.value);
                return;
            }
            
            if(elem.dataset.index != 6) {
                console.log(elem.dataset.side, elem.name, elem.dataset.index, Number(elem.value));
                parameters[elem.dataset.side][elem.name][elem.dataset.index] = Number(elem.value);
                elem.style.backgroundColor = "#FEEEBA";
                if(elem.name == "compression_ratio" || elem.name == "g50" || elem.name == "g65" || elem.name == "g80" ){
                    controlParameters(elem.dataset.index, elem.dataset.side);
                }
            }
            else{
                //"All" input field
                var value = Number(elem.value);
                var currentElemArray = document.getElementsByName(elem.name);
                if(elem.dataset.side == "left") {
                    for (var i = 0; i < 6; i++) {
                        currentElemArray[i].value = value;
                        currentElemArray[i].style.backgroundColor = "#FEEEBA";
                        parameters[elem.dataset.side][elem.name][i] = value;
                        if (elem.name == "compression_ratio" || elem.name == "g50" || elem.name == "g65" || elem.name == "g80") {
                            controlParameters(i, "left");
                        }
                    }
                }
                else if(elem.dataset.side == "right"){
                    for (var i = 7; i < 13; i++) {
                        currentElemArray[i].value = value;
                        currentElemArray[i].style.backgroundColor = "#FEEEBA";
                        parameters[elem.dataset.side][elem.name][i-7] = value;
                        if (elem.name == "compression_ratio" || elem.name == "g50" || elem.name == "g65" || elem.name == "g80") {
                            controlParameters(i-7, "right");
                        }
                    }
                }
            }
        })
            .click(function(){
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
            // for(i = 0; i < 6; i++){
            //     controlParameters(i);
            // }
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

        function controlParameters(id, side){
            var index_side = "";
            if(side == "left"){
                index_side = id + "_l";
            }
            else if( side == "right"){
                index_side = id + "_r";
            }
            //controlling via CR/G65
            if(parameters['control_via'] == 0){
                var cr = parseFloat(document.getElementById("cr_" + index_side).value);
                if (cr != 0) {
                    slope = (1 - cr) / cr;
                    var g65 = parseFloat(document.getElementById("g65_" + index_side).value);
                    var g50 = g65 - (slope * 15);
                    var g80 = g65 + (slope * 15);
                    parameters[side]['g50'][id] = g50;
                    parameters[side]['g80'][id] = g80;

                    document.getElementById("g50_" + index_side).value = g50;
                    document.getElementById("g50_" + index_side).style.backgroundColor = "#FEEEBA";
                    document.getElementById("g80_" + index_side).value = g80;
                    document.getElementById("g80_" + index_side).style.backgroundColor = "#FEEEBA";

                }
            }
            //controlling via G50/G80
            else if(parameters['control_via'] == 1){
                var g50 = parseFloat(document.getElementById("g50_" + index_side).value);
                var g80 = parseFloat(document.getElementById("g80_" + index_side).value);
                var slope = (g80 - g50)/30;
                var g65 = g50 + slope * 15;
                parameters[side]['g65'][id] = g65;
                document.getElementById("g65_" + index_side).value = g65;
                document.getElementById("g65_" + index_side).style.backgroundColor = "#FEEEBA";

                if(slope != -1){
                    var cr = Math.round( (1 / (1 + slope)) * 100 ) / 100;
                    parameters[side]['compression_ratio'][id] = cr;
                    document.getElementById("cr_" + index_side).value = cr;
                    document.getElementById("cr_" + index_side).style.backgroundColor = "#FEEEBA";
                }
            }
        }

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
                    data: {
                        left: {
                            en_ha: 1,
                            afc: this['parameters']['left']['afc'],
                            rear_mics: 0,
                            g50: this['parameters']['left']['g50'],
                            g80: this['parameters']['left']['g80'],
                            knee_low: this['parameters']['left']['knee_low'],
                            mpo_band: this['parameters']['left']['mpo_band'],
                            attack: this['parameters']['left']['attack'],
                            release: this['parameters']['left']['release'],
                            global_mpo: this['parameters']['left']['global_mpo'],
                            afc_delay: this['parameters']['left']['afc_delay']

                        },
                        right: {
                            en_ha: 1,
                            afc: this['parameters']['right']['afc'],
                            rear_mics: 0,
                            g50: this['parameters']['right']['g50'],
                            g80: this['parameters']['right']['g80'],
                            knee_low: this['parameters']['right']['knee_low'],
                            mpo_band: this['parameters']['right']['mpo_band'],
                            attack: this['parameters']['right']['attack'],
                            release: this['parameters']['right']['release'],
                            global_mpo: this['parameters']['right']['global_mpo'],
                            afc_delay: this['parameters']['right']['afc_delay']
                        }
                    }
                }),
                success: function(response){
                    console.log(response);
                    $('input[type=number]').css("background-color", "");
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
                    console.log(params);
                    //update parameters to reflect RTMHA state
                    parameters['left']['g50'] = params['left']['g50'];
                    parameters['left']['g80'] = params['left']['g80'];
                    parameters['left']['knee_low'] = params['left']['knee_low'];
                    parameters['left']['mpo_band'] = params['left']['mpo_band'];
                    parameters['left']['afc'] = params['left']['afc'];
                    parameters['left']['attack'] = params['left']['attack'];
                    parameters['left']['release'] = params['left']['release'];
                    parameters['left']['global_mpo'] = params['left']['global_mpo'];
                    parameters['left']['afc_delay'] = params['left']['afc_delay'];


                    parameters['right']['g50'] = params['right']['g50'];
                    parameters['right']['g80'] = params['right']['g80'];
                    parameters['right']['knee_low'] = params['right']['knee_low'];
                    parameters['right']['mpo_band'] = params['right']['mpo_band'];
                    parameters['right']['afc'] = params['right']['afc'];
                    parameters['right']['attack'] = params['right']['attack'];
                    parameters['right']['release'] = params['right']['release'];
                    parameters['right']['global_mpo'] = params['right']['global_mpo'];
                    parameters['right']['afc_delay'] = params['left']['afc_delay'];

                    //update inner html to reflect RTMHA state
                    for(i = 0; i < 6; i++){
                        document.getElementById("g50_"+i+"_l").value = parameters['left']['g50'][i];
                        document.getElementById("g80_"+i+"_l").value = parameters['left']['g80'][i];
                        document.getElementById("kneelow_"+i+"_l").value = parameters['left']['knee_low'][i];
                        document.getElementById("mpo_"+i+"_l").value = parameters['left']['mpo_band'][i];
                        document.getElementById("attack_"+i+"_l").value = parameters['left']['attack'][i];
                        document.getElementById("release_"+i+"_l").value = parameters['left']['release'][i];
                        controlParameters(i,"left");


                        document.getElementById("g50_"+i+"_r").value = parameters['right']['g50'][i];
                        document.getElementById("g80_"+i+"_r").value = parameters['right']['g80'][i];
                        document.getElementById("kneelow_"+i+"_r").value = parameters['right']['knee_low'][i];
                        document.getElementById("mpo_"+i+"_r").value = parameters['right']['mpo_band'][i];
                        document.getElementById("attack_"+i+"_r").value = parameters['right']['attack'][i];
                        document.getElementById("release_"+i+"_r").value = parameters['right']['release'][i];
                        controlParameters(i,"right");

                    }
                    
                    $('#afc_delay').val(parameters['left']['afc_delay']);
                    $('#global_mpo').val(parameters['left']['global_mpo']);

                    if(parameters['left']['afc'] == 0 || parameters['right']['afc'] == 0){
                        $('#afc_off').closest('.btn').button('toggle');
                        parameters['left']['afc'] = 0;
                        parameters['right']['afc'] = 0;
                    }
                    else{
                        $('#afc_on').closest('.btn').button('toggle');
                    }
                    $('input[type=number]').css("background-color", "");
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
