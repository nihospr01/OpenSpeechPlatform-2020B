<?php

$addr = gethostbyname("127.0.0.1");

$client = stream_socket_client("tcp://$addr:8001", $errno, $errorMessage);

if ($client === false) {
    throw new UnexpectedValueException("Failed to connect: $errorMessage");
}



        echo "Socket connected successfully. \n";


	//initialize data
	$test_data = [
		"user_id" => 10,
		"method" => "set",
		"request_action" => 1,
		"data" => [
			"left" => [
        			"en_ha" => 1,
        			"rear_mics" => 0,
        			"gain" => -40,
        			"g50" => [10,10,10,10,10,10],
        			"g80" => [10,10,10,10,10,10],
        			"knee_low" => [41,12,43,44,45,46],
        			"knee_high" => [120,121,122,123,124,125],
        			"attack" => [5,5,5,5,5,5],
        			"release" => [20,20,20,20,20,20],
              "mpo" => 120,
              "noise_estimation_type" => 0,
              "spectral_type" => 0,
              "spectral_subtraction" => 0,
              "afc" => 0,
              "afc_delay" => 151,
              "afc_mu" => 0.005,
              "afc_rho" => 0.985,
              "afc_power_estimate" => 0
			],
			"right" => [
        			"en_ha" => 1,
        			"rear_mics" => 0,
        			"gain" => -40,
        			"g50" => [10,10,10,10,10,10],
        			"g80" => [10,10,10,10,10,10],
        			"knee_low" => [41,12,43,44,45,46],
        			"knee_high" => [120,121,122,123,124,125],
        			"attack" => [5,5,5,5,5,5],
        			"release" => [20,20,20,20,20,20],
              "mpo" => 120,
              "noise_estimation_type" => 0,
              "spectral_type" => 0,
              "spectral_subtraction" => 0,
              "afc" => 0,
              "afc_delay" => 152,
              "afc_mu" => 0.005,
              "afc_rho" => 0.985,
              "afc_power_estimate" => 0
			]
    		]

	];





  fwrite($client, json_encode($test_data));
  $json = stream_get_contents($client, -1, -1);
  if($json == "success"){
    echo "This was successful. \n";
  }
  else{
    echo "This was unsuccessful. \n";
  }
  fclose($client);

  $addr = gethostbyname("127.0.0.1");

  $client = stream_socket_client("tcp://$addr:8001", $errno, $errorMessage);

  if ($client === false) {
      throw new UnexpectedValueException("Failed to connect: $errorMessage");
  }



          echo "Socket connected successfully. \n";


  	//initialize data
  	$test_data = [
  		"user_id" => 10,
  		"method" => "get",
  		"request_action" => 1,
  		"data" => []

  	];





  fwrite($client, json_encode($test_data));
  $json = stream_get_contents($client, -1, -1);
  var_dump(json_decode($json, true));
  fclose($client);




?>
