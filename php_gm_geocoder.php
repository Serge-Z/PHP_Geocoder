<?php

function geocode($location, $latLng='', $bounds='') {
	$params = array('address'	=>	$location,
		'latlng'	=>	trim(str_replace(' ', '', $latLng), '()'),
		'bounds'	=>	trim(str_replace('),(','|', str_replace(' ', '', $bounds)), '()'),
		'region'	=>	'US',
		'sensor'	=>	'true');
	$ch = curl_init('https://maps.googleapis.com/maps/api/geocode/json?'. http_build_query($params));
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

?>