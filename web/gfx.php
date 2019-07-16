<?php

function arrowdirection($deg) {
	$style = '-moz-transform: rotate(' . $deg . 'deg); -webkit-transform: rotate(' . $deg . 'deg); -o-transform: rotate(' . $deg . 'deg); -ms-transform: rotate(' . $deg . 'deg); transform: rotate(' . $deg . 'deg); }';
	return $style;
}

function _bearing($lat1, $lon1, $lat2, $lon2) {
 $bearing = (rad2deg(atan2(sin(deg2rad($lon2) - deg2rad($lon1)) * cos(deg2rad($lat2)), cos(deg2rad($lat1)) * sin(deg2rad($lat2)) - sin(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($lon2) - deg2rad($lon1)))) + 360) % 360;
 return $bearing;
}
