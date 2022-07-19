<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function get_geolocation($address = null)
{
	$CI =& get_instance();
	$details_url = "http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address)."&sensor=false";
   			
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $details_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$response = json_decode(curl_exec($ch), true);
	$location = '';
   			// If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
	if ($response['status'] == 'OK') {
		$geometry = $response['results'][0]['geometry'];
		$location = $geometry['location'];
	}

	return $location;
}