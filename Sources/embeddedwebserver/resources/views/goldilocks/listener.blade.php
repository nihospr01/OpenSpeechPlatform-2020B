<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/popper.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js')}}"></script>

    <title>Self Adjustment</title>

    <style>
        .footer {
            position: fixed;
            right: 0.3%;
            bottom: 0%;
            /*width: 100%;*/
            background-color: transparent;
            color: white;
        }

        .leftBtn{

            padding: 0rem;
            font-size: 2.2rem;
            line-height:inherit;
            border-radius:.3rem;
            width: 100px;
            height: 75px;
            align-self: center;
        }

        .rightBtn{

            padding: 0rem;
            font-size: 2.2rem;
            line-height: inherit;
            border-radius:.3rem;
            left:0;
            width:100px;
            height: 75px;
            align-self: center;
        }

        .okay{
            padding: 0rem;
            font-size: 1.4rem;
            line-height: inherit;
            border-radius:.3rem;
            left:0;
            width:90px;
            height: 75px;
            align-self: center;
            vertical-align: top;
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
            margin-right: 10%;
            margin-bottom: 0;
            margin-left: 8%;
        }

        .dontSave{
            padding: .5rem;
            font-size:1.4rem;
            line-height:1.2;
            border-radius:.2rem;
            width:110px;
            height: 70px;
            margin-right: 10%;
            margin-bottom: 0;
            margin-right: 10%;
        }


    </style>

</head>

<body style="background-color: #e8ecf1;">

    <div class="container-fluid" style="margin-bottom: 2px">
        <div class="row">
            <div  class="col">
                <button type="button" class="btn btn-outline-dark btn-block" style="background-color: white;" data-toggle="modal" data-target="#infoModal">
                    HA Values
                </button>
            </div>
            <div  class="col">
                <button id="lvhValues" class="btn btn-outline-dark btn-block" style="background-color: white;">
                    {{ $listener->listener }}
                </button>
            </div>

        </div>
    </div>

    <div class="container-fluid" style="background-color: #e8ecf1">

        <!--CRSIPNESS-->
        <div id="crispness_section" class="container" style="background-color: #f4d03f; padding: 1rem; visibility: hidden;">
            <h3 style="display:none" id="h_value" type="integer">0</h3>

            <div class="row">
                <div class="col-12 text-center h3">
                    CRISPNESS
                </div>
            </div>

            <div class="row" style="height:33%">
                <div class= "col-4 text-center">
                    <button id="h_less"  type="button" class="leftBtn">
                        Less
                    </button>
                </div>

                <div class="col-4 text-center">
                    <button type="button" id="h_okay" class="okay">
                        Ok
                    </button>
                </div>

                <div class="col-4 text-center">
                    <button id="h_more"  type="button" class="rightBtn">
                        More
                    </button>
                </div>
            </div>
        </div>

        <!--LOUDNESS-->
        <div id="loudness_section" class="container" style= "background-color: #6bb9f0; padding:1rem; visibility: hidden;" >
            <h3 style="display:none" id="v_value" type="integer">0</h3>

            <div class="row">
                <div class="col-12 text-center h3">
                    LOUDNESS
                </div>
            </div>

            <div class= "row" style="height:33%">
                <div class="col-4 text-center">
                    <button id="v_less"  type="button" class="leftBtn">
                        Less
                    </button>
                </div>
                <div class="col-4 text-center">
                    <button type="button" id="v_okay" class="okay">
                        Ok
                    </button>
                </div>
                <div class="col-4 text-center">
                    <button id="v_more"  type="button" class="rightBtn">
                        More
                    </button>
                </div>
            </div>
        </div>


        <!--FULLNESS-->
        <div id="fullness_section" class="container" style= "background-color: #fabe58; padding:1rem; visibility: hidden;">
            <h3 style="display:none" id="l_value" type="integer">0</h3>

            <div class="row">
                <div class="col-12 text-center h3">
                    FULLNESS
                </div>
            </div>

            <div class="row" style="height:33%">
                <div class="col-4 text-center">
                    <button id="l_less"  type="button" class="leftBtn">
                        Less
                    </button>
                </div>
                <div class="col-4 text-center">
                    <button type="button" id="l_okay" class="okay">
                        Ok
                    </button>
                </div>
                <div class="col-4 text-center">
                    <button id="l_more"  type="button" class="rightBtn">
                        More
                    </button>
                </div>
            </div>
        </div>

        <div class="container footer" id="end_buttons" style="display: none;">
            <div class="row" >
                <div class="col-4 text-center" >
                    <button type="button" id="save_button" class="save btn-info">
                        Save
                    </button>
                </div>

                <div class="col-4 text-center" style="align-items:center">
                    <button type="button" id="save_as_modal_trigger" class="saveAs btn-info" data-toggle="modal" data-target="#saveAsModal">
                        Save As
                    </button>
                </div>

                <div class="col-4 text-center" style="align-items:center">
                    <button type="button" id="dont_save_button" class="save btn-info" style="display: inline-block;">
                        Don't Save
                    </button>
                </div>
            </div>
        </div>

        <div class="container-fluid footer" id="finish_footer" style="margin-top: 0.5rem;">
            <div class="row" style="margin-bottom: 0.5rem">
                <div class="col-12 text-center">
                    <button type="button" id="finish_button" class="okay btn-info" style="visibility: hidden; display: inline-block;">
                        Finish
                    </button>
                </div>
            </div>
        </div>

    </div>



    <!--Modal for saving new program-->
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
                    <input id="modalInput" name="programName" type="text" placeholder="Quiet, Noisy, etc." value="{{ $next_name }}">
                    <p style="color: red" id="modalInputError"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button id="save_as_button" type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!--Modal for viewing hearing aid state-->
    <div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoModalLabel">Logout Listener</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" style="float: left;" onclick="location='{{ url("goldilocks/listener/logout") }}'">Logout</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>




    <script language="JavaScript">

        /** Initialize parameters for two channels **/
        var parametersTwoChannels = {
            left:{
            'targets': [0, 0, 0, 0, 0, 0],
            'ltass': [0, 0, 0, 0, 0, 0],
            'hearing_loss': [0, 0, 0, 0, 0, 0],
            'compression_ratio': [1, 1, 1, 1, 1, 1],
            'g50': [0, 0, 0, 0, 0, 0],
            'g65': [0, 0, 0, 0, 0, 0],
            'g80': [0, 0, 0, 0, 0, 0],

            'g50_max': [0, 0, 0, 0, 0, 0],
            'knee_low': [45, 45, 45, 45, 45, 45],
            'mpo_band': [110, 110, 110, 110, 110, 110],
            'attack': [5, 5, 5, 5, 5, 5],
            'release': [100, 100, 100, 100, 100, 100],

            },
            right:{
                'targets': [0, 0, 0, 0, 0, 0],
            'ltass': [0, 0, 0, 0, 0, 0],
            'hearing_loss': [0, 0, 0, 0, 0, 0],
            'compression_ratio': [1, 1, 1, 1, 1, 1],
            'g50': [0, 0, 0, 0, 0, 0],
            'g65': [0, 0, 0, 0, 0, 0],
            'g80': [0, 0, 0, 0, 0, 0],

            'g50_max': [0, 0, 0, 0, 0, 0],
            'knee_low': [45, 45, 45, 45, 45, 45],
            'mpo_band': [110, 110, 110, 110, 110, 110],
            'attack': [5, 5, 5, 5, 5, 5],
            'release': [100, 100, 100, 100, 100, 100],

            }
        };

        /** Initialize parameters for single channel **/
        var parametersSingleChannel = {
            'multiplier_l': [0, 0, 0, 0, 0, 0],
            'multiplier_h': [0, 0, 0, 0, 0, 0],
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
            'app_behavior': 1 //0 - one by one, 1 - volume only, 2 - all 3
        };

        var programId = -1;
        var programIdStart = -1;
        /** Need to calculate for right and left channel seperately **/
        var channels = ['left', 'right'];

        /** Pull parameters if they have been passed through **/
        if("{{$parameters}}" != ""){
            var parameters = JSON.parse("{{$parameters}}".replace(/&quot;/g,'"'));
            var parametersTwoChannels = parameters.parametersTwoChannels;
            var parametersSingleChannel = parameters.parameters;
            console.log("parameters passed through:", parameters);
        }

        if("{{$program_id}}" != null && "{{$program_id}}" != ""){
            this['programId'] = this['programIdStart'] = '{{$program_id}}';
            console.log("program_id passed through", this['programId']);
        }


        /* To keep track of adjustment logs */
        //store the starting g65 so that we may use it in updating function
        // index 0 is starting_g65 for left channel, index 1 is starting_g65 for right channel
        var starting_g65 = [this['parametersTwoChannels']['left']['g65'].slice(), this['parametersTwoChannels']['right']['g65'].slice()];

        var l_multipliers = this['parametersSingleChannel']['multiplier_l'].slice();
        var h_multipliers = this['parametersSingleChannel']['multiplier_h'].slice();
         // index 0 is cr for left channel, index 1 is cr for right channel
        var cr = [this['parametersTwoChannels']['left']['compression_ratio'].slice(),this['parametersTwoChannels']['right']['compression_ratio'].slice()];

        var changeString = "";
        var steps = 0;


        // set up L and H step sizes based on dB
        // L (fullness) - max lmult (250,500,1000)
        var l_mul = Math.max(parametersSingleChannel['multiplier_l'][0],
        parametersSingleChannel['multiplier_l'][1],
        parametersSingleChannel['multiplier_l'][2]);
        // H (crispness) - max hmult (2000,4000,8000)
        var h_mul = Math.max(parametersSingleChannel['multiplier_h'][3],
        parametersSingleChannel['multiplier_h'][4],
        parametersSingleChannel['multiplier_h'][5]);


        /** Set up sequence based on parameters passed through **/
        var sequence;
        //case: sequence
        if(parametersSingleChannel['app_behavior'] === 0) {
            if (parametersSingleChannel['sequence_num'] === 3) {
                if (parametersSingleChannel['sequence_order'] === 0) {
                    //3,V: [V, H, V, L, three]
                    sequence = ['V', 'H', 'V', 'L', 3];
                } else {
                    //3,H: [H, V, H, L, three]
                    sequence = ['H', 'V', 'H', 'L', 3];
                }
            }
            else {
                if (parametersSingleChannel['sequence_order'] === 0) {
                    //2,V: [V, H, two]
                    sequence = ['V', 'H', 2];
                } else {
                    //2,H: [H, V, two]
                    sequence = ['H', 'V', 2];
                }
            }
        }
        //case: loudness only
        else if(parametersSingleChannel['app_behavior'] === 1){
            sequence = [1];
        }
        //case: either all three or two, without sequence
        else{
            sequence = [parametersSingleChannel['sequence_num']];
        }

        //load the first panel
        nextPanel();

        /**
         * Loading this page will transmit the most recent program (decided by researcher). This has the effect that
         * refreshing the page resets the sequence and starts with the hearing aid in the correct state.
         */
        transmit();

        //log that the app has started
        logButtonPress('started_app');
        var start = new Date();


        /** Fullness button control **/

        $('#l_less').click(function(){
            var dec = decrement('l_value', 'l_step', 'l_min');
            if (dec != -1) {
                logButtonPress('l_less');
                steps += 1;
            }
        });

        $('#l_okay').click(function(){
            nextPanel();
            logButtonPress('l_okay');
        });

        $('#l_more').click(function(){
            var inc = increment('l_value', 'l_step', 'l_max');
            if (inc != -1) {
                logButtonPress('l_more');
                steps += 1;
            }
        });



        /** Loudness button control **/

        $('#v_less').click(function(){
            var dec = decrement('v_value', 'v_step', 'v_min');
            if (dec != -1) {
                logButtonPress('v_less');
                steps += 1;
            }
        });

        $('#v_okay').click(function(){
            nextPanel();
            logButtonPress('v_okay');
        });

        $('#v_more').click(function(){
            var inc = increment('v_value', 'v_step', 'v_max');
            if (inc != -1) {
                logButtonPress('v_more');
                steps += 1;
            }
        });



        /** Crispness button control **/

        $('#h_less').click(function(){
            var dec = decrement('h_value', 'h_step', 'h_min');

            // link with fullness for 2-parameter option
            var inc = 0;
            if (parametersSingleChannel['sequence_num'] == 2 && dec != -1) {
                inc = increment('l_value', 'l_step', 'l_max');
                // error in increment -- rollback decrement as well
                if (inc == -1) {
                    increment('h_value', 'h_step', 'h_max');
                }
                else {
                    logButtonPress('l_more')
                }
            }

            if (dec != -1 && inc != -1) {
                logButtonPress('h_less');
                steps += 1;
            }
        });

        $('#h_okay').click(function(){
            nextPanel();
            logButtonPress('h_okay');
        });

        $('#h_more').click(function(){
            var inc = increment('h_value', 'h_step', 'h_max');

            // link with fullness for 2-parameter option
            var dec = 0;  // default value in scope
            if (parametersSingleChannel['sequence_num'] == 2 && inc != -1) {
                dec = decrement('l_value', 'l_step', 'l_min');
                // error in decrement -- rollback increment as well
                if (dec == -1) {
                    decrement('h_value', 'h_step', 'h_min');
                }
                else {
                    logButtonPress('l_less');
                }
            }

            // log only if both successful
            if (inc != -1 && dec != -1) {
                logButtonPress('h_more');
                steps += 1;
            }
        });



        /** Finish button control **/
        $('#finish_button').click(function(){
            nextPanel();
            logButtonPress('finish_button');
        });

        /** Don't save button control **/
        $('#dont_save_button').click(function(){
            backToProgramsPage();
        });

        /** Save button control **/
        $('#save_button').click(function(){
            updateProgram();
            logButtonPress('save_button');
        });

        /** Save as button control (in modal) **/
        $('#save_as_button').click(function(){
            saveProgram();
            logButtonPress('save_as_button');
        })

        /** Pop up box control **/
        $('#saveAsModal').on('shown.bs.modal', function () {
            $('#modalInput').trigger('focus')
        });

        $('#infoModal').on('shown.bs.modal', function () {
            monitorValues();
        });

        $('#lvhValues').click(function(){
            updateLVHText();
        });

        /**
         * Redirects to programs page
         */
        function backToProgramsPage() {
            let newUrl = window.location.origin + "/goldilocks/listener/programs";
            window.location.replace(newUrl);
        }

        disableButtonOnMaxOvershoot();
        disableButtonOnMinOvershoot();

        /**
         * Logic to handle panel changes. Uses variable 'sequence' array to see what goes next.
         */
        function nextPanel(){

            hideAllPanels();
            console.log(this['sequence'])
            if(this['sequence'].length > 1){
                var elem = this['sequence'].shift();
                switch(elem){
                    case 'V':
                        document.getElementById("loudness_section").style.visibility = "visible";
                        break;
                    case 'H':
                        document.getElementById("crispness_section").style.visibility = "visible";
                        break;
                    case 'L':
                        document.getElementById("fullness_section").style.visibility = "visible";
                        break;
                }
            }

            else if(this['sequence'].length == 1){
                var elem = this['sequence'].shift();

                switch (elem) {
                    case 1:
                        document.getElementById("loudness_section").style.visibility = "visible"
                        document.getElementById("v_okay").style.visibility = "hidden";                   
                        document.getElementById("finish_button").style.visibility = "visible"; 
                        break;

                    case 2:
                        document.getElementById("crispness_section").style.visibility = "visible";
                        document.getElementById("loudness_section").style.visibility = "visible";
                        document.getElementById("h_okay").style.visibility = "hidden";
                        document.getElementById("v_okay").style.visibility = "hidden";
                        document.getElementById("finish_button").style.visibility = "visible";
                        break;

                    case 3:
                        document.getElementById("crispness_section").style.visibility = "visible";
                        document.getElementById("loudness_section").style.visibility = "visible";
                        document.getElementById("fullness_section").style.visibility = "visible";
                        document.getElementById("h_okay").style.visibility = "hidden";
                        document.getElementById("v_okay").style.visibility = "hidden";
                        document.getElementById("l_okay").style.visibility = "hidden";
                        document.getElementById("finish_button").style.visibility = "visible";
                        break;
                }
            }

            //empty sequence array implies finish button has been pressed
            else{
                document.getElementById("end_buttons").style.display = "block";
                document.getElementById("save_button").style.visibility = "visible";
                document.getElementById("dont_save_button").style.visibility = "visible";
                document.getElementById("save_as_modal_trigger").style.visibility = parametersSingleChannel['app_behavior'] == 1 ? 'hidden' : "visible";
            }
        }

        /**
         * Reset page to show no panels. Useful at the beginning of updating next panel.
         */
        function hideAllPanels(){
            document.getElementById("crispness_section").style.visibility = "hidden";
            document.getElementById("loudness_section").style.visibility = "hidden";
            document.getElementById("fullness_section").style.visibility = "hidden";
            document.getElementById("finish_button").style.visibility = "hidden";
            document.getElementById("save_button").style.visibility = "hidden";
            document.getElementById("dont_save_button").style.visibility = "hidden";
            document.getElementById("finish_footer").style.visibility = "hidden";
            document.getElementById("end_buttons").style.visibility = "hidden";
            document.getElementById("save_as_modal_trigger").style.visibility = "hidden";
        }



        /**
         * Calculate g50[id] and g80[id] based on g65[id] and compression_ration[id] on the passed in channel
         */
        function crg65(id, channel){
            var cr = this['parametersTwoChannels'][channel]['compression_ratio'][id];
            if (cr != 0) {
                slope = (1 - cr) / cr;
                var g65 = this['parametersTwoChannels'][channel]['g65'][id];
                var g50 = g65 - (slope * 15);
                var g80 = g65 + (slope * 15);
                this['parametersTwoChannels'][channel]['g50'][id] = g50;
                this['parametersTwoChannels'][channel]['g80'][id] = g80;
            }
        }

        /**
         * Calculate g65[id] and compression_ration[id] based on g50[id] and g80[id]
         */
        function g50g80(id, channel){
            var g50 = parametersTwoChannels[channel]['g50'][id];
            var g80 = parametersTwoChannels[channel]['g80'][id];
            var slope = (g80 - g50)/30;

            var g65 = g50 + slope * 15;
            this['parametersTwoChannels'][channel]['g65'][id] = g65;
            if(slope != -1){
                var cr = Math.round( (1 / (1 + slope)) * 10 ) / 10;
                this['parametersTwoChannels'][channel]['compression_ratio'][id] = cr;
            }
        }

        /**
         * Returns true if change would exceed maximum for one channel
         */
        function exceedsMax(l_val, v_val, h_val){
            for(i = 0; i < 6; i++){
                //loop through two channels of starting_g65 and cr
                for(j = 0; j < 2; j++){
                    var g65 = this['starting_g65'][j][i] + v_val + (l_val * this['parametersSingleChannel']['multiplier_l'][i]) + (h_val * this['parametersSingleChannel']['multiplier_h'][i]);
                    var cr = this['parametersTwoChannels'][channels[j]]['compression_ratio'][i];
                    if(cr != 0){
                        var slope = (1 - cr) / cr;
                        var g50 = g65 - (slope * 15);
                        if(g50 > this['parametersTwoChannels'][channels[j]]['g50_max'][i]){
                            return true;
                        }
                    }
                    else{
                        alert("Compression ratio should not be set to zero. Please ask for assistance.");
                        return true;
                    }
                }
                
            }
            return false;
        }


        /**
         * Calculate new value for l/v/h and check to see if it exceeds maximum. If so show warning, otherwise update
         * values and transmit.
         */
        function increment(id, step_type, max){
            var old_value = +document.getElementById(id).innerHTML; //plus sign is to denote as integer
            var add = this['parametersSingleChannel'][step_type];
            var new_value = old_value + add;

            if (id === 'l_value') {
                add *= l_mul;
            }
            else if (id === 'h_value') {
                add *= h_mul;
            }

            if(new_value <= this['parametersSingleChannel'][max]){
                document.getElementById(id).innerHTML = old_value + add;

                var l_val = +document.getElementById("l_value").innerHTML / l_mul;
                var v_val = +document.getElementById("v_value").innerHTML;
                var h_val = +document.getElementById("h_value").innerHTML / h_mul;

                //check to see if this change exceeds max g50 gain
                if(exceedsMax(l_val, v_val, h_val)){
                    //output max message
                    // alert("Warning: you've attempted to exceed maximum gain. No change will take place.");

                    //change back the value
                    // document.getElementById(id).innerHTML = old_value;
                    // return -1;
                }
                else {
                    //update parameters
                    for (i = 0; i < 6; i++) {
                        for( j = 0; j<2; j++){
                            this['parametersTwoChannels'][channels[j]]['g65'][i] = this['starting_g65'][j][i] + v_val + (l_val * this['parametersSingleChannel']['multiplier_l'][i]) + (h_val * this['parametersSingleChannel']['multiplier_h'][i]);
                            crg65(i, channels[j]);
                        }
                    }

                    //send values to hearing aid
                    transmit();
                    updateLVHText();
                }

            } else{
                // alert("Warning: Cannot exceed maximum value for " + step_type);

                // document.getElementById(id).innerHTML = old_value;
                // return -1;
            }

            this.disableButtonOnMaxOvershoot();
            this.enableButtonOnBypassMinOvershoot();
        }

        function enableButtonOnBypassMinOvershoot() {
            // preemptive calculaions to determine button enables
            var l_less = document.getElementById("l_less");
            var v_less = document.getElementById("v_less");
            var h_less = document.getElementById("h_less");

            var sub_l = this['parametersSingleChannel']['l_step'];
            var sub_h = this['parametersSingleChannel']['h_step'];
            var sub_v = this['parametersSingleChannel']['v_step'];

            var old_value_l = +document.getElementById("l_value").innerHTML;
            var old_value_h = +document.getElementById("h_value").innerHTML;
            var old_value_v = +document.getElementById("v_value").innerHTML;

            var new_value_l = old_value_l - sub_l;
            var new_value_h = old_value_h - sub_h;
            var new_value_v = old_value_v - sub_v;

            // enable l button if doesn't overshoot min anymore
            if(new_value_l > this['parametersSingleChannel']['l_min']){
                l_less.disabled = false;
                l_less.innerHTML = "Less";
            }

            if(new_value_h > this['parametersSingleChannel']['h_min']){
                h_less.disabled = false;
                h_less.innerHTML = "Less";
            }

            if(new_value_v > this['parametersSingleChannel']['v_min']){
                v_less.disabled = false;
                v_less.innerHTML = "Less";
            }
        }

        function disableButtonOnMaxOvershoot() {
            // preemptive calculaions to determine button disables
            var l_more = document.getElementById("l_more");
            var v_more = document.getElementById("v_more");
            var h_more = document.getElementById("h_more");

            var add_l = this['parametersSingleChannel']['l_step'];
            var add_h = this['parametersSingleChannel']['h_step'];
            var add_v = this['parametersSingleChannel']['v_step'];

            var old_value_l = +document.getElementById("l_value").innerHTML;
            var old_value_h = +document.getElementById("h_value").innerHTML;
            var old_value_v = +document.getElementById("v_value").innerHTML;

            var new_value_l = old_value_l + add_l;
            var new_value_h = old_value_h + add_h;
            var new_value_v = old_value_v + add_v;

            add_l *= l_mul;
            add_h *= h_mul;

            // disable l button on overshoot
            if(new_value_l <= this['parametersSingleChannel']['l_max']){
                var new_value_l_2 = old_value_l + add_l;

                var l_val = new_value_l_2 / l_mul;
                var v_val = old_value_v;
                var h_val = old_value_h / h_mul;

                if(exceedsMax(l_val, v_val, h_val)){
                    l_more.disabled = true;
                    l_more.innerHTML = "MAX";
                    l_more.style.color = "red";
                }
            } else {
                l_more.disabled = true;
                l_more.innerHTML = "MAX";
                l_more.style.color = "red";
            }

            // disable h button on overshoot
            if(new_value_h <= this['parametersSingleChannel']['h_max']){
                var new_value_h_2 = old_value_h + add_h;

                var l_val = old_value_l / l_mul;
                var v_val = old_value_v;
                var h_val = new_value_h_2 / h_mul;

                if(exceedsMax(l_val, v_val, h_val)){
                    h_more.disabled = true;
                    h_more.innerHTML = "MAX";
                }
            } else {
                h_more.disabled = true;
                h_more.innerHTML = "MAX";
            }

            // disable v button on overshoot
            if(new_value_v <= this['parametersSingleChannel']['v_max']){
                var new_value_v_2 = old_value_v + add_v;

                var l_val = old_value_l / l_mul;
                var v_val = new_value_v_2;
                var h_val = old_value_h / h_mul;

                if(exceedsMax(l_val, v_val, h_val)){
                    v_more.disabled = true;
                    v_more.innerHTML = "MAX";
                }
            } else {
                v_more.disabled = true;
                v_more.innerHTML = "MAX";
            }
        }

        /**
         * Calculate new value for l/v/h and check to see if it exceeds minimum. If so show warning, otherwise update
         * values and transmit.
         */
        function decrement(id, step_type, min){
            var old_value = +document.getElementById(id).innerHTML; //plus sign is to denote as integer
            var sub = this['parametersSingleChannel'][step_type];
            var new_value = old_value - sub;

            // calculate the values in decibels for display
            if (id === 'l_value') {
                sub *= l_mul;
            }
            else if (id === 'h_value') {
                sub *= h_mul;
            }

            if(new_value >= this['parametersSingleChannel'][min]){
                document.getElementById(id).innerHTML = old_value - sub;

                var l_val = +document.getElementById("l_value").innerHTML / l_mul;
                var v_val = +document.getElementById("v_value").innerHTML;
                var h_val = +document.getElementById("h_value").innerHTML / h_mul;

                //update parameters for left and right channel
                for(i = 0; i < 6; i++){
                    for(j = 0; j < 2; j++){
                        this['parametersTwoChannels'][channels[j]]['g65'][i] = this['starting_g65'][j][i] + v_val + (l_val * this['parametersSingleChannel']['multiplier_l'][i]) + (h_val * this['parametersSingleChannel']['multiplier_h'][i]);
                        crg65(i, channels[j]);
                    }
                   
                }

                //send values to hearing aid
                transmit();
                updateLVHText();
            }

            this.disableButtonOnMinOvershoot();
            this.enableButtonOnBypassMaxOvershoot();
        }

        function enableButtonOnBypassMaxOvershoot() {
            // preemptive calculaions to determine button enables
            var l_more = document.getElementById("l_more");
            var v_more = document.getElementById("v_more");
            var h_more = document.getElementById("h_more");

            var add_l = this['parametersSingleChannel']['l_step'];
            var add_h = this['parametersSingleChannel']['h_step'];
            var add_v = this['parametersSingleChannel']['v_step'];

            var old_value_l = +document.getElementById("l_value").innerHTML;
            var old_value_h = +document.getElementById("h_value").innerHTML;
            var old_value_v = +document.getElementById("v_value").innerHTML;

            var new_value_l = old_value_l + add_l;
            var new_value_h = old_value_h + add_h;
            var new_value_v = old_value_v + add_v;

            add_l *= l_mul;
            add_h *= h_mul;

            // enable l button if doesn't bypass max threshold anymore
            if(new_value_l <= this['parametersSingleChannel']['l_max']){
                var new_value_l_2 = old_value_l + add_l;

                var l_val = new_value_l_2 / l_mul;
                var v_val = old_value_v;
                var h_val = old_value_h / h_mul;

                if(!exceedsMax(l_val, v_val, h_val)){
                    l_more.disabled = false;
                    l_more.innerHTML = "More";
                }
            }
            
            // enable v button if doesn't bypass max threshold anymore
            if(new_value_v <= this['parametersSingleChannel']['v_max']){
                var new_value_v_2 = old_value_v + add_v;

                var l_val = old_value_l / l_mul;
                var v_val = new_value_v_2;
                var h_val = old_value_h / h_mul;

                if(!exceedsMax(l_val, v_val, h_val)){
                    v_more.disabled = false;
                    v_more.innerHTML = "More";
                }
            }

            // enable h button if doesn't bypass max threshold anymore
            if(new_value_h <= this['parametersSingleChannel']['h_max']){
                var new_value_h_2 = old_value_h + add_h;

                var l_val = old_value_l / l_mul;
                var v_val = old_value_v;
                var h_val = new_value_h_2 / h_mul;

                if(!exceedsMax(l_val, v_val, h_val)){
                    h_more.disabled = false;
                    h_more.innerHTML = "More";
                }
            }
        }

        function disableButtonOnMinOvershoot() {
            // preemptive calculaions to determine button disables
            var l_less = document.getElementById("l_less");
            var v_less = document.getElementById("v_less");
            var h_less = document.getElementById("h_less");

            var sub_l = this['parametersSingleChannel']['l_step'];
            var sub_h = this['parametersSingleChannel']['h_step'];
            var sub_v = this['parametersSingleChannel']['v_step'];

            var old_value_l = +document.getElementById("l_value").innerHTML;
            var old_value_h = +document.getElementById("h_value").innerHTML;
            var old_value_v = +document.getElementById("v_value").innerHTML;

            var new_value_l = old_value_l - sub_l;
            var new_value_h = old_value_h - sub_h;
            var new_value_v = old_value_v - sub_v;

            if(new_value_l < this['parametersSingleChannel']['l_min']){
                l_less.disabled = true;
                l_less.innerHTML = "MIN";
            }

            if(new_value_h < this['parametersSingleChannel']['h_min']){
                h_less.disabled = true;
                h_less.innerHTML = "MIN";
            }

            if(new_value_v < this['parametersSingleChannel']['v_min']){
                v_less.disabled = true;
                v_less.innerHTML = "MIN";
            }
        }

        /**
         * Update the lvhValues HTML element using the current values
         */
        function updateLVHText(){
            var elem = document.getElementById("lvhValues");
            var l_val = document.getElementById("l_value").innerHTML;
            var v_val = document.getElementById("v_value").innerHTML;
            var h_val = document.getElementById("h_value").innerHTML;

            // 03/28/2019 -- commented out for production
            // elem.innerHTML = l_val + ", " + v_val + ", " + h_val;
        }



        /**
         * Make a POST request to /api/params with the proper data to update the state of the hearing aid. This should
         * be done after any changes to the values are made and upon loading the page.
         */
        function transmit(){
            $.ajax({
                method: 'POST',
                url: '/api/params',
                dataType: 'text json',
                contentType: 'application/json',
                data: JSON.stringify({
                    user_id: this['listener_id'],
                    method: "set",
                    request_action: 1,
                    data: {
                        left: {
                            en_ha: 1,
                            afc: this['parametersTwoChannels']['left']['afc'],
                            rear_mics: 0,
                            g50: this['parametersTwoChannels']['left']['g50'],
                            g80: this['parametersTwoChannels']['left']['g80'],
                            knee_low: this['parametersTwoChannels']['left']['knee_low'],
                            mpo_band: this['parametersTwoChannels']['left']['mpo_band'],
                            attack: this['parametersTwoChannels']['left']['attack'],
                            release: this['parametersTwoChannels']['left']['release']
                        },
                        right: {
                            en_ha: 1,
                            afc: this['parametersTwoChannels']['right']['afc'],
                            rear_mics: 0,
                            g50: this['parametersTwoChannels']['right']['g50'],
                            g80: this['parametersTwoChannels']['right']['g80'],
                            knee_low: this['parametersTwoChannels']['right']['knee_low'],
                            mpo_band: this['parametersTwoChannels']['right']['mpo_band'],
                            attack: this['parametersTwoChannels']['right']['attack'],
                            release: this['parametersTwoChannels']['right']['release']
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



        /**
         * Update and save program in the database that already exists. Program in database is denoted by programId
         * variable.
         */
        function updateProgram(){
            // throw error if no program has been selected
            if(this['programId'] == -1){
                if(confirm('No program selected. Press okay to go to programs, press cancel to stay.')) {
                    location.href = '{{ url('programs') }}';
                }
            }
            else{
                $.ajax({
                    method: 'PUT',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    url: '/goldilocks/listener',
                    dataType: 'text json',
                    contentType: 'application/json',
                    data: JSON.stringify([
                        this['programId'],
                        this['parametersSingleChannel'],
                        this['parametersTwoChannels'],

                    ]),
                    success: function(response){
                        console.log(response);
                        logAdjustmentSession();
                        alert('Program saved');
                        location.href= '{{ url('/goldilocks/listener/programs') }}';
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        alert('There was an error with your request');
                    }
                });

            }
        }



        /**
         * Create a new program in the database tied to the listener. Requires a non-empty name field.
         */
        function saveProgram(){
            console.log("Saving program");
            if(document.getElementById("modalInput").value == ''){
                document.getElementById("modalInputError").innerHTML = "Must include a name";
                console.log("Error: input is empty");
            }
            else{
                $.ajax({
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    url: '/goldilocks/listener',
                    dataType: 'text json',
                    contentType: 'application/json',
                    data: JSON.stringify([
                        document.getElementById("modalInput").value,
                        this['parametersSingleChannel'],
                        this['parametersTwoChannels'],
                    ]),
                    success: function(response){
                        if(response['status'] == "failure"){
                            document.getElementById("modalInputError").innerHTML = "The name \"" + (document.getElementById("modalInput").value) + "\" is already taken.";
                        }
                        else {
                            console.log(response);
                            programId = response["id"];
                            logAdjustmentSession();
                            $('#saveAsModal').modal('hide');
                            location.href = '{{ url('/goldilocks/listener/programs') }}';
                        }
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
         * Makes a GET request on /api/getParams to pull the parameters from the hearing aid and fills the variables
         * in the page based off of these parameters.
         */
        function monitorValues(){
            $.ajax({
                method: 'GET',
                url: '/api/getParams',
                success: function(response){
                    console.log(response);
                    params = JSON.parse(response);
                    for(i = 0; i < 2; i++){
                        let channel = channels[i];
                        parametersTwoChannels[channel]['g50'] = params[channel]['g50'];
                        parametersTwoChannels[channel]['g80'] = params[channel]['g80'];
                        parametersTwoChannels[channel]['knee_low'] = params[channel]['knee_low'];
                        parametersTwoChannels[channel]['mpo_band'] = params[channel]['mpo_band'];
                        parametersTwoChannels[channel]['afc'] = params[channel]['afc'];
                        parametersTwoChannels[channel]['attack'] = params[channel]['attack'];
                        parametersTwoChannels[channel]['release'] = params[channel]['release'];

                    }
                    
                    for(i = 0; i < 6; i++){
                        for(j=0; j <2; j++){
                            g50g80(i, channels[j]);
                        }    
                    }
                    document.getElementById("infoModalText").innerHTML = JSON.stringify(
                        {
                            CR: parameters['compression_ratio'],
                            G65: parameters['g65'],
                            KL: parameters['knee_low'],
                            MPO: parameters['mpo_band'],
                            AT: parameters['attack'],
                            RT: parameters['release']
                        }
                    );
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }



        /**
         * Logs the button press in the database. Should called after calculations occur (if any), so that we have
         * updated lvh values.
         *
         * @param button_id
         */
        function logButtonPress(button_id){
            var l_val = +document.getElementById("l_value").innerHTML;
            var v_val = +document.getElementById("v_value").innerHTML;
            var h_val = +document.getElementById("h_value").innerHTML;

            // build changeString
            if (button_id === "l_more" || button_id === "l_less") {
                changeString += "L" + l_val + "|";
            }
            else if (button_id === "v_more" || button_id === "v_less") {
                changeString += "V" + v_val + "|";
            }
            else if (button_id === "h_more" || button_id === "h_less") {
                changeString += "H" + h_val + "|";
            }

            // log to db
            $.ajax({
                method: 'POST',
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                url: '/goldilocks/listener/log',
                dataType: 'text json',
                contentType: 'application/json',
                data: JSON.stringify({
                   action: button_id,
                   program_state: {
                        parameters: parametersSingleChannel,
                        parametersTwoChannels
                   },
                   lvh_values: {
                       l_value: l_val,
                       v_value: v_val,
                       h_value: h_val
                   },
                }),
                success: function(response) {
                   console.log(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                   console.log(JSON.stringify(jqXHR));
                   console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }


        /**
         * Logs the adjustment session on the press of finish button to the
         * table `listener_adjustment_logs`
         */
        function logAdjustmentSession(){
            // get finish timestamp
            var dt = new Date();
            var tz = dt.getTimezoneOffset() / -60;

            // elapsed ms since start of session until finish
            var ms = dt - start;

            // strip last '|' delimiter from changeString
            changeString = changeString.slice(0, -1);

            var l_val = +document.getElementById("l_value").innerHTML;
            var v_val = +document.getElementById("v_value").innerHTML;
            var h_val = +document.getElementById("h_value").innerHTML;

            console.log("millisecs: " + ms);
            console.log("g65: " + starting_g65);
            console.log("cr: " + cr);
            console.log("lmul: " + l_multipliers);
            console.log("hmul: " + h_multipliers);
            console.log("step-by-step changes: " + changeString);
            console.log("timestamp: " + dt);

            var jsonData = JSON.stringify({
                   final_lvh: {
                       l_value: l_val,
                       v_value: v_val,
                       h_value: h_val
                   },
                   starting_g65: starting_g65,
                   ending_g65: [this['parametersTwoChannels']['left']['g65'].slice(), this['parametersTwoChannels']['right']['g65'].slice()],
                   cr: cr,
                   lmul: l_multipliers,
                   hmul: h_multipliers,
                   time_ms: ms,
                   steps: steps,
                   changes: changeString,
                   start_program_id: programIdStart,
                   end_program_id: programId,
                   timestamp: dt,
                   timezone: tz
                });
            console.log(jsonData);

            // log to db
            $.ajax({
                async: false,
                method: 'POST',
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                url: '/goldilocks/listener/adjustmentLog',
                dataType: 'text json',
                contentType: 'application/json',
                data: jsonData,
                success: function(response) {
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
