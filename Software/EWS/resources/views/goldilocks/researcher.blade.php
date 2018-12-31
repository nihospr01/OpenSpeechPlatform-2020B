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
        {{--<nav aria-label="breadcrumb">--}}
            {{--<ol class="breadcrumb">--}}
                {{--<li class="breadcrumb-item"><a href="{{ url('/goldilocks') }}">Goldilocks</a></li>--}}
                {{--<li class="breadcrumb-item active" aria-current="page">Researcher Page</li>--}}
            {{--</ol>--}}
        {{--</nav>--}}


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
            <div class="col-3">
                <div class="row align-items-center">
                    Control via:
                </div>
                <div class="row align-items-center">
                    <div id="control_via_buttons" class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-light active">
                            <input type="radio" name="control_via" id="cr_g65"  autocomplete="off" checked> CR/G65
                        </label>
                        <label class="btn btn-light">
                            <input type="radio" name="control_via" id="g50_g80"  autocomplete="off"> G50/G80
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-3 ">
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
                <div class="row align-items-center" style="margin: 1px">
                    <button id="btnGroupDrop1" type="button" class="btn btn-info btn-block dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Read
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        @foreach($programs as $program)
                            <a id="{{$program->id}}" name="{{$program->name}}" class="dropdown-item" href="#" onclick="changeProgram( {{$program->id}} , this.name )">{{$program->name}}</a>
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



        <div class="row">
            <div class="col-12">
                <table class="table table-striped table-bordered table-sm" style="border-color: white">
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
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="cr_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="1" onchange="changeColor(this.id)" onblur="cr(0)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="cr_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="1" onchange="changeColor(this.id)" onblur="cr(1)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="cr_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="1" onchange="changeColor(this.id)" onblur="cr(2)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="cr_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="1" onchange="changeColor(this.id)" onblur="cr(3)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="cr_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="1" onchange="changeColor(this.id)" onblur="cr(4)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="cr_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="1" onchange="changeColor(this.id)" onblur="cr(5)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="cr_all" class="form-control form-control-sm table-field font-weight-bold" type="number" value="1" onchange="changeColor(this.id)"></td>
                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">G50</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g50_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g50(0)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g50_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g50(1)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g50_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g50(2)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g50_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g50(3)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g50_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g50(4)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g50_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g50(5)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g50_all" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">G65</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g65_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g65(0)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g65_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g65(1)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g65_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g65(2)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g65_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g65(3)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g65_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g65(4)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g65_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g65(5)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g65_all" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">G80</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g80_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g80(0)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g80_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g80(1)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g80_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g80(2)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g80_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g80(3)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g80_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g80(4)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g80_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="g80(5)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="g80_all" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)"></td>
                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">Knee</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="kneelow_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="45" onchange="changeColor(this.id)" onblur="kneelow(0)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="kneelow_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="45" onchange="changeColor(this.id)" onblur="kneelow(1)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="kneelow_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="45" onchange="changeColor(this.id)" onblur="kneelow(2)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="kneelow_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="45" onchange="changeColor(this.id)" onblur="kneelow(3)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="kneelow_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="45" onchange="changeColor(this.id)" onblur="kneelow(4)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="kneelow_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="45" onchange="changeColor(this.id)" onblur="kneelow(5)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="kneelow_all" class="form-control form-control-sm table-field font-weight-bold" type="number" value="45" onchange="changeColor(this.id)"></td>

                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">MPO</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="mpo_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="110" onchange="changeColor(this.id)" onblur="mpo(0)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="mpo_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="110" onchange="changeColor(this.id)" onblur="mpo(1)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="mpo_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="110" onchange="changeColor(this.id)" onblur="mpo(2)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="mpo_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="110" onchange="changeColor(this.id)" onblur="mpo(3)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="mpo_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="110" onchange="changeColor(this.id)" onblur="mpo(4)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="mpo_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="110" onchange="changeColor(this.id)" onblur="mpo(5)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="mpo_all" class="form-control form-control-sm table-field font-weight-bold" type="number" value="110" onchange="changeColor(this.id)"></td>

                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">Attack</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="attack_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="5" onchange="changeColor(this.id)" onblur="attack(0)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="attack_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="5" onchange="changeColor(this.id)" onblur="attack(1)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="attack_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="5" onchange="changeColor(this.id)" onblur="attack(2)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="attack_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="5" onchange="changeColor(this.id)" onblur="attack(3)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="attack_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="5" onchange="changeColor(this.id)" onblur="attack(4)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="attack_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="5" onchange="changeColor(this.id)" onblur="attack(5)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="attack_all" class="form-control form-control-sm table-field font-weight-bold" type="number" value="5" onchange="changeColor(this.id)"></td>

                        </tr>

                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">Release</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="release_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="100" onchange="changeColor(this.id)" onblur="release(0)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="release_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="100" onchange="changeColor(this.id)" onblur="release(1)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="release_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="100" onchange="changeColor(this.id)" onblur="release(2)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="release_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="100" onchange="changeColor(this.id)" onblur="release(3)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="release_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="100" onchange="changeColor(this.id)" onblur="release(4)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="release_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="100" onchange="changeColor(this.id)" onblur="release(5)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="release_all" class="form-control form-control-sm table-field font-weight-bold" type="number" value="100" onchange="changeColor(this.id)"></td>
                        </tr>

                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">Targets</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="targets_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="targets(0)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="targets_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="targets(1)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="targets_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="targets(2)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="targets_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="targets(3)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="targets_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="targets(4)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="targets_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="targets(5)"></td>
                            <td>
                                <button type="button" class="btn btn-info btn-block" onclick="calcg65()">Set</button>
                            </td>

                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">LTASS</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="ltass_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="ltass(0)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="ltass_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="ltass(1)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="ltass_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="ltass(2)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="ltass_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="ltass(3)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="ltass_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="ltass(4)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="ltass_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="ltass(5)"></td>
                            <td>
                                <button type="button" class="btn btn-info btn-block" onclick="monitorValues()">Monitor</button>
                            </td>

                        </tr>
                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">Thresh</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="hl_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="hl(0)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="hl_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="hl(1)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="hl_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="hl(2)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="hl_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="hl(3)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="hl_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="hl(4)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="hl_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="hl(5)"></td>
                        </tr>

                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">L Mult</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multiL_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="multiL(0)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multiL_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="multiL(1)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multiL_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="multiL(2)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multiL_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="multiL(3)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multiL_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="multiL(4)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multiL_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="multiL(5)"></td>
                        </tr>

                        <tr>
                            <th class="text-center" style="font-size: 1.5rem; border-color: white">H Mult</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multiH_0" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="multiH(0)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multiH_1" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="multiH(1)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multiH_2" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="multiH(2)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multiH_3" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="multiH(3)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multiH_4" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="multiH(4)"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="multiH_5" class="form-control form-control-sm table-field font-weight-bold" type="number" value="0" onchange="changeColor(this.id)" onblur="multiH(5)"></td>
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
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="stepL" class="form-control form-control-sm table-field font-weight-bold" type="number" value="1" onchange="changeColor(this.id)" onblur="stepL()"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="stepV" class="form-control form-control-sm table-field font-weight-bold" type="number" value="3" onchange="changeColor(this.id)" onblur="stepV()"></td>
                        <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="stepH" class="form-control form-control-sm table-field font-weight-bold" type="number" value="1" onchange="changeColor(this.id)" onblur="stepH()"></td>
                    </tr>
                    <tr>
                            <td class="text-center" style="font-size: 2.0rem; border-color: white">Min</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="minL" class="form-control form-control-sm table-field font-weight-bold" type="number" value="-40" onchange="changeColor(this.id)" onblur="minL()"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="minV" class="form-control form-control-sm table-field font-weight-bold" type="number" value="-40" onchange="changeColor(this.id)" onblur="minV()"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="minH" class="form-control form-control-sm table-field font-weight-bold" type="number" value="-40" onchange="changeColor(this.id)" onblur="minH()"></td>
                        </tr>
                        <tr>
                            <td class="text-center" style="font-size: 2.0rem; border-color: white">Max</th>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="maxL" class="form-control form-control-sm table-field font-weight-bold" type="number" value="40" onchange="changeColor(this.id)" onblur="maxL()"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="maxV" class="form-control form-control-sm table-field font-weight-bold" type="number" value="40" onchange="changeColor(this.id)" onblur="maxV()"></td>
                            <td style="border-color: white; border-width: thick"><input style="color: black; font-size: 1.2rem" id="maxH" class="form-control form-control-sm table-field font-weight-bold" type="number" value="40" onchange="changeColor(this.id)" onblur="maxH()"></td>
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
                    <button type="button" class="btn btn-info btn-block" onclick="transmit()">Trasmit</button>
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
        var programs = JSON.parse("{{$parameters}}".replace(/&quot;/g,'"'));

        //set currently selected program to -1
        var selectedProgram = -1;

        //parameters to be refered to throughout processes
        var parameters = {
            'targets': [0, 0, 0, 0, 0, 0],
            'ltass': [0, 0, 0, 0, 0, 0],
            'hearing_loss': [0, 0, 0, 0, 0, 0],
            'compression_ratio': [1, 1, 1, 1, 1, 1],
            'g50': [0, 0, 0, 0, 0, 0],
            'g65': [0, 0, 0, 0, 0, 0],
            'g80': [0, 0, 0, 0, 0, 0],
            'multiplier_l': [0, 0, 0, 0, 0, 0],
            'multiplier_h': [0, 0, 0, 0, 0, 0],
            'knee_low': [45, 45, 45, 45, 45, 45],
            'mpo_limit': [110, 110, 110, 110, 110, 110],
            'attack': [5, 5, 5, 5, 5, 5],
            'release': [100, 100, 100, 100, 100, 100],
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

        //start with G50/G80 deactivated
        deactivateG50G80();

        //logic to trigger Save As modal input
        $('#saveAsModal').on('shown.bs.modal', function () {
            $('#modalInput').trigger('focus')
        });

        //logic for controlling radio buttons
        $("#control_via_buttons :input").change(function() {
            switch (this.id) {
                case "g50_g80":
                    console.log("Controlling via g50/g80");
                    parameters['control_via'] = 1;
                    for(i = 0; i < 6; i++){
                        g50g80(i);
                    }
                    deactivateCRG65();
                    break;
                case "cr_g65":
                    console.log("Controlling via CR/g65");
                    parameters['control_via'] = 0;
                    for(i = 0; i < 6; i++){
                        crg65(i);
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


        /**
         * Deactivates compression ratio and G65 input fields. Used when switching to G50/G80 control
         */
        function deactivateCRG65(){
            for(i = 0; i < 7; i++){
                if(i !== 6) {
                    var cr_elem = document.getElementById("cr_" + i);
                    var g65_elem = document.getElementById("g65_" + i);
                    var g50_elem = document.getElementById("g50_" + i);
                    var g80_elem = document.getElementById("g80_" + i);
                }
                else{
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
        function deactivateG50G80(){
            for(i = 0; i < 7; i++){
                if(i !== 6) {
                    var cr_elem = document.getElementById("cr_" + i);
                    var g65_elem = document.getElementById("g65_" + i);
                    var g50_elem = document.getElementById("g50_" + i);
                    var g80_elem = document.getElementById("g80_" + i);
                }
                else{
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
        function fillDataField(){
            if(this['selectedProgram'] != -1){
                document.getElementById("dataField").value = JSON.stringify(this['parameters']);
                updateMostRecentProgram(this['selectedProgram']);
                return true;
            }
            else{
                alert("No program has been selected. Please select a program.");
                return false;
            }
        }


        /**
         * Calculates and fills G50/G80 parameters and input fields based on CR/G65 input fields. Only works if app is
         * set to be controlled via CR/G65
         *
         * @param id
         */
        function crg65(id){
            if(this['parameters']['control_via'] == 0) {
                var cr = parseFloat(document.getElementById("cr_" + id).value);
                console.log(cr);
                if (cr != 0) {
                    slope = (1 - cr) / cr;
                    var g65 = parseFloat(document.getElementById("g65_" + id).value);
                    var g50 = g65 - (slope * 15);
                    var g80 = g65 + (slope * 15);
                    this['parameters']['g50'][id] = g50;
                    this['parameters']['g80'][id] = g80;

                    document.getElementById("g50_" + id).value = g50;
                    document.getElementById("g80_" + id).value = g80;
                }
            }
        }


        /**
         * Calculates and fills CR/G65 parameters and input fields based on G50/G80 input fields. Only works if app is
         * set to be controlled via CR/G65
         *
         * @param id
         */
        function g50g80(id){
            if(this['parameters']['control_via'] == 1) {
                var g50 = parseFloat(document.getElementById("g50_" + id).value);
                var g80 = parseFloat(document.getElementById("g80_" + id).value);
                var slope = (g80 - g50)/30;
                console.log("slope: ", slope);
                var g65 = g50 + slope * 15;
                this['parameters']['g65'][id] = g65;
                if(slope != -1){
                    var cr = Math.round( (1 / (1 + slope)) * 10 ) / 10;
                    this['parameters']['compression_ratio'][id] = cr;
                    document.getElementById("cr_" + id).value = cr;
                }
                document.getElementById("g65_" + id).value = g65;
            }
        }


        /**
         * Calculates and fills G65 input fields based on the function: G65[i] = Targets[i] - LTASS[i]
         * After calculating G65, calculates and fills G50/G80, and sets program to be controlled via
         */
        function calcg65(){
            for(var i = 0; i < 6; i++){
                this['parameters']['g65'][i] = this['parameters']['targets'][i] - this['parameters']['ltass'][i];
                document.getElementById("g65_"+i).value = this['parameters']['g65'][i];
                crg65(i);
            }
            //if currently being controlled by g50g80, switch
            if(this['parameters']['control_via'] !== 0){
                this['parameters']['control_via'] == 0;
                $('#cr_g65').closest('.btn').button('toggle');
                deactivateG50G80();
            }
        }


        /**
         * Update most recently used program in the database so that it gets loaded by the goldilocks app when the listener
         * logs in.
         *
         * @param programId
         */
        function updateMostRecentProgram(programId){
            if(this['selectedProgram'] != -1){
                //post request
                $.ajax({
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    url: '/goldilocks/listener/programs/current',
                    data: JSON.stringify([
                        programId
                    ]),
                    success: function(response){
                        console.log("Success updating most recent program", response);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log("Error updating most recent program: " + textStatus + ' : ' + errorThrown);
                    }
                });
            }
        }



        /** Upon updating the input field in HTML, the parameter element needs to be updated as well. **/

        function targets(id){
            this['parameters']['targets'][id] = parseFloat(document.getElementById("targets_"+id).value);
        }
        function ltass(id){
            this['parameters']['ltass'][id] = parseFloat(document.getElementById("ltass_"+id).value);
        }
        function hl(id){
            this['parameters']['hearing_loss'][id] = parseFloat(document.getElementById("hl_"+id).value);
        }
        function cr(id){
            this['parameters']['compression_ratio'][id] = parseFloat(document.getElementById("cr_"+id).value);
            crg65(id);
        }
        function g50(id){
            this['parameters']['g50'][id] = parseFloat(document.getElementById("g50_"+id).value);
            g50g80(id);
        }
        function g65(id){
            this['parameters']['g65'][id] = parseFloat(document.getElementById("g65_"+id).value);
            crg65(id);
        }
        function g80(id){
            this['parameters']['g80'][id] = parseFloat(document.getElementById("g80_"+id).value);
            g50g80(id);
        }
        function multiL(id){
            this['parameters']['multiplier_l'][id] = parseFloat(document.getElementById("multiL_"+id).value);
        }
        function multiH(id){
            this['parameters']['multiplier_h'][id] = parseFloat(document.getElementById("multiH_"+id).value);
        }
        function kneelow(id){
            this['parameters']['knee_low'][id] = parseFloat(document.getElementById("kneelow_"+id).value);
        }
        function mpo(id){
            this['parameters']['mpo_limit'][id] = parseFloat(document.getElementById("mpo_"+id).value);
        }
        function attack(id){
            this['parameters']['attack'][id] = parseFloat(document.getElementById("attack_"+id).value);
        }
        function release(id){
            this['parameters']['release'][id] = parseFloat(document.getElementById("release_"+id).value);
        }
        function minL(){
            this['parameters']['l_min'] = parseFloat(document.getElementById("minL").value);
        }
        function maxL(){
            this['parameters']['l_max'] = parseFloat(document.getElementById("maxL").value);
        }
        function stepL(){
            this['parameters']['l_step'] = parseFloat(document.getElementById("stepL").value);
        }
        function minV(){
            this['parameters']['v_min'] = parseFloat(document.getElementById("minV").value);
        }
        function maxV(){
            this['parameters']['v_max'] = parseFloat(document.getElementById("maxV").value);
        }
        function stepV(){
            this['parameters']['v_step'] = parseFloat(document.getElementById("stepV").value);
        }
        function minH(){
            this['parameters']['h_min'] = parseFloat(document.getElementById("minH").value);
        }
        function maxH(){
            this['parameters']['h_max'] = parseFloat(document.getElementById("maxH").value);
        }
        function stepH(){
            this['parameters']['h_step'] = parseFloat(document.getElementById("stepH").value);
        }



        /**
         * Make a get request on /api/getParams to pull the parameters directly from the RTMHA. After getting the
         * RTMHA parameters, the function updates the javascript and inner html elements with the appropriate values.
         * It will also switch to control via G50/G80 automatically because RTMHA only sends G50/G80 parameters.
         */
        function monitorValues(){
            $.ajax({
                method: 'GET',
                url: '/api/getParams',
                success: function(response){
                    console.log(response);
                    params = JSON.parse(response);

                    //update parameters to reflect RTMHA state
                    parameters['g50'] = params['left']['g50'];
                    parameters['g80'] = params['left']['g80'];
                    parameters['knee_low'] = params['left']['knee_low'];
                    parameters['mpo_limit'] = params['left']['knee_high'];
                    parameters['afc'] = params['left']['afc'];
                    parameters['attack'] = params['left']['attack'];
                    parameters['release'] = params['left']['release'];

                    //update inner html to reflect RTMHA state
                    for(i = 0; i < 6; i++){
                        document.getElementById("g50_"+i).value = parameters['g50'][i];
                        document.getElementById("g80_"+i).value = parameters['g80'][i];
                        document.getElementById("kneelow_"+i).value = parameters['knee_low'][i];
                        document.getElementById("mpo_"+i).value = parameters['mpo_limit'][i];
                        document.getElementById("attack_"+i).value = parameters['attack'][i];
                        document.getElementById("release_"+i).value = parameters['release'][i];
                    }

                    //change control to G50/G80 because RTMHA only sends G50/G80
                    parameters['control_via'] = 1;
                    $('#g50_g80').closest('.btn').button('toggle');
                    for(i = 0; i < 6; i++){
                        g50g80(i);
                    }
                    deactivateCRG65();

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }


        /**
         * TODO
         * Function will eventually change the color of the input field. Currently only replaces and empty field with a
         * zero
         *
         * @param id
         */
        function changeColor(id){
            if(document.getElementById(id).value == ""){
                document.getElementById(id).value = 0;
            }
        }


        /**
         * Change the values of the inner HTML elements and javascript parameters based on the program selected from
         * the dropdown menu. Also calls updateMostRecentProgram() to set this as the program the listener will
         * use when logging into the Goldilocks app.
         *
         * @param id
         * @param progName
         */
        function changeProgram(id, progName){
            console.log('change program:', id);
            document.getElementById("btnGroupDrop1").innerHTML = progName;
            var newProgram = this['programs'][id];
            this['parameters'] = newProgram;
            this['selectedProgram'] = id;
            console.log(this['parameters']);

            //control via
            if(this['parameters']['control_via'] == 0){
                $('#cr_g65').closest('.btn').button('toggle');
                deactivateG50G80();
            }
            else{
                $('#g50_g80').closest('.btn').button('toggle');
                deactivateCRG65();
            }

            //afc
            if(this['parameters']['afc'] == 0){
                $('#afc_off').closest('.btn').button('toggle');
            }
            else{
                $('#afc_on').closest('.btn').button('toggle');
            }

            //sequence_num
            if(this['parameters']['sequence_num'] == 3){
                $('#num_3').closest('.btn').button('toggle');
            }
            else{
                $('#num_2').closest('.btn').button('toggle');
            }

            //sequence_order
            if(this['parameters']['sequence_order'] == 0){
                $('#seq_v').closest('.btn').button('toggle');
            }
            else{
                $('#seq_h').closest('.btn').button('toggle');
            }

            //sequence_order
            if(this['parameters']['app_behavior'] == 0){
                $('#one_by_one').closest('.btn').button('toggle');
            }
            else if(this['parameters']['app_behavior'] == 1){
                $('#volume_only').closest('.btn').button('toggle');
            }
            else{
                $('#all_3').closest('.btn').button('toggle');
            }

            //fill in input fields
            for(var i = 0; i < 7; i++){
                if(i != 6){
                    document.getElementById("cr_"+i).value = newProgram['compression_ratio'][i];
                    document.getElementById("g50_"+i).value = newProgram['g50'][i];
                    document.getElementById("g65_"+i).value = newProgram['g65'][i];
                    document.getElementById("g80_"+i).value = newProgram['g80'][i];
                    document.getElementById("targets_"+i).value = newProgram['targets'][i];
                    document.getElementById("ltass_"+i).value = newProgram['ltass'][i];
                    document.getElementById("hl_"+i).value = newProgram['hearing_loss'][i];
                    document.getElementById("multiL_"+i).value = newProgram['multiplier_l'][i];
                    document.getElementById("multiH_"+i).value = newProgram['multiplier_h'][i];
                    document.getElementById("kneelow_"+i).value = newProgram['knee_low'][i];
                    document.getElementById("mpo_"+i).value = newProgram['mpo_limit'][i];
                    document.getElementById("attack_"+i).value = newProgram['attack'][i];
                    document.getElementById("release_"+i).value = newProgram['release'][i];
                }
                else{
                    document.getElementById("cr_all").value = "";
                    document.getElementById("g50_all").value = "";
                    document.getElementById("g65_all").value = "";
                    document.getElementById("g80_all").value = "";
                    document.getElementById("kneelow_all").value = "";
                    document.getElementById("mpo_all").value = "";
                    document.getElementById("attack_all").value = "";
                    document.getElementById("release_all").value = "";
                }
            }
            document.getElementById("minL").value = newProgram['l_min'];
            document.getElementById("maxL").value = newProgram['l_max'];
            document.getElementById("stepL").value = newProgram['l_step'];
            document.getElementById("minV").value = newProgram['v_min'];
            document.getElementById("maxV").value = newProgram['v_max'];
            document.getElementById("stepV").value = newProgram['v_step'];
            document.getElementById("minH").value = newProgram['h_min'];
            document.getElementById("maxH").value = newProgram['h_max'];
            document.getElementById("stepH").value = newProgram['h_step'];

            //store in database most recent program for listener
            updateMostRecentProgram(id);
        }


        /**
         * Overrite current program with new values. If no program has been selected user is prompted to Save As
         * instead.
         */
        function save(){
            console.log("Save");
            if(this['selectedProgram'] != -1){
                //ajax request to save program
                $.ajax({
                    method: 'PUT',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    url: '/goldilocks/listener',
                    data: JSON.stringify([
                        this['selectedProgram'],
                        this['parameters']
                    ]),
                    success: function(response){
                        console.log(response);
                        alert('Program saved.');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        alert('There was an error with your request');
                    }
                });
            }
            else{
                alert("No setting selected. Click Save As.");
            }
        }


        /**
         * Save current settings as a new program. Program name field must not be empty.
         *
         */
        function saveAs(){
            console.log("Save as");
            if(document.getElementById("modalInput").value == ''){
                document.getElementById("modalInputError").innerHTML = "Must include a name";
                console.log("Error: input is empty");
            }
            else{
                $.ajax({
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    url: '/goldilocks/listener',
                    data: JSON.stringify([
                        document.getElementById("modalInput").value,
                        this['parameters']
                    ]),
                    success: function(response){
                        console.log(response);
                        $('#saveAsModal').modal('hide');
                        alert("New program saved. Reloading.");
                        location.reload();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        document.getElementById("modalInputError").innerHTML = "There was an error with your request";
                    }
                });
            }
        }


        /**
         * Transmit the current program state by makign a POST request on api/params
         *
         */
        function transmit(){
            console.log("Attempting to transmit");
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
                            afc: this['parameters']['afc'],
                            rear_mics: 0,
                            g50: this['parameters']['g50'],
                            g80: this['parameters']['g80'],
                            knee_low: this['parameters']['knee_low'],
                            knee_high: this['parameters']['mpo_limit'],
                            attack: this['parameters']['attack'],
                            release: this['parameters']['release']
                        },
                        right: {
                            en_ha: 1,
                            afc: this['parameters']['afc'],
                            rear_mics: 0,
                            g50: this['parameters']['g50'],
                            g80: this['parameters']['g80'],
                            knee_low: this['parameters']['knee_low'],
                            knee_high: this['parameters']['mpo_limit'],
                            attack: this['parameters']['attack'],
                            release: this['parameters']['release']
                        }
                    }
                }),
                success: function(response){
                    console.log(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }

    </script>

</body>
</html>