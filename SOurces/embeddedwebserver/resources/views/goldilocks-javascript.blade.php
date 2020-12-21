<!DOCTYPE html>
 	<html>
    	<head>
	      	<!--Import materialize.css-->
	      	<link type="text/css" rel="stylesheet" href="{{ URL::asset('css/materialize/css/materialize.min.css')}}"  media="screen,projection"/>

	      	<!--Let browser know website is optimized for mobile-->
	      	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    	</head>

    	

    	<body style="background-color: #f1f1f1">
	    	<div class="row">
		      	<div class="card-panel col s12 m8 l6 offset-m2 offset-l3 center-align">
						<a class="btn-floating btn-large waves-effect waves-light blue" onclick="increment('l_value', 'l_step', 'l_max')" style="margin-top: 10px"><i class="material-icons">+</i></a>

 						<h3 class="hide" id="l_value" type="integer">5</h3>
		        		<h4>Fullness</h4>
						<a class="btn-floating btn-large waves-effect waves-light blue" onclick="decrement('l_value', 'l_step', 'l_min')" style="margin-bottom: 10px"><i class="material-icons">-</i></a>
		      	</div>
		    </div>

		     <div class="row">
		      	<div class="card-panel col s12 m8 l6 offset-m2 offset-l3 center-align">
		       	 	<a class="btn-floating btn-large waves-effect waves-light blue" onclick="increment('h_value', 'h_step', 'h_max')" style="margin-top: 10px"><i class="material-icons">+</i></a>

		        	<h3 class="hide" id="h_value" type="integer">3</h3>
		        	<h4>Crispness</h4>
					<a class="btn-floating btn-large waves-effect waves-light blue" onclick="decrement('h_value', 'h_step', 'h_min')" style="margin-bottom: 10px"><i class="material-icons">-</i></a>

		      	</div>
    		</div>

		    <div class="row">
		      	<div class="card-panel col s12 m8 l6 offset-m2 offset-l3 center-align">
		       	 	<a class="btn-floating btn-large waves-effect waves-light blue" onclick="increment('v_value', 'v_step', 'v_max')" style="margin-top: 10px"><i class="material-icons">+</i></a>


 					<h3 class="hide" id="v_value" type="integer">10</h3>
		       	 	<h4>Volume</h4>
					<a class="btn-floating btn-large waves-effect waves-light blue" onclick="decrement('v_value', 'v_step', 'v_min')" style="margin-bottom: 10px"><i class="material-icons">-</i></a>
		      	</div>
		     </div>


    		<script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
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
