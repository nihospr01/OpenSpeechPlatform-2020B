<html lang="en">

<head>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	
	<style>
/*		.table-field{
			text-align: center;
			font-size: 0.5rem;
			width: 2rem;
		}
*/
		.center-text{
			text-align: center;
		}


		th {font-size:0.5rem;} /*1rem = 16px*/

		@media (min-width: 544px) {  
		  	th {font-size:1rem;} /*1rem = 16px*/
/*
			.table-field{
				text-align: center;
				font-size: 1rem;
				width: 4rem;
			}*/

		}
		 
		/* Medium devices (tablets, 768px and up) The navbar toggle appears at this breakpoint */
		@media (min-width: 768px) {  
		  	th {font-size:1rem;} /*1rem = 16px*/
/*
		  	.table-field{
				text-align: center;
				font-size: 1rem;
				width: 4rem;
			}*/

		}
		 
		/* Large devices (desktops, 992px and up) */
		@media (min-width: 992px) { 
		  	th {font-size:1rem;} /*1rem = 16px*/
/*
		  	.table-field{
				text-align: center;
				font-size: 1rem;
				width: 5rem;
			}*/

		}
		 
		/* Extra large devices (large desktops, 1200px and up) */
		@media (min-width: 1200px) {  
		  	th {font-size:1rem;} /*1rem = 16px*/
/*
		  	.table-field{
				text-align: center;
				font-size: 1rem;
				width: 5rem;
			}*/
		}
	</style>


	<title>Master Hearing Aid Control</title>

</head>


<body class="bg-light">

	</br>

	<div class="container">
	  	<div class="row">
		    <div class="col-lg-6">
		    	<h4 style="text-align: center">Client Data</h4>

		    	<div class="input-group mb-3">
		  			<div class="input-group-prepend">
		    			<span class="input-group-text" id="gender_addon">Gender</span>
		  			</div>
					<select class="custom-select" id="gender_select" name="gender">
					   	<option value="-1" selected>Choose...</option>
					    <option value="1">Male</option>
					    <option value="2">Female</option>
					    <option value="0">Decline to state</option>
					</select>
		  		</div>

		  		<div class="input-group mb-3">
		  			<div class="input-group-prepend">
		    			<span class="input-group-text" id="dob_addon"">Date of birth</span>
		  			</div>
		  			 <input type='text' class="form-control" id="dob" name="dob" placeholder="yyyymmdd" />
		  		</div>

			  	<div class="form-group">
				    <label for="tonal_language">Do you speak a tonal language?</label>
					<select class="custom-select" id="tonal_select" name="tonal">
					   	<option value="-1" selected>Choose...</option>
					    <option value="1">Yes</option>
					    <option value="0">No</option>
					</select>
				</div>

		    </div>
		    <div class="col-lg-6">
		    	<h4 style="text-align: center">Hearing Aid History</h4>

		    	<div class="input-group mb-3">
		  			<div class="input-group-prepend">
		    			<span class="input-group-text" id="exp_addon"">Experience</span>
		  			</div>
					<select class="custom-select" id="experience_select" name="experience">
					   	<option value="-1" selected>Choose...</option>
					    <option value="0">Experienced User</option>
					    <option value="1">New User</option>
					</select>
		  		</div>

		    	<div class="input-group mb-3">
		  			<div class="input-group-prepend">
		    			<span class="input-group-text" id="device_addon">Device</span>
		  			</div>
					<select class="custom-select" id="device_select" name="haType">
					   	<option value="-1" selected>Choose...</option>
					    <option value="0">CIC</option>
					    <option value="1">ITC</option>
					    <option value="2">ITE</option>
					    <option value="3">BTE</option>
					</select>
		  		</div>

		    	<div class="input-group mb-3">
		  			<div class="input-group-prepend">
		    			<span class="input-group-text" id="number_addon"">#</span>
		  			</div>
					<select class="custom-select" id="number_select" name="numAids">
					   	<option value="-1" selected>Choose...</option>
					    <option value="0">Unilateral</option>
					    <option value="1">Bilateral</option>
					</select>
		  		</div>
		    </div>
	  	</div>
	</div>

		

	<div class="container" style="background-color: #FFFFFF">
		<h4 class="center-text" style="padding-top: 10; padding-bottom: 10;">Audiogram</h4>

		<table class="table">
			<thead>
				<th class="center-text">250</th>
				<th class="center-text">500</th>
				<th class="center-text">1000</th>
				<th class="center-text">2000</th>
				<th class="center-text">4000</th>
				<th class="center-text">8000</th>
				</thead>
				<tbody>
					<tr>
					<td><input id="audiogram_0" class="form-control form-control-sm table-field" type="number" placeholder=""></td>
					<td><input id="audiogram_1" class="form-control form-control-sm table-field" type="number" placeholder=""></td>
					<td><input id="audiogram_2" class="form-control form-control-sm table-field" type="number" placeholder=""></td>
					<td><input id="audiogram_3" class="form-control form-control-sm table-field" type="number" placeholder=""></td>
					<td><input id="audiogram_4" class="form-control form-control-sm table-field" type="number" placeholder=""></td>
					<td><input id="audiogram_5" class="form-control form-control-sm table-field" type="number" placeholder=""></td>
					</tr>
				</tbody>
		</table>
	</div>

	<div class="container">
		<div class="row">
			<div class="col">
				<button type="button" class="btn btn-secondary float-right" onclick="getParameters()">Get Parameters</button>
			</div>
		</div>
	</div>


	</br>

	<div class="container" style="background-color: #FFFFFF">
		<h4 class="center-text" style="padding-top: 10; padding-bottom: 10;">Parameters</h4>

  		<div class="table-responsive">
  			<table class="table">
  				<thead>
  					<th>Freqeuncy</th>
  					<th class="center-text">177</th>
  					<th class="center-text">354</th>
  					<th class="center-text">707</th>
  					<th class="center-text">1414</th>
  					<th class="center-text">2828</th>
  					<th class="center-text">5657</th>
  				</thead>

  				<tbody>

  					<tr>
  						<th>Compression Ratio</th>
  						<td><input id="cr_0" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="cr_1" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="cr_2" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="cr_3" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="cr_4" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="cr_5" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  					</tr>

  					<tr>
  						<th>G50</th>
  						<td><input id="g50_0" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="g50_1" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="g50_2" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="g50_3" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="g50_4" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="g50_5" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  					</tr>

  					<tr>
  						<th>G65</th>
  						<td><input id="g65_0" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="g65_1" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="g65_2" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="g65_3" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="g65_4" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="g65_5" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  					</tr>

    				<tr>
  						<th>G80</th>
  						<td><input id="g80_0" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="g80_1" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="g80_2" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="g80_3" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="g80_4" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="g80_5" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  					</tr>

    					<tr>
  						<th>Knee Low</th>
  						<td><input id="kneelow_0" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="kneelow_1" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="kneelow_2" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="kneelow_3" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="kneelow_4" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="kneelow_5" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  					</tr>

    				<tr>
  						<th>Knee High</th>
  						<td><input id="kneehigh_0" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="kneehigh_1" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="kneehigh_2" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="kneehigh_3" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="kneehigh_4" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="kneehigh_5" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  					</tr>

  					<tr>
  						<th>Attack</th>
  						<td><input id="attack_0" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="attack_1" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="attack_2" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="attack_3" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="attack_4" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="attack_5" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  					</tr>

  					<tr>
  						<th>Release</th>
  						<td><input id="release_0" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="release_1" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="release_2" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="release_3" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="release_4" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  						<td><input id="release_5" class="form-control form-control-sm table-field" type="number" placeholder="0"></td>
  					</tr>
  				</tbody>

  			</table>
		</div>
	</div>

	</br>

	<div class="container">
		<div class="row">
			<div class="col">
				<button type="button" class="btn btn-primary float-right" onclick="submitParameters()">Transmit</button>
			</div>
		</div>
		<div class="row">
			<div class="alert alert-danger" role="alert" id="not_connected_alert" style="visibility: hidden">
				<strong>Error!</strong> Looks like you're not connected to a hearing aid.
			</div>
			<div class="alert alert-success" role="alert" id="success_alert" style="visibility: hidden">
				<strong>Success!</strong> Parameters sent.
			</div>
		</div>
	</div>

	</br>



<!-- 	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
 -->
 <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<script>

	function validateInfo() {

	    var warning_string = "";


	    var gender = document.getElementById("gender_select").value;
	    if(gender == "-1"){
	        warning_string = warning_string.concat("-Please select a gender\n");
	    }

	    var dob = document.getElementById("dob").value;
	    if(dob == "" || dob.length != 8){
	        warning_string = warning_string.concat("-Please set DOB (yyyymmdd)\n");
	    }

	    var language = document.getElementById("tonal_select").value;
	    if(language == "-1"){
	        warning_string = warning_string.concat("-Please check whether you speak a tonal language\n");
	    }

	    var exp = document.getElementById("experience_select").value;
	    if(exp == "-1"){
	        warning_string = warning_string.concat("-Please select your experience\n");
	    }

	    var device = document.getElementById("device_select").value;
	    if(device == "-1"){
	        warning_string = warning_string.concat("-Please select device type\n");
	    }

	    var numAids = document.getElementById("number_select").value;
	    if(numAids == "-1"){
	        warning_string = warning_string.concat("-Please select number of hearing aids\n");
	    }

	    //final check
	    if(warning_string != ""){
	        alert(warning_string);
	        return false;
	    }
	    else{
	    	return true;
	    }

	}

	function getParameters(){

		if(validateInfo()){
			$.ajax({
			    method: 'POST', // Type of response and matches what we said in the route
			    url: '/nalnl2', // This is the url we gave in the route
			    data: {
			    	"_token": "{{ csrf_token() }}",
					'gender': document.getElementById("gender_select").value,
					'dob': document.getElementById("dob").value,
					'tonal': document.getElementById("tonal_select").value,
					'experience': document.getElementById("experience_select").value,
					'ha_type': document.getElementById("device_select").value,
					'num_aids': document.getElementById("number_select").value,
					'audiogram': [
									document.getElementById("audiogram_0").value,
									document.getElementById("audiogram_1").value,
									document.getElementById("audiogram_2").value,
									document.getElementById("audiogram_3").value,
									document.getElementById("audiogram_4").value,
									document.getElementById("audiogram_5").value,
								]
			    }, // a JSON object to send back
			    success: function(response){ // What to do if we succeed
			        console.log(response);
			        var parameters = JSON.parse(response);

			        document.getElementById("cr_0").value = parameters.compression_ratio[0];
			        document.getElementById("cr_1").value = parameters.compression_ratio[1];
			        document.getElementById("cr_2").value = parameters.compression_ratio[2];
			        document.getElementById("cr_3").value = parameters.compression_ratio[3];
			        document.getElementById("cr_4").value = parameters.compression_ratio[4];
			        document.getElementById("cr_5").value = parameters.compression_ratio[5];

			        document.getElementById("g50_0").value = parameters.g50[0];
			        document.getElementById("g50_1").value = parameters.g50[1];
			        document.getElementById("g50_2").value = parameters.g50[2];
			        document.getElementById("g50_3").value = parameters.g50[3];
			        document.getElementById("g50_4").value = parameters.g50[4];
			        document.getElementById("g50_5").value = parameters.g50[5];

			        document.getElementById("g80_0").value = parameters.g80[0];
			        document.getElementById("g80_1").value = parameters.g80[1];
			        document.getElementById("g80_2").value = parameters.g80[2];
			        document.getElementById("g80_3").value = parameters.g80[3];
			        document.getElementById("g80_4").value = parameters.g80[4];
			        document.getElementById("g80_5").value = parameters.g80[5];

			        document.getElementById("g65_0").value = parameters.g65[0];
			        document.getElementById("g65_1").value = parameters.g65[1];
			        document.getElementById("g65_2").value = parameters.g65[2];
			        document.getElementById("g65_3").value = parameters.g65[3];
			        document.getElementById("g65_4").value = parameters.g65[4];
			        document.getElementById("g65_5").value = parameters.g65[5];

			        document.getElementById("kneelow_0").value = parameters.knee_low[0];
			        document.getElementById("kneelow_1").value = parameters.knee_low[1];
			        document.getElementById("kneelow_2").value = parameters.knee_low[2];
			        document.getElementById("kneelow_3").value = parameters.knee_low[3];
			        document.getElementById("kneelow_4").value = parameters.knee_low[4];
			        document.getElementById("kneelow_5").value = parameters.knee_low[5];

			        document.getElementById("kneehigh_0").value = parameters.knee_high[0];
			        document.getElementById("kneehigh_1").value = parameters.knee_high[1];
			        document.getElementById("kneehigh_2").value = parameters.knee_high[2];
			        document.getElementById("kneehigh_3").value = parameters.knee_high[3];
			        document.getElementById("kneehigh_4").value = parameters.knee_high[4];
			        document.getElementById("kneehigh_5").value = parameters.knee_high[5];

			        document.getElementById("attack_0").value = parameters.attack[0];
			        document.getElementById("attack_1").value = parameters.attack[1];
			        document.getElementById("attack_2").value = parameters.attack[2];
			        document.getElementById("attack_3").value = parameters.attack[3];
			        document.getElementById("attack_4").value = parameters.attack[4];
			        document.getElementById("attack_5").value = parameters.attack[5];

					document.getElementById("release_0").value = parameters.release[0];
			        document.getElementById("release_1").value = parameters.release[1];
			        document.getElementById("release_2").value = parameters.release[2];
			        document.getElementById("release_3").value = parameters.release[3];
			        document.getElementById("release_4").value = parameters.release[4];
			        document.getElementById("release_5").value = parameters.release[5];
			    },
			    error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
			        console.log(JSON.stringify(jqXHR));
			        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
			    }
			});
		}
	}

	function submitParameters(){
		document.getElementById("not_connected_alert").style.visibility = "hidden";
		document.getElementById("success_alert").style.visibility = "hidden";
		$.ajax({
		    method: 'POST', // Type of response and matches what we said in the route
		    url: '/nalnl2update', // This is the url we gave in the route
		    data: {
		    	"_token": "{{ csrf_token() }}",
				'noOp': 0,
				'afc': 1,
				'feedback': 1,
				'rear': 1,
				'g50': [
			        document.getElementById("g50_0").value,
			        document.getElementById("g50_1").value,
			        document.getElementById("g50_2").value,
			        document.getElementById("g50_3").value,
			        document.getElementById("g50_4").value,
			        document.getElementById("g50_5").value
				],
				'g80': [
			        document.getElementById("g80_0").value,
			        document.getElementById("g80_1").value,
			        document.getElementById("g80_2").value,
			        document.getElementById("g80_3").value,
			        document.getElementById("g80_4").value,
			        document.getElementById("g80_5").value
				],
				'knee_low': [
			        document.getElementById("kneelow_0").value,
			        document.getElementById("kneelow_1").value,
			        document.getElementById("kneelow_2").value,
			        document.getElementById("kneelow_3").value,
			        document.getElementById("kneelow_4").value,
			        document.getElementById("kneelow_5").value
				],
				'knee_high': [	
			        document.getElementById("kneehigh_0").value,
			        document.getElementById("kneehigh_1").value,
			        document.getElementById("kneehigh_2").value,
			        document.getElementById("kneehigh_3").value,
			        document.getElementById("kneehigh_4").value,
			        document.getElementById("kneehigh_5").value
				],
				'attack': [
			        document.getElementById("attack_0").value,
			        document.getElementById("attack_1").value,
			        document.getElementById("attack_2").value,
			        document.getElementById("attack_3").value,
			        document.getElementById("attack_4").value,
			        document.getElementById("attack_5").value						
				],
				'release': [
			        document.getElementById("release_0").value,
			        document.getElementById("release_1").value,
			        document.getElementById("release_2").value,
			        document.getElementById("release_3").value,
			        document.getElementById("release_4").value,
			        document.getElementById("release_5").value
				]
		    }, // a JSON object to send back
		    success: function(response){ // What to do if we succeed
		        console.log(response);
		        document.getElementById("success_alert").style.visibility = "visible";
		    },
		    error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
		        console.log(JSON.stringify(jqXHR));
		        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
		        document.getElementById("not_connected_alert").style.visibility = "visible";
		    }
		});
	}


</script>


</body>
