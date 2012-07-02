<?php

function geocode($location, $latLng='', $bounds='', $coordonly = true) {
	/*
	 * Mostly used to take any input and turn into a lat/lng coordinate.
	 * For that reason, a PHP object is returned with "lat" and "lng".
	 * To return the entire object, set $coordonly to FALSE.
	 *
	 * Uses the Google Geocoding API (2500 requests / day), but if that fails, it
	 * falls back to Yahoo! (50,000 / day).
	 *
	 * $latLng and $bounds are used only by Google's Geocoding API. For more
	 * information on that, refer to the documentation on "Viewport Biasing."
	 *
	 * For more information, see Google's documentation here: https://developers.google.com/maps/documentation/geocoding/
	 * and Yahoo's documentation here: http://developer.yahoo.com/geo/placefinder/guide/index.html
	 */

	$params = array('address'	=>	$location,
		'latlng'	=>	(empty($latLng)) ? '' : trim(str_replace(' ', '', $latLng), '()'),
		'bounds'	=>	(empty($bounds)) ? '' : trim(str_replace('),(','|', str_replace(' ', '', $bounds)), '()'),
		'region'	=>	'US',
		'sensor'	=>	'true');
	$ch = curl_init('https://maps.googleapis.com/maps/api/geocode/json?'. http_build_query($params));
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$result = curl_exec($ch);
	curl_close($ch);
	$decoded = json_decode($result);
	if($decoded->status == 'OK') {
		if($coordonly) {
			// returns only latitude and longitude
			return $decoded->results[0]->geometry->location;
		} else {
			return $decoded;
		}
	} else {
		// Why not Yahoo?
		$params = array(	'q'		=>	$location,
							'appid'	=>	'hyqMFu3e'	); // get one from Yahoo
		$ch = curl_init('http://where.yahooapis.com/geocode?'. http_build_query($params));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$result = curl_exec($ch);
		curl_close($ch);
		$parsed = simplexml_load_string($result);
		if($coordonly) {
			$parsed->Result->lat = $parsed->Result->offsetlat;
			$parsed->Result->lng = $parsed->Result->offsetlon;
			return $parsed->Result;
		} else {
			return $parsed;
		}
	}
}

?>