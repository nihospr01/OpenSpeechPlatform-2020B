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
        .leftBtn{

            padding: 0rem;
            font-size: 2.5rem;
            line-height:1.5;
            border-radius:.3rem;
            width: 100px;
            height: 90px;
            align-self: center;
        }

        .rightBtn{

            padding: 0rem;
            font-size: 2.5rem;
            line-height:1.5;
            border-radius:.3rem;
            left:0;
            width:100px;
            height: 90px;
            align-self: center;
        }

        .okay{
            padding: 0rem;
            font-size: 2rem;
            line-height:1.5;
            border-radius:.3rem;
            left:0;
            width:90px;
            height: 90px;
            align-self: center;
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
        }


    </style>

</head>

<body style="background-color: #e8ecf1;">

    <div class="container-fluid" style="margin-bottom: 2px">
        <div class="row">
            <div  class="col-sm menu">
                <button type="button" class="btn btn-outline-dark btn-block" style="background-color: white;" data-toggle="modal" data-target="#infoModal">
                    ID: {{ $listener->listener_id  }}
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

        <div class="container" style="margin-top: 0.5rem;">
            <div class="row" style="margin-bottom: 0.5rem">
                <div class="col-12 text-center" style="align-items:center">
                    <button type="button" id="finish_button" class="okay btn-info" style="visibility: hidden">
                        Finish
                    </button>
                </div>
            </div>

            <div class="row" >
                <div class="col-6 text-center" >
                    <button type="button" id="save_button" class="save btn-info">
                        Save
                    </button>
                </div>

                <div class="col-6 text-center" style="align-items:center">
                    <button type="button" id="save_as_modal_trigger" class="saveAs btn-info" data-toggle="modal" data-target="#saveAsModal">
                        Save As
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
                        <input id="modalInput" name="programName" type="text" placeholder="Quiet, Noisy, etc.">
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
                        <h5 class="modal-title" id="infoModalLabel">Parameters</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p id="infoModalText">No info to show</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


    <script language="JavaScript">

        /** Initialize parameters **/
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
            'app_behavior': 1 //0 - one by one, 1 - volume only, 2 - all 3
        };

        var programId = -1;



        /** Pull parameters if they have been passed through **/
        if("{{$parameters}}" != ""){
            var parameters = JSON.parse("{{$parameters}}".replace(/&quot;/g,'"'));
            console.log("parameters passed through:", parameters);
        }

        if("{{$program_id}}" != null && "{{$program_id}}" != ""){
            this['programId'] = '{{$program_id}}';
            console.log("program_id passed through", this['programId']);
        }


        //store the starting g65 so that we may use it in updating function
        var starting_g65 = this['parameters']['g65'].slice();


        /** Set up sequence based on parameters passed through **/
        var sequence;
        //case: sequence
        if(parameters['app_behavior'] === 0) {
            if (parameters['sequence_num'] === 3) {
                if (parameters['sequence_order'] === 0) {
                    //3,V: [V, H, V, L, three]
                    sequence = ['V', 'H', 'V', 'L', 3];
                } else {
                    //3,H: [H, V, H, L, three]
                    sequence = ['H', 'V', 'H', 'L', 3];
                }
            }
            else {
                if (parameters['sequence_order'] === 0) {
                    //2,V: [V, H, two]
                    sequence = ['V', 'H', 2];
                } else {
                    //2,H: [H, V, two]
                    sequence = ['H', 'V', 2];
                }
            }
        }
        //case: loudness only
        else if(parameters['app_behavior'] === 1){
            sequence = [1];
        }
        //case: either all three or two, without sequence
        else{
            sequence = [parameters['sequence_num']];
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



        /** Fullness button control **/

        $('#l_less').click(function(){
            decrement('l_value', 'l_step', 'l_min');
            logButtonPress('l_less');
        });

        $('#l_okay').click(function(){
            nextPanel();
            logButtonPress('l_okay');
        });

        $('#l_more').click(function(){
            increment('l_value', 'l_step', 'l_max');
            logButtonPress('l_more');
        });



        /** Loudness button control **/

        $('#v_less').click(function(){
            decrement('v_value', 'v_step', 'v_min');
            logButtonPress('v_less');
        });

        $('#v_okay').click(function(){
            nextPanel();
            logButtonPress('v_okay');
        });

        $('#v_more').click(function(){
            increment('v_value', 'v_step', 'v_max');
            logButtonPress('v_more');
        });



        /** Crispness button control **/

        $('#h_less').click(function(){
            decrement('h_value', 'h_step', 'h_min');
            logButtonPress('h_less');
        });

        $('#h_okay').click(function(){
            nextPanel();
            logButtonPress('h_okay');
        });

        $('#h_more').click(function(){
            increment('h_value', 'h_step', 'h_max');
            logButtonPress('h_more');
        });



        /** Finish button control **/

        $('#finish_button').click(function(){
            nextPanel();
            logButtonPress('finish_button');
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
            console.log("Info modal opened");
            monitorValues();
        });



        /**
         * Logic to handle panel changes. Uses variable 'sequence' array to see what goes next.
         */
        function nextPanel(){

            hideAllPanels();

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
                        document.getElementById("finish_button").style.visibility = "hidden";
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
                document.getElementById("save_button").style.visibility = "visible";
                document.getElementById("save_as_modal_trigger").style.visibility = "visible";
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
            document.getElementById("save_as_modal_trigger").style.visibility = "hidden";
        }



        /**
         * Calculate g50[id] and g80[id] based on g65[id] and compression_ration[id]
         */
        function crg65(id){
            var cr = this['parameters']['compression_ratio'][id];
            if (cr != 0) {
                slope = (1 - cr) / cr;
                var g65 = this['parameters']['g65'][id];
                var g50 = g65 - (slope * 15);
                var g80 = g65 + (slope * 15);
                this['parameters']['g50'][id] = g50;
                this['parameters']['g80'][id] = g80;
            }
        }

        /**
         * Calculate g65[id] and compression_ration[id] based on g50[id] and g80[id]
         */
        function g50g80(id){
            var g50 = parameters['g50'][id];
            var g80 = parameters['g80'][id];
            var slope = (g80 - g50)/30;

            var g65 = g50 + slope * 15;
            this['parameters']['g65'][id] = g65;
            if(slope != -1){
                var cr = Math.round( (1 / (1 + slope)) * 10 ) / 10;
                this['parameters']['compression_ratio'][id] = cr;
            }
        }



        /**
         * Calculate new value for l/v/h and check to see if it exceeds maximum. If so show warning, otherwise update
         * values and transmit.
         */
        function increment(id, step_type, max){
            var old_value = +document.getElementById(id).innerHTML; //plus sign is to denote as integer
            var new_value = old_value + this['parameters'][step_type];

            if(new_value <= this['parameters'][max]){
                document.getElementById(id).innerHTML = new_value;

                var l_val = +document.getElementById("l_value").innerHTML;
                var v_val = +document.getElementById("v_value").innerHTML;
                var h_val = +document.getElementById("h_value").innerHTML;

                //update parameters
                for(i = 0; i < 6; i++){
                    this['parameters']['g65'][i] = this['starting_g65'][i] + v_val + (l_val * this['parameters']['multiplier_l'][i]) + (h_val * this['parameters']['multiplier_h'][i]);
                    crg65(i);
                }

                //send values to hearing aid
                transmit();

            }
            else{
                alert("Warning: Cannot exceed maximum value for " + step_type);
            }
        }

        /**
         * Calculate new value for l/v/h and check to see if it exceeds minimum. If so show warning, otherwise update
         * values and transmit.
         */
        function decrement(id, step_type, min){
            var old_value = +document.getElementById(id).innerHTML; //plus sign is to denote as integer
            var new_value = old_value - this['parameters'][step_type];

            if(new_value >= this['parameters'][min]){
                document.getElementById(id).innerHTML = new_value;

                var l_val = +document.getElementById("l_value").innerHTML;
                var v_val = +document.getElementById("v_value").innerHTML;
                var h_val = +document.getElementById("h_value").innerHTML;

                //update parameters
                for(i = 0; i < 6; i++){
                    this['parameters']['g65'][i] = this['starting_g65'][i] + v_val + (l_val * this['parameters']['multiplier_l'][i]) + (h_val * this['parameters']['multiplier_h'][i]);
                    crg65(i);
                }

                //send values to hearing aid
                transmit();

            }
            else{
                alert("Warning: Cannot exceed minimum value for " + step_type);
            }

        }



        /**
         * Make a POST request to /api/params with the proper data to update the state of the hearing aid. This should
         * be done after any changes to the values are made and upon loading the page.
         */
        function transmit(){
            $.ajax({
                method: 'POST',
                url: '/api/params',
                data: JSON.stringify({
                    user_id: this['listener_id'],
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
                    data: JSON.stringify([
                        this['programId'],
                        this['parameters']
                    ]),
                    success: function(response){
                        console.log(response);
                        alert('Program saved');
                        location.href= '{{ url('programs') }}';
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
                    data: JSON.stringify([
                        document.getElementById("modalInput").value,
                        this['parameters']
                    ]),
                    success: function(response){
                        console.log(response);
                        $('#saveAsModal').modal('hide');
                        location.href= '{{ url('programs') }}';
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

                    parameters['g50'] = params['left']['g50'];
                    parameters['g80'] = params['left']['g80'];
                    parameters['knee_low'] = params['left']['knee_low'];
                    parameters['mpo_limit'] = params['left']['knee_high'];
                    parameters['afc'] = params['left']['afc'];
                    parameters['attack'] = params['left']['attack'];
                    parameters['release'] = params['left']['release'];
                    for(i = 0; i < 6; i++){
                        g50g80(i);
                    }
                    document.getElementById("infoModalText").innerHTML = JSON.stringify(
                        {
                            CR: parameters['compression_ratio'],
                            G65: parameters['g65'],
                            KL: parameters['knee_low'],
                            MPO: parameters['mpo_limit'],
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

            $.ajax({
                method: 'POST',
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                url: '/goldilocks/listener/log',
                data: JSON.stringify({
                   action: button_id,
                   program_state: parameters,
                   lvh_values: {
                       l_value: l_val,
                       v_value: v_val,
                       h_value: h_val
                   }
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

    </script>

</body>

</html>
