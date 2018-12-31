<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href= "{{ asset('css/all3.css') }}" >
    <title>ADJUST ALL</title>
  </head>
  <body>
  <div class="container-fluid">
    <div class="row">
        <div  class="col-sm menu"><button type="button" class="btn btn-outline-dark btn-block" onclick="location.href = '{{ url('login') }}'">Restart</button></div>
        <div  class="col-sm menu"><button type="button" class="btn btn-outline-dark btn-block">ID: Listener</button></div>
        <div  class="col-sm menu"><button type="button" class="btn btn-outline-dark btn-block" onclick="alert('You will hear a man talking. Adjust Loudness, Crispness, and Fullness to your preference and hit OK each time')">Show Instuctions</button></div>
        <div  class="col-sm menu"><button type="button" class="btn btn-outline-dark btn-block" onclick="showNumbers()">Show Numbers</button></div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row mycontainer">
        <div class="col-sm-4 panel1 mycontainer" id="fullness_panel" style="display:none"><b class="h10">FULLNESS</b>
          <h3 style="display:none" id="l_value" type="integer">5</h3>
          <button type="button" class="btn1 btn-secondary btn-lg btn-block" onclick="increment('l_value', 'l_step', 'l_max')">MORE</button>
          <button type="button" id="fullness_OK" class="btn1 btn-light btn-lg" onclick="fullnessLogic()">OK</button>
          <button type="button" class="btn1 btn-secondary btn-lg btn-block" onclick="decrement('l_value', 'l_step', 'l_min')">LESS</button>

          </div>
        <div class="col-sm-4 panel2 mycontainer" id="loudness_panel"><b class="h10">LOUDNESS</b>
          <h3 style="display:none" id="v_value" type="integer">10</h3>
          <button type="button" class="btn1 btn-secondary btn-lg btn-block" onclick="increment('v_value', 'v_step', 'v_max')">MORE</button>
          <button type="button" id="loudness_OK" class="btn1 btn-light btn-lg" onclick="loudnessLogic()">OK</button>
          <button type="button" class="btn1 btn-secondary btn-lg btn-block" onclick="decrement('v_value', 'v_step', 'v_min')">LESS</button>

        </div>
        <div class="col-sm-4 panel3 mycontainer" id="crispness_panel" style="display:none"><b class="h10" >CRISPNESS</b>
          <h3 style="display:none" id="h_value" type="integer">3</h3>
          <button type="button" class="btn1 btn-secondary btn-lg btn-block" onclick="increment('h_value', 'h_step', 'h_max')">MORE</button>
          <button type="button" id="crispness_OK" class="btn1 btn-light btn-lg" onclick="crispnessLogic()">OK</button>
          <button type="button" class="btn1 btn-secondary btn-lg btn-block" onclick="decrement('h_value', 'h_step', 'h_min')">LESS</button>

        </div>
      </div>
    </div>

    <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/popper.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{ URL::asset('css/materialize/js/materialize.min.js')}}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/bytebuffer.min.js')}}"></script>


    <script type='text/javascript' >

        var g50 = [0,0,0,0,0,0];
        var g80 = [0,0,0,0,0,0];
        var knee_low_values = [45,45,45,45,45,45];
        var mpo_Limit_values = [100,100,100,100,100,100];
        var attack_time = [555,555,555,555,555,555];
        var release_time = [202,202,202,202,202,202];

        update_arrays();


        var comp_ratio = [1.0,1.0,1.0,1.0,1.0,1.0];

        var l_step = 2;
        var v_step = 2;
        var h_step = 1;

        var l_max = 21;
        var v_max = 40;
        var h_max = 20;

        var l_min = 0;
        var v_min = 0;
        var h_min = 0;

        var flag = 0;

        //display
        function writeArrayToConsole(){
          console.log ("g50 " + g50.toString());
          console.log ("g80 " + g80.toString());
        }

        function showNumbers(){
          alert ("g50 " + g50.toString() + "\n" + "g80 " + g80.toString());
        }

        function loudnessLogic(){
          if (flag == 0){
            showCrispness_d();
            var elem2 = document.getElementById("crispness_OK");
            elem2.style.visibility = "visible";
            hideLoudness_d();
            hideFullness_d();
            flag = 1;
          }
          else if (flag == 1){
            //hideCrispness_d();
            var elem1 = document.getElementById("fullness_OK");
            elem1.style.visibility = "visible";
            hideLoudness_d();
            showFullness_d();
            flag = 2;
          }
          else if (flag == 2){
            //hideCrispness_d();
          }
          //console.log ("from my logic flag " + flag);
        }

        function crispnessLogic(){
          //console.log ("from show crispness flag " + flag);
          //hideFullness_d();
          showLoudness_d();
          hideCrispness_d();
        }

        function fullnessLogic(){
          //console.log ("from show crispness flag " + flag);

          //hideFullness_d();
          showLoudness_d();
          showCrispness_d();
          showFullness_d();
          var elem1 = document.getElementById("fullness_OK");
          elem1.style.visibility = "hidden";

          var elem2 = document.getElementById("crispness_OK");
          elem2.style.visibility = "hidden";
        }


        function showFullness_d(){
          var elem = document.getElementById("fullness_panel");
          elem.style.display = "block";
        }

        function hideFullness_d(){
          var elem = document.getElementById("fullness_panel");
          elem.style.display = "none";
        }

        function showLoudness_d(){
          //console.log ("from hide loudness flag display" + elem.style.display);
          var elem = document.getElementById("loudness_panel");
          elem.style.display = "block";
          //console.log ("from show loudness flag" + flag);
        }

        function hideLoudness_d(){
          var elem = document.getElementById("loudness_panel");
          elem.style.display = "none";
        }

        function showCrispness_d(){
          var elem = document.getElementById("crispness_panel");
          elem.style.display = "block";
        }

        function hideCrispness_d(){
          var elem = document.getElementById("crispness_panel");
          elem.style.display = "none";

        }

      //******  Will need to update this function when full implementation is in place...
      function update_arrays(){
        var l_val = +document.getElementById("l_value").innerHTML;
        var v_val = +document.getElementById("v_value").innerHTML;
        var h_val = +document.getElementById("h_value").innerHTML;

        //console.log(l_val, v_val, h_val);

        this['g50'][0] = v_val - (2 * l_val);
        this['g80'][0] = v_val - (2 * l_val);

        this['g50'][1] = v_val - l_val;
        this['g80'][1] = v_val - l_val;

        for(i = 2; i < 6; i++){
          this['g50'][i] = v_val + h_val;
          this['g80'][i] = v_val + h_val;
        }

        send_params();
      }

      function increment(id, step_type, max){
        //plus sign is to denote as integer
        old_value = +document.getElementById(id).innerHTML;
        new_value = old_value + this[step_type];
        if(new_value <= this[max]){
          document.getElementById(id).innerHTML = new_value;
          update_arrays();
        }
      }

      function decrement(id, step_type, min){
        //plus sign is to denote as integer
        old_value = +document.getElementById(id).innerHTML;
        new_value = old_value - this[step_type];
        if(new_value >= this[min]){
          document.getElementById(id).innerHTML = new_value;
          update_arrays();
        }

      }

      function send_params(){

        $.ajax({
            method: 'POST', // Type of response and matches what we said in the route
            url: '/api/params', // This is the url we gave in the route
            data: JSON.stringify({
            noOp: 0,
            afc: 1,
            feedback: 1,
            rear: 1,
            g50: this['g50'],
            g80: this['g80'],
            kneelow: this['knee_low_values'],
            mpoLimit: this['mpo_Limit_values'],
            attackTime: this['attack_time'],
            releaseTime: this['release_time']
            }), // a JSON object to send back
            success: function(response){ // What to do if we succeed
                console.log(response);
            },
            error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
                console.log(JSON.stringify(jqXHR));
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            }
        });
      }

    </script>
  </body>
</html>
