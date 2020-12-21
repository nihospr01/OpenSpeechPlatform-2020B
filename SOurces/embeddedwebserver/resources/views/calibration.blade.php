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
            color: black;
            font-size: 1rem;
        }

        table {
            border-collapse: collapse;
        }

        td {
            border-color: white;
            border-width: thick;
        }

        tr:nth-child(3n+1) {
            border-bottom: solid thick;
        }
    </style>


    <title>MHA Calibration</title>
</head>

<body>

    <div class="container">
        <div class="row align-items-center">
            <div class="col-6">
                <div class="card" style="width: 100%">
                    <div class="card-body">
                        <h5 class="card-title">MHA Calibration</h5>
                        <div class="row" style="margin-bottom: 10px">
                            <div class="col-12 col-md-4 col-align">
                                Channel:
                            </div>
                            <div class="col-12 col-md-8 col-align">
                                <div id="control_channel" class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-light active">
                                        <input type="radio" name="control_channel" id="channel_left" autocomplete="off"> Left
                                    </label>
                                    <label class="btn btn-light">
                                        <input type="radio" name="control_channel" id="channel_right" autocomplete="off"> Right
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="row align-items-center" style="margin: 1px">
                    <button id="btnGroupDrop1" type="button" class="btn btn-info btn-block dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        No calibration selected
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        @foreach($calibrations as $c)
                        <a id="calibration_{{$c->id}}" name="{{$c->name}}" class="dropdown-item" href="#" onclick='changeCalibration( "{{$c->id}}" , "{{$c->name}}" )'>{{$c->name}}</a>
                        @endforeach

                        @if(count($calibrations) == 0)
                        <a class="dropdown-item" href="#">No saved calibrations</a>
                        @endif
                    </div>
                </div>

                <div class="row align-items-center" style="margin: 1px">
                    <button type="button" class="btn btn-info btn-block" onclick="save()">Save</button>
                </div>
                <div class="row align-items-center" style="margin: 1px">
                    <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#saveAsModal">Save-as</button>
                </div>
                <div class="row align-items-center" style="margin: 1px">
                    <button type="button" class="btn btn-info btn-block" onclick="transmitUncalibrated()">Transmit Desired Parameters</button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <table class="table table-striped table-bordered table-sm" style="border-color: white">
                    <thead>
                        <th class="text-center" style="font-size: 2.5rem; border-color: white;  width:6rem;"></th>
                        <th class="text-center" style="font-size: 2.5rem; border-color: white"></th>
                        <th class="center-text" style="font-size: 1.5rem; border-color: white">0250</th>
                        <th class="center-text" style="font-size: 1.5rem; border-color: white">0500 </th>
                        <th class="center-text" style="font-size: 1.5rem; border-color: white">1000</th>
                        <th class="center-text" style="font-size: 1.5rem; border-color: white">2000</th>
                        <th class="center-text" style="font-size: 1.5rem; border-color: white">4000</th>
                        <th class="center-text" style="font-size: 1.5rem; border-color: white">8000</th>
                        <th class="center-text" style="font-size: 1.5rem; color:green; border-color: white">All</th>
                        <th class="center-text" style="font-size: 1.5rem; border-color: white; width: 2rem;">Calibrated</th>
                    </thead>

                    <tbody>
                        <tr>
                            <th style="font-size: 1rem; border-color: white">
                                <input type="radio" name="select_param" id="cr_select" value="cr" style="visibility: hidden">
                                <label for="cr_select">
                                    CR
                                </label></th>
                            </th>
                            <th class="text-center" style="font-size: 1rem; border-color: white"></th>
                            <td><input name="compression_ratio" data-index="0" id="cr_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="1"></td>
                            <td><input name="compression_ratio" data-index="1.4" id="cr_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="1"></td>
                            <td><input name="compression_ratio" data-index="2" id="cr_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="1"></td>
                            <td><input name="compression_ratio" data-index="3" id="cr_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="1"></td>
                            <td><input name="compression_ratio" data-index="4" id="cr_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="1"></td>
                            <td><input name="compression_ratio" data-index="5" id="cr_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="1"></td>
                            <td><input name="compression_ratio" data-index="6" id="cr_all" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="1"></td>
                        </tr>

                        <tr>
                            <th style="font-size: 1rem; border-color: white">
                                <input type="radio" name="select_param" id="g65_select" value="g65">
                                <label for="g65_select">
                                    G65
                                </label></th>
                            </th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">Desired</th>
                            <td><input name="g65_desired" data-index="0" id="g65_desired_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="g65_desired" data-index="1" id="g65_desired_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="g65_desired" data-index="2" id="g65_desired_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="g65_desired" data-index="3" id="g65_desired_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="g65_desired" data-index="4" id="g65_desired_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="g65_desired" data-index="5" id="g65_desired_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="g65_desired" data-index="6" id="g65_desired_all" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1rem; border-color: white"></th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">Measured</th>
                            <td><input name="g65_measured" data-index="0" id="g65_measured_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="g65_measured" data-index="1" id="g65_measured_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="g65_measured" data-index="2" id="g65_measured_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="g65_measured" data-index="3" id="g65_measured_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="g65_measured" data-index="4" id="g65_measured_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="g65_measured" data-index="5" id="g65_measured_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="g65_measured" data-index="6" id="g65_measured_all" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1rem; border-color: white"></th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">Subtraction</th>
                            <td><input name="g65_correction" data-index="0" id="g65_correction_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="" disabled></td>
                            <td><input name="g65_correction" data-index="1" id="g65_correction_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="" disabled></td>
                            <td><input name="g65_correction" data-index="2" id="g65_correction_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="" disabled></td>
                            <td><input name="g65_correction" data-index="3" id="g65_correction_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="" disabled></td>
                            <td><input name="g65_correction" data-index="4" id="g65_correction_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="" disabled></td>
                            <td><input name="g65_correction" data-index="5" id="g65_correction_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="" disabled></td>
                            <td></td>
                            <td class="text-center"><input type="checkbox" name="corrected_param" id="g65_corrected" value="g65" check="faslse" disabled></td>
                        </tr>

                        <tr>
                            <th style="font-size: 1rem; border-color: white">
                                <input type="radio" name="select_param" id="mpo_select" value="mpo_band">
                                <label for="mpo_select">
                                    MPO
                                </label>
                            </th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">Desired</th>
                            <td><input name="mpo_band_desired" data-index="0" id="mpo_band_desired_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="mpo_band_desired" data-index="1" id="mpo_band_desired_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="mpo_band_desired" data-index="2" id="mpo_band_desired_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="mpo_band_desired" data-index="3" id="mpo_band_desired_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="mpo_band_desired" data-index="4" id="mpo_band_desired_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="mpo_band_desired" data-index="5" id="mpo_band_desired_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="mpo_band_desired" data-index="6" id="mpo_band_desired_all" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1rem; border-color: white"></th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">Measured</th>
                            <td><input name="mpo_band_measured" data-index="0" id="mpo_band_measured_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="mpo_band_measured" data-index="1" id="mpo_band_measured_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="mpo_band_measured" data-index="2" id="mpo_band_measured_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="mpo_band_measured" data-index="3" id="mpo_band_measured_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="mpo_band_measured" data-index="4" id="mpo_band_measured_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="mpo_band_measured" data-index="5" id="mpo_band_measured_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                            <td><input name="mpo_band_measured" data-index="6" id="mpo_band_measured_all" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value=""></td>
                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1rem; border-color: white"></th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">Subtraction</th>
                            <td><input name="mpo_band_correction" data-index="0" id="mpo_band_correction_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="" disabled></td>
                            <td><input name="mpo_band_correction" data-index="1" id="mpo_band_correction_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="" disabled></td>
                            <td><input name="mpo_band_correction" data-index="2" id="mpo_band_correction_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="" disabled></td>
                            <td><input name="mpo_band_correction" data-index="3" id="mpo_band_correction_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="" disabled></td>
                            <td><input name="mpo_band_correction" data-index="4" id="mpo_band_correction_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="" disabled></td>
                            <td><input name="mpo_band_correction" data-index="5" id="mpo_band_correction_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" value="" disabled></td>
                            <td></td>
                            <td class="text-center"><input type="checkbox" name="corrected_param" id="g65_corrected" value="mpo_band" checked="faslse" disabled></td>
                        </tr>

                        <tr>
                            <th style="font-size: 1rem; border-color: white">
                                <input type="radio" name="select_param" id="attack_select" value="attack">
                                <label for="attack_select">
                                    Attack
                                </label>
                            </th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">Desired</th>
                            <td><input name="attack_desired" data-index="0" id="attack_desired_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="attack_desired" data-index="1" id="attack_desired_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="attack_desired" data-index="2" id="attack_desired_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="attack_desired" data-index="3" id="attack_desired_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="attack_desired" data-index="4" id="attack_desired_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="attack_desired" data-index="5" id="attack_desired_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="attack_desired" data-index="6" id="attack_desired_all" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1rem; border-color: white"></th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">Measured</th>
                            <td><input name="attack_measured" data-index="0" id="attack_measured_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="attack_measured" data-index="1" id="attack_measured_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="attack_measured" data-index="2" id="attack_measured_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="attack_measured" data-index="3" id="attack_measured_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="attack_measured" data-index="4" id="attack_measured_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="attack_measured" data-index="5" id="attack_measured_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="attack_measured" data-index="6" id="attack_measured_all" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1rem; border-color: white"></th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">Division</th>
                            <td><input name="attack_correction" data-index="0" id="attack_correction_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" disabled></td>
                            <td><input name="attack_correction" data-index="1" id="attack_correction_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" disabled></td>
                            <td><input name="attack_correction" data-index="2" id="attack_correction_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" disabled></td>
                            <td><input name="attack_correction" data-index="3" id="attack_correction_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" disabled></td>
                            <td><input name="attack_correction" data-index="4" id="attack_correction_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" disabled></td>
                            <td><input name="attack_correction" data-index="5" id="attack_correction_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" disabled></td>
                            <td></td>
                            <td class="text-center"><input type="checkbox" name="corrected_param" id="attack_corrected" value="attack" checked="faslse" disabled></td>
                        </tr>

                        <tr>
                            <th style="font-size: 1rem; border-color: white">
                                <input type="radio" name="select_param" id="release_select" value="release">
                                <label for="release_select">
                                    Release
                                </label>
                            </th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">Desired</th>
                            <td><input name="release_desired" data-index="0" id="release_desired_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="release_desired" data-index="1" id="release_desired_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="release_desired" data-index="2" id="release_desired_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="release_desired" data-index="3" id="release_desired_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="release_desired" data-index="4" id="release_desired_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="release_desired" data-index="5" id="release_desired_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="release_desired" data-index="6" id="release_desired_all" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1rem; border-color: white"></th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">Measured</th>
                            <td><input name="release_measured" data-index="0" id="release_measured_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="release_measured" data-index="1" id="release_measured_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="release_measured" data-index="2" id="release_measured_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="release_measured" data-index="3" id="release_measured_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="release_measured" data-index="4" id="release_measured_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="release_measured" data-index="5" id="release_measured_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="release_measured" data-index="6" id="release_measured_all" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1rem; border-color: white"></th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">Division</th>
                            <td><input name="release_correction" data-index="0" id="release_correction_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" disabled></td>
                            <td><input name="release_correction" data-index="1" id="release_correction_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" disabled></td>
                            <td><input name="release_correction" data-index="2" id="release_correction_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" disabled></td>
                            <td><input name="release_correction" data-index="3" id="release_correction_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" disabled></td>
                            <td><input name="release_correction" data-index="4" id="release_correction_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" disabled></td>
                            <td><input name="release_correction" data-index="5" id="release_correction_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" disabled></td>
                            <td></td>
                            <td class="text-center"><input type="checkbox" name="corrected_param" id="release_corrected" value="release" checked="faslse" disabled></td>
                        </tr>

                        <tr>
                            <th style="font-size: 1rem; border-color: white">
                                <input type="radio" name="select_param" id="knee_select" value="knee_low">
                                <label for="knee_select">
                                    Knee
                                </label>
                            </th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">Desired</th>
                            <td><input name="knee_low_desired" data-index="0" id="knee_low_desired_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="knee_low_desired" data-index="1" id="knee_low_desired_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="knee_low_desired" data-index="2" id="knee_low_desired_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="knee_low_desired" data-index="3" id="knee_low_desired_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="knee_low_desired" data-index="4" id="knee_low_desired_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="knee_low_desired" data-index="5" id="knee_low_desired_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="knee_low_desired" data-index="6" id="knee_low_desired_all" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1rem; border-color: white"></th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">Measured</th>
                            <td><input name="knee_low_measured" data-index="0" id="knee_low_measured_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="knee_low_measured" data-index="1" id="knee_low_measured_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="knee_low_measured" data-index="2" id="knee_low_measured_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="knee_low_measured" data-index="3" id="knee_low_measured_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="knee_low_measured" data-index="4" id="knee_low_measured_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="knee_low_measured" data-index="5" id="knee_low_measured_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                            <td><input name="knee_low_measured" data-index="6" id="knee_low_measured_all" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1"></td>
                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1rem; border-color: white"></th>
                            <th class="text-center" style="font-size: 1rem; border-color: white">Subtraction</th>
                            <td><input name="knee_low_correction" data-index="0" id="knee_low_correction_0" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" disabled></td>
                            <td><input name="knee_low_correction" data-index="1" id="knee_low_correction_1" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" disabled></td>
                            <td><input name="knee_low_correction" data-index="2" id="knee_low_correction_2" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" disabled></td>
                            <td><input name="knee_low_correction" data-index="3" id="knee_low_correction_3" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" disabled></td>
                            <td><input name="knee_low_correction" data-index="4" id="knee_low_correction_4" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" disabled></td>
                            <td><input name="knee_low_correction" data-index="5" id="knee_low_correction_5" class="form-control form-control-sm table-field font-weight-bold" type="number" step="0.1" disabled></td>
                            <td></td>
                            <td class="text-center"><input type="checkbox" name="corrected_param" id="knee_low_corrected" value="knee_low" checked="faslse" disabled></td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

        <!--Modal-->
        <div class="modal fade" id="saveAsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Save New Calibration</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <label for="modalInput" style="padding-right: 5px">Calibration name </label>
                        <input id="modalInput" name="calibrationName" type="text" placeholder="">
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
    </div>

    <script language="JavaScript">
        var desiredParameters = {
            'left': {
                'compression_ratio': [1, 1, 1, 1, 1, 1],
                'g65': [0, 0, 0, 0, 0, 0],
                'knee_low': [0, 0, 0, 0, 0, 0],
                'mpo_band': [0, 0, 0, 0, 0, 0],
                'attack': [1, 1, 1, 1, 1, 1],
                'release': [1, 1, 1, 1, 1, 1],
            },
            'right': {
                'compression_ratio': [1, 1, 1, 1, 1, 1],
                'g65': [0, 0, 0, 0, 0, 0],
                'knee_low': [0, 0, 0, 0, 0, 0],
                'mpo_band': [0, 0, 0, 0, 0, 0],
                'attack': [1, 1, 1, 1, 1, 1],
                'release': [1, 1, 1, 1, 1, 1],
            },
        };
        var measuredParameters = {
            'left': {
                'g65': [0, 0, 0, 0, 0, 0],
                'knee_low': [0, 0, 0, 0, 0, 0],
                'mpo_band': [0, 0, 0, 0, 0, 0],
                'attack': [1, 1, 1, 1, 1, 1],
                'release': [1, 1, 1, 1, 1, 1],
            },
            'right': {
                'g65': [0, 0, 0, 0, 0, 0],
                'knee_low': [0, 0, 0, 0, 0, 0],
                'mpo_band': [0, 0, 0, 0, 0, 0],
                'attack': [1, 1, 1, 1, 1, 1],
                'release': [1, 1, 1, 1, 1, 1],
            },
        };
        var correctionParameters = {
            'left': {
                'g65': [0, 0, 0, 0, 0, 0],
                'knee_low': [0, 0, 0, 0, 0, 0],
                'mpo_band': [0, 0, 0, 0, 0, 0],
                'attack': [1, 1, 1, 1, 1, 1],
                'release': [1, 1, 1, 1, 1, 1],
            },
            'right': {
                'g65': [0, 0, 0, 0, 0, 0],
                'knee_low': [0, 0, 0, 0, 0, 0],
                'mpo_band': [0, 0, 0, 0, 0, 0],
                'attack': [1, 1, 1, 1, 1, 1],
                'release': [1, 1, 1, 1, 1, 1],
            }
        };

        var calibratedParameters = {
            'left': {
                'g65': 0,
                'knee_low': 0,
                'mpo_band': 0,
                'attack': 0,
                'release': 0,
            },
            'right': {
                'g65': 0,
                'knee_low': 0,
                'mpo_band': 0,
                'attack': 0,
                'release': 0,
            }
        }

        var channel = "left";
        var selectedCalibration = -1;
        var selectedParam = "";
        var calibratedParam = "";

        var dc = {!!$defaultCalibration!!};
        if (dc != -1) {
            changeCalibration(dc["id"], dc["name"]);
        }


        // NO DEFAULT CALIBRATION
        // DONT NEED TO UPDATE CALIBRATION AND MEASURED VALUE
        updateParamTable(channel);
        updateCalibratedParameters();


        //input field control
        $('input[type=number]').keyup(function(event) {
            if(selectedParam == ""){
                alert("Please first select parameter to calibrate");
                event.target.value = "";
                return;
            }
            var elem = event.target;
            var value = Math.round(Number(elem.value) * 10) / 10; // round to 1 d.p.
            var currentElemArray = document.getElementsByName(elem.name);
            //"All" input field
            if (elem.dataset.index == 6) {
                for (var i = 0; i < 6; i++) {
                    updateSingleInputBlock(elem.name, i, value);
                }
            } else {
                updateSingleInputBlock(elem.name, elem.dataset.index, value);
            }
        }).click(function() {
            $(this).select();
        });


        function fixPrecision(value) {
            return Math.round(Number(value) * 10) / 10;
        }
        
        // update correction, and input for one param block
        function updateSingleInputBlock(elemName, index, value){
        
            var paramName = elemName.substr(0, elemName.lastIndexOf("_"));
            var currentElemArray = document.getElementsByName(elemName);
            var correctionArray = document.getElementsByName(paramName + "_correction");

            //update HTML for input value
            currentElemArray[index].value = value;
            // currentElemArray[i].style.backgroundColor = "#FEEEBA";
            // updateParam
            if (elemName.includes("_desired")) {
                desiredParameters[channel][paramName][index] = value;
            } else if (elemName.includes("_measured")) {
                measuredParameters[channel][paramName][index] = value;
            }

            // update correction
            // no correction for cr
            if(elemName.includes("compression_ratio")){
                return;
            }
            var correction = "";
            if(measuredParameters[channel][paramName][index] != "" && desiredParameters[channel][paramName][index] != "" ){
                if (elemName.includes("attack") || elemName.includes("release")) {
                    correction = fixPrecision(measuredParameters[channel][paramName][index] / desiredParameters[channel][paramName][index]);
                }else{
                    correction = fixPrecision(measuredParameters[channel][paramName][index] - desiredParameters[channel][paramName][index]);
                }
            }
            correctionArray[index].value = correction;
            correctionParameters[channel][paramName][index] = correction;

        }

        $("#control_channel :input[type=radio]").change(function() {
            saveAllParameters(channel);
            switch (this.id) {
                case "channel_left":
                    channel = "left"
                    break;
                case "channel_right":
                    channel = "right"
                    break;
            }
            updateCalibratedParameters();
            updateParamTable(channel);
        });

        // disable other input box for not selected param
        $("input[name=select_param]").change(function(event) {
            selectedParam = $("input[name=select_param]:checked").val();
            console.log('select:' + selectedParam);

            for (var i = 0; i < 6; i++) {
                var elements = ['g65', 'mpo_band', 'knee_low', 'attack', 'release'];
                for (const e of elements) {
                    if (e == selectedParam) {
                        document.getElementById(e + "_measured_" + i).disabled = false;
                        document.getElementById(e + "_desired_" + i).disabled = false;
                        document.getElementById(e + "_measured_all").disabled = false;
                        document.getElementById(e + "_desired_all").disabled = false;
                    } else {
                        document.getElementById(e + "_measured_" + i).disabled = true;
                        document.getElementById(e + "_desired_" + i).disabled = true;
                        document.getElementById(e + "_measured_all").disabled = true;
                        document.getElementById(e + "_desired_all").disabled = true;
                    }
                }

            }
        });

        function saveAllParameters(channel) {
            for (var i = 0; i < 6; i++) {
                // cr
                var cr = parseFloat(document.getElementById("cr_" + i).value);
                desiredParameters[channel]['compression_ratio'][i] = cr;

                var elements = ['g65', 'mpo_band', 'knee_low', 'attack', 'release'];
                for (const e of elements) {
                    var desired = parseFloat(document.getElementById(e + "_desired_" + i).value);
                    desiredParameters[channel][e][i] = desired;

                    var measured = parseFloat(document.getElementById(e + "_measured_" + i).value);
                    measuredParameters[channel][e][i] = measured;

                    var correction = parseFloat(document.getElementById(e + "_correction_" + i).value);
                    correctionParameters[channel][e][i] = correction;
                }

            }

        }

        function updateCalibratedParameters() {
            var checkBtns = document.getElementsByName("corrected_param");
            for (var i = 0; i < 5; i++) {
                checkBtns[i].checked = calibratedParameters[channel][checkBtns[i].value];
            }
        }

        // update substraction
        // function updateAllCorrections() {
        //     var elements = ['g65_', 'mpo_band_', 'knee_low_', 'attack_', 'release_'];
        //     elements.forEach(updateCorrections);
        // }

        // function updateCorrections(elemName) {
        //     var category = elemName.substr(0, elemName.lastIndexOf("_"));
        //     var desiredArray = document.getElementsByName(category + "_desired");
        //     var measuredArray = document.getElementsByName(category + "_measured");
        //     var correctionArray = document.getElementsByName(category + "_correction");
        //     for (var i = 0; i < 6; i++) {
        //         var correction;
        //         if (measuredArray[i].value == "") {
        //             correctionArray[i].value = "";
        //             continue;
        //         }
        //         // divide for correctness
        //         if (elemName.includes("attack") || elemName.includes("release")) {
        //             correction = measuredArray[i].value / desiredArray[i].value;
        //         }
        //         // subtract for corrections
        //         else {
        //             correction = measuredArray[i].value - desiredArray[i].value;
        //             console.log(correction);
        //         }

        //         console.log(correction);

        //         correctionArray[i].value = correction;
        //         correctionParameters[channel][elemName.substr(0, elemName.lastIndexOf("_"))][i] = correction;
        //     }
        // }

        // update the inner HTML value for all inputs
        function updateParamTable(channel) {
            for (var i = 0; i < 6; i++) {
                $("#cr_" + i).val(desiredParameters[channel]['compression_ratio'][i]);
                $("#g65_desired_" + i).val(selectedParam === 'g65' ? desiredParameters[channel]['g65'][i] : "");
                $("#knee_low_desired_" + i).val(selectedParam === 'knee_low'?desiredParameters[channel]['knee_low'][i]:"");
                $("#mpo_band_desired_" + i).val(selectedParam === 'mpo_band'? desiredParameters[channel]['mpo_band'][i]:"");
                $("#attack_desired_" + i).val(selectedParam === 'attack' ?desiredParameters[channel]['attack'][i]:"");
                $("#release_desired_" + i).val(selectedParam === 'release'?desiredParameters[channel]['release'][i]:"");
                // controlParameters(i, "desired")
                $("#g65_measured_" + i).val(selectedParam === 'g65' ? measuredParameters[channel]['g65'][i]:"");
                $("#knee_low_measured_" + i).val(selectedParam === 'knee_low' ? measuredParameters[channel]['knee_low'][i]:"");
                $("#mpo_band_measured_" + i).val(selectedParam === 'mpo_band' ? measuredParameters[channel]['mpo_band'][i]:"");
                $("#attack_measured_" + i).val(selectedParam === 'attack' ? measuredParameters[channel]['attack'][i]:"");
                $("#release_measured_" + i).val(selectedParam === 'release' ?measuredParameters[channel]['release'][i]:"");
                // controlParameters(i, "measured")
                $("#g65_correction_" + i).val(selectedParam === 'g65' || calibratedParameters[channel]['g65']==1?correctionParameters[channel]['g65'][i]:"");
                $("#knee_low_correction_" + i).val(selectedParam === 'knee_low' || calibratedParameters[channel]['knee_low']==1?correctionParameters[channel]['knee_low'][i]:"");
                $("#mpo_band_correction_" + i).val(selectedParam === 'mpo_band' || calibratedParameters[channel]['mpo_band']==1?correctionParameters[channel]['mpo_band'][i]:"");
                $("#attack_correction_" + i).val(selectedParam === 'attack' || calibratedParameters[channel]['attack']==1?correctionParameters[channel]['attack'][i]:"");
                $("#release_correction_" + i).val(selectedParam === 'release' || calibratedParameters[channel]['release']==1?correctionParameters[channel]['release'][i]:"");
            }
            // clear "All" input value
            $("#cr_all").val("");
            $("#g65_desired_all").val("");
            $("#g65_measured_all").val("");
            $("#knee_low_desired_all").val("");
            $("#knee_low_measured_all").val("");
            $("#mpo_band_desired_all").val("");
            $("#mpo_band_measured_all").val("");
            $("#attack_desired_all").val("");
            $("#attack_measured_all").val("");
            $("#release_desired_all").val("");
            $("#release_measured_all").val("");

            // $('input[type=number]').css("background-color", "");
        }


        /**
         * Change the values of the inner HTML elements and javascript parameters based on the calibration selected from
         * the dropdown menu.
         *
         * @param id
         * @param cName
         */
        function changeCalibration(id, cName) {
            console.log('change calibration to:', id);
            this["selectedCalibration"] = id;
            document.getElementById("btnGroupDrop1").innerHTML = "Current Calibration: " + cName;
            var allCalibrations = {!!$calibrations!!};
            var calibration = allCalibrations.filter(
                element => element.id == id
            );

            var newCalibration = JSON.parse(calibration[0]['parameters']);
            var calibratedParams = JSON.parse(calibration[0]['calibratedParams']);

            this['correctionParameters'] = newCalibration;
            this['calibratedParameters'] = calibratedParams;
            // console.log('new calibration:' + this['calibratedParameters']);

            //recalculateMeasuredParameters();
            updateParamTable(channel);
            updateCalibratedParameters();

            $.ajax({
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                url: '/calibration/default',
                data: JSON.stringify([
                    id
                ]),
                success: function(response) {
                    console.log(JSON.parse(response));
                    if (JSON.parse(response)['status'] == "failure") {
                        alert("[FAILURE] Setting default calibration unsuccessful");
                    } else {
                        console.log("Default calibration set to: " + id);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    alert("[FAILURE] changeCalibration() setting calibration: " + errorThrown);
                }
            });
        }

        /**
         * Recalculates the measured parameters section using the desired parameters and the saved correction parameters.
         */
        function recalculateMeasuredParameters() {
            var channels = ["left", "right"];
            var subElements = ['g65', 'mpo_band', 'knee_low'];
            var mulElements = ['attack', 'release'];

            for (let i = 0; i < 6; i++) {
                for (let c of channels) {
                    for (let e of subElements) {
                        measuredParameters[c][e][i] = desiredParameters[c][e][i] + correctionParameters[c][e][i];
                    }
                    for (let e of mulElements) {
                        measuredParameters[c][e][i] = desiredParameters[c][e][i] * correctionParameters[c][e][i];
                    }
                }
            }
        }

        /**
         * Overrite current calibration with new values. If no program has been selected user is prompted to Save As
         * instead.
         */
        function save() {
            console.log("Save");
            if (selectedParam != calibratedParam) {
                console.log("Error: selectedParam is not calibrated");
                alert("Transmit and calibrate the selected parameter before save");
                return;
            }

            if (this['selectedCalibration'] != -1) {
                calibratedParameters[channel][selectedParam] = 1;
                //ajax request to save program
                $.ajax({
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    url: '/calibration',
                    data: JSON.stringify([
                        this['selectedCalibration'],
                        this['correctionParameters'],
                        calibratedParameters
                    ]),
                    success: function(response) {
                        console.log(response);
                        alert('Program saved.');
                        updateCalibratedParameters();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        calibratedParameters[channel][selectedParam] = 0;
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
            // data_left = {
            //     [selectedParam]:correctionParameters['left'][selectedParam]
            // };
            // data_right = {
            //     [selectedParam]:correctionParameters['right'][selectedParam]
            // }

            if (selectedParam != calibratedParam) {
                console.log("Error: selectedParam is not calibrated");
                alert("Transmit and calibrate the selected parameter before save as");
                return;
            }


            if (document.getElementById("modalInput").value == '') {
                document.getElementById("modalInputError").innerHTML = "Must include a name";
                console.log("Error: input is empty");
            } else {
                calibratedParameters[channel][selectedParam] = 1;
                $.ajax({
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    url: '/calibration',
                    data: JSON.stringify([
                        document.getElementById("modalInput").value,
                        this['correctionParameters'],
                        calibratedParameters
                    ]),
                    success: function(response) {
                        console.log(JSON.parse(response));
                        if (JSON.parse(response)['status'] == "failure") {
                            document.getElementById("modalInputError").innerHTML = "The name \"" + (document.getElementById("modalInput").value) + "\" is already taken.";
                        } else {
                            $('#saveAsModal').modal('hide');
                            alert("New program saved. Refreshing the web page.");
                            updateCalibratedParameters();
                            location.reload();

                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        calibratedParameters[channel][selectedParam] = 0;
                        document.getElementById("modalInputError").innerHTML = "There was an error with your request.";
                    }
                });
            }
        }

        /**
         * Transmit the uncalibrated desired parameters by making a POST request on api/paramsUncalibrated
         *
         */
        function transmitUncalibrated() {
            console.log("Attempting to transmit uncalibrated");
            var genericParameters = {!!$genericParameters!!};
            var data_left, data_right;
            if (!selectedParam) {
                console.log("Error: selectedParam is empty");
                alert("Must select one param to transmit");
                return;
            }
            if (selectedParam == 'g65') {

                data_left = {
                    g50: desiredParameters['left']['g65'],
                    g80: desiredParameters['left']['g65'],
                };
                data_right = {
                    g50: desiredParameters['right']['g65'],
                    g80: desiredParameters['right']['g65'],
                }
            } else if (selectedParam == 'knee_low'){
                data_left = {
	                g50: [20,20,20,20,20,20],
                    g80: [0,0,0,0,0,0],
                    spl_calibartion: [100,100,100,100,100,100],
                    knee_low: desiredParameters['left']['knee_low'],
                };
                data_right = {
                    g50: [20,20,20,20,20,20],
                    g80: [0,0,0,0,0,0],
                    spl_calibartion: [100,100,100,100,100,100],
                    knee_low: desiredParameters['right']['knee_low'],
                }
            } else if (selectedParam == 'mpo_band'){
                data_left = {
                    g50: [15,15,15,15,15,15],
                    g80: [15,15,15,15,15,15],
                    attack: [5,5,5,5,5,5],
                    release: [20,20,20,20,20,20],
                    mpo_band: desiredParameters['left']['mpo_band']
                };
                data_right = {
                    g50: [15,15,15,15,15,15],
                    g80: [15,15,15,15,15,15],
                    attack: [5,5,5,5,5,5],
                    release: [20,20,20,20,20,20],
                    mpo_band: desiredParameters['right']['mpo_band']
                }

            } else {
                data_left = {
                    [selectedParam]: desiredParameters['left'][selectedParam]
                };
                data_right = {
                    [selectedParam]: desiredParameters['right'][selectedParam]
                }
            }
            if(selectedParam == 'mpo_band'){
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
                            afc: 0,
                            rear_mics: 0,
                            // since cr = 1, g50 = g80 = g65
                            // ...data_left,
                            ...data_left,
                            // g50: desiredParameters['left']['g65'],
                            // g80: desiredParameters['left']['g65'],
                            // knee_low: desiredParameters['left']['knee_low'],
                            // mpo_band: desiredParameters['left']['mpo_band'],
                            // attack: desiredParameters['left']['attack'],
                            // release: desiredParameters['left']['release'],
                            //gain: genericParameters.gain
                        },
                        right: {
                            en_ha: 1,
                            afc: 0,
                            rear_mics: 0,
                            ...data_right,
                            //since cr = 1, g50 = g80 = g65
                            // g50: desiredParameters['right']['g65'],
                            // g80: desiredParameters['right']['g65'],
                            // knee_low: desiredParameters['right']['knee_low'],
                            // mpo_band: desiredParameters['right']['mpo_band'],
                            // attack: desiredParameters['right']['attack'],
                            // release: desiredParameters['right']['release'],
                            //gain: genericParameters.gain
                        }
                    }
                }),
                success: function(response) {
                    console.log(response);
                    console.log('success');
                    alert("Selected parameter is transmitted");
                    calibratedParam = selectedParam;
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(JSON.stringify(jqXHR));
                    console.log('error')
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    alert("[ERROR] transmitUncalibrated(): " + errorThrown);
                }
                });
            } else {
                $.ajax({
                method: 'POST',
                url: '/api/paramsUncalibrated',
                data: JSON.stringify({
                    user_id: this['listener'],
                    method: "set",
                    request_action: 1,
                    data: {
                        left: {
                            en_ha: 1,
                            afc: 0,
                            rear_mics: 0,
                            // since cr = 1, g50 = g80 = g65
                            // ...data_left,
                            ...data_left,
                            // g50: desiredParameters['left']['g65'],
                            // g80: desiredParameters['left']['g65'],
                            // knee_low: desiredParameters['left']['knee_low'],
                            // mpo_band: desiredParameters['left']['mpo_band'],
                            // attack: desiredParameters['left']['attack'],
                            // release: desiredParameters['left']['release'],
                            //gain: genericParameters.gain
                        },
                        right: {
                            en_ha: 1,
                            afc: 0,
                            rear_mics: 0,
                            ...data_right,
                            //since cr = 1, g50 = g80 = g65
                            // g50: desiredParameters['right']['g65'],
                            // g80: desiredParameters['right']['g65'],
                            // knee_low: desiredParameters['right']['knee_low'],
                            // mpo_band: desiredParameters['right']['mpo_band'],
                            // attack: desiredParameters['right']['attack'],
                            // release: desiredParameters['right']['release'],
                            //gain: genericParameters.gain
                        }
                    }
                }),
                success: function(response) {
                    console.log(response);
                    console.log('success');
                    alert("Selected parameter is transmitted");
                    calibratedParam = selectedParam;
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(JSON.stringify(jqXHR));
                    console.log('error')
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    alert("[ERROR] transmitUncalibrated(): " + errorThrown);
                }
                });
            }
            
        }
    </script>

</body>

</html>
