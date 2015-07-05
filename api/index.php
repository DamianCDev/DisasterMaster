<?php

$apiKey = '8TvT6h8adR3HU2g';

header("Access-Control-Allow-Origin: *");

# Include Instant API's function library.
require_once('class.csv-to-api.php');


$url = $_SERVER['REQUEST_URI'];
$urlApiKey = strstr($url, '&apiKey=');

$urlApiKey = substr($urlApiKey, 8);

if ($urlApiKey == $apiKey) {
	# No Source file is given
	if ( !isset( $_REQUEST['source'] ) ) {
	  echo "No file provided.";
	  die();
	}

	# Create a new instance of the Instant API class.
	$api = new CSV_To_API();

	# Intercept the requested URL and use the parameters within it to determine what data to respond with.
	$api->parse_query();

	# Gather the requested data from its CSV source, converting it into JSON, XML, or HTML.
	$api->parse();

	# Send the JSON to the browser.
	echo $api->output();
}


else {
	echo "INV Invalid Key or no key provided.";
}