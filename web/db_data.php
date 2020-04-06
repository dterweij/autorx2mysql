<?php
include("gfx.php");
$dbt = new MyDb();

function CurrentSondes() {
	global $dbt,$_nearbysonde,$mylat,$mylon;

	$SQL = "SELECT * FROM `sondedata` WHERE `last_date` >= NOW() - INTERVAL 10 MINUTE ORDER BY `last_date` DESC";
	$result = $dbt -> select($SQL);
	$sondes = count($result); 

	// No data for 1 day? most likly the logger script is not running or auto rx is crashed
	$SQL = "SELECT * FROM `sondedata` WHERE `last_date` >= NOW() - INTERVAL 1 DAY ORDER BY `last_date` DESC";
	$hartbeatresult = $dbt -> select($SQL);
	$hartbeat = count($hartbeatresult); 
	
	if ($sondes > 4)
		$sondes = 4; // max 4 columns = max 4 receivers/sondes data

if ($sondes >= 1) {
	for ($i = 0; $i <= ( $sondes - 1 ); $i++) {
	$date = MyDateFormat($result[$i]['last_date']);
	$_distance = number_format(_Rounding($result[$i]['distance'],1), 1);
	if ( $_distance < $_nearbysonde ) {
	$_distance = '<span class="sondeisclose">' . $_distance . '</span>'; 	
	}
	$bearing = _Rounding(_bearing($mylat,$mylon,$result[$i]['lat'],$result[$i]['lon']),0);
    
	echo '<div class="column">';
	echo '<div class="textbox">';
	echo '<div class="titel-box">Current ( Seen last 10 minutes ) <i class="fa fa-1x fa-exclamation-circle blink"></i></div>';

	echo '<div class="divTable myTable">';
	echo '<div class="divTableBody">';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Date:</div>';
	echo '<div class="divTableCell">'. $date . '</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Station:</div>';
	echo '<div class="divTableCell">'. _Station($result[$i]['station']) . '</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Callsign:</div>';
	echo '<div class="divTableCell">'. $result[$i]['callsign'] . '</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Frame:</div>';
	echo '<div class="divTableCell">'. $result[$i]['frame'] . '</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Altitude:</div>';
	echo '<div class="divTableCell">'. _Rounding($result[$i]['alt'],1) . ' M</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Distance:</div>';
	echo '<div class="divTableCell">' . _getColor($_distance) . ' KM</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Frequency:</div>';
	echo '<div class="divTableCell">'. $result[$i]['freq'] . '</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Bearing:</div>';
	echo '<div class="divTableCell">'. $bearing . '&deg;</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Direction:</div>';
	echo '<div class="divTableCell">'. $result[$i]['direction'] . '</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Model:</div>';
	echo '<div class="divTableCell">'. $result[$i]['model'] . '</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Latitude:</div>';
	echo '<div class="divTableCell">'. $result[$i]['lat'] . '</div>';
	echo '</div>';
	
	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Longtitude:</div>';
	echo '<div class="divTableCell">'. $result[$i]['lon'] . '</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Battery:</div>';
	echo '<div class="divTableCell">'. $result[$i]['batt'] . ' V</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Temperature:</div>';
	echo '<div class="divTableCell">'. $result[$i]['temp'] . '&deg;C</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">GPS Satelites:</div>';
	echo '<div class="divTableCell">'. $result[$i]['sats'] . '</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Baloon speed:</div>';
	echo '<div class="divTableCell">'. _Rounding($result[$i]['speed'],1) . ' m/s</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Evelation:</div>';
	echo '<div class="divTableCell">'. _Rounding($result[$i]['evel'],1) . '&deg;</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Humidity:</div>';
	echo '<div class="divTableCell">'. $result[$i]['hum'] . '%</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">BT (what is this?):</div>';
	echo '<div class="divTableCell">'. $result[$i]['bt'] . '</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Nearest City:</div>';
	echo '<div class="divTableCell">Not yet implemented</div>';
	echo '</div>';
	
	echo '</div>';
	echo '</div>';

	echo '</div>';
	echo '</div>';
	}
}
	if ($sondes < 1 && $hartbeat > 1) {
	echo '<div class="column">';
	echo '<div class="textbox">';
	echo '<div class="titel-box">Nothing?</div>';

	echo '<div class="divTable myTable">';
	echo '<div class="divTableBody">';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">There is no radio sonde in the range of your receiver.<br>UDP to MySQL logger and auto_rx might be running.<br>Everything seems to be operational.</div>';
	echo '</div>';

	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	}

	if ($sondes < 1 && $hartbeat < 1) {
	echo '<div class="column">';
	echo '<div class="textbox">';
	echo '<div class="titel-box">Alert!</div>';

	echo '<div class="divTable myTable">';
	echo '<div class="divTableBody">';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell"><span class="bold" style="color: red;">UDP to MySQL logger script seems not running or auto_rx is not running!</span>(Reason: no data seen in last 24 hours)</div>';
	echo '</div>';

	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	}

	
}

function TableLatestSondes($si) {
global $dbt,$_nearbysonde,$mylon,$mylat;

$result = $dbt -> getlatestSondes($si);
$sondes = count($result); 

echo '<table class="blueTable">';
echo '<thead><tr>';
echo '<th>Last Date</th>';
echo '<th>Station</th>';
echo '<th>Callsign</th>';
echo '<th>Map</th>';
echo '<th>Frequency (MHz)</th>';
echo '<th>Altitude (M)</th>';
echo '<th>Direction</th>';
echo '<th>Distance (KM)</th>';
echo '</tr></thead><tbody>';

for ($i = 0; $i <= ( $sondes - 1 ); $i++) {
	$date = MyDateFormat($result[$i]['last_date']);
	echo '<tr>';
 	echo '<td>'. $date . '</td>';
	echo '<td>'. _Station($result[$i]['station']) . '</td>';

	$sondenumber = $result[$i]['callsign'];
	echo '<td>'. $sondenumber . '</td>';

	// Skip DFM/DFN09/iMet as radiosondy and Radio_auto_rx uses different serials
	if ($result[$i]['model'] == "DFM" || $result[$i]['model'] == "iMet" || $result[$i]['model'] == "DFM09") {
		echo '<td align="center">&nbsp;</td>';
	} else {
		echo '<td align="center"><a title="RadioSondy" href="https://radiosondy.info/sonde.php?sondenumber='. $sondenumber . '" target="new" class="linkbutton">RS</a>';
		echo '<a title="SondeHub" href="https://tracker.sondehub.org/?sondehub=1#!mt=osm&mz=7&qm=all&f=RS_'. $sondenumber . '&q=RS_' . $sondenumber . '" target="new" class="linkbutton">SH</a></td>';
	}
	echo '<td>'. str_replace(" MHz","",$result[$i]['freq']) . '</td>';
	echo '<td>'. _Rounding($result[$i]['alt'],1) . '</td>';

	$bearing = _Rounding(_bearing($mylat,$mylon,$result[$i]['lat'],$result[$i]['lon']),0);
	echo '<td align="center" valign="middle">';
	echo '<div class="divTable myTable2">';
    	echo '<div class="divTableBody">';
    	echo '<div class="divTableRow">';
    
	echo '<div class="divTableCell" style="width: 33%;">';
	echo $result[$i]['direction'];
	echo '</div>';

	echo '<div class="divTableCell" style="width: 33%;">';
	echo $bearing . '&deg;';
	echo '</div>';

	echo '<div id="adir" class="divTableCell" style="width: 33%;">';
	echo '<i class="arrow" style="'. arrowdirection($bearing). '"></i>';
	echo '<div>';
	
	echo '</div></div></div>';
	echo '</td>';

		$_distance = _Rounding($result[$i]['distance'],1);
		$_distance = _getColor($_distance); 
		
	echo '<td>'. $_distance . '</td>';
	echo '</tr>';
}
echo '</tbody></table>';

}

// Alt
function MaxAlt() {
	global $dbt;

	$SQL = "SELECT * FROM sondedata ORDER BY alt DESC LIMIT 1";
	$result = $dbt -> select($SQL);
	$rows = count($result); 
	
	echo '<div class="divTable myTable">';
	echo '<div class="divTableBody">';
	
	for ($i = 0; $i <= ( $rows - 1 ); $i++) {
		$date = MyDateFormat($result[$i]['last_date']);

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell"><span class="bold">Last Seen</span></div>';
		echo '<div class="divTableCell">&nbsp;</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Date:</div>';
 		echo '<div class="divTableCell">'. $date . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Station:</div>';
		echo '<div class="divTableCell">'. _Station($result[$i]['station']) . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
 		echo '<div class="divTableCell">Callsign:</div>';
		echo '<div class="divTableCell">'. $result[$i]['callsign'] . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Altitude (M):</div>';
		echo '<div class="divTableCell"><span class="bold">'. _Rounding($result[$i]['alt'],1) . '</span></div>';
		echo '</div>';
	}
}

function MinAlt() {
	global $dbt;

	$SQL = "SELECT * FROM sondedata ORDER BY alt ASC LIMIT 1";
	$result = $dbt -> select($SQL);
	$rows = count($result); 
	
	echo '<div class="divTable myTable">';
	echo '<div class="divTableBody">';
	
	for ($i = 0; $i <= ( $rows - 1 ); $i++) {
		$date = MyDateFormat($result[$i]['last_date']);

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell"><span class="bold">Last Seen</span></div>';
		echo '<div class="divTableCell">&nbsp;</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Date:</div>';
 		echo '<div class="divTableCell">'. $date . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Station:</div>';
		echo '<div class="divTableCell">'. _Station($result[$i]['station']) . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
 		echo '<div class="divTableCell">Callsign:</div>';
		echo '<div class="divTableCell">'. $result[$i]['callsign'] . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Altitude (M):</div>';
		echo '<div class="divTableCell"><span class="bold">'. _Rounding($result[$i]['alt'],1) . '</span></div>';
		echo '</div>';
	}
	
	}

function FirstMaxAlt() {
	global $dbt;

	$SQL = "SELECT * FROM first_seen ORDER BY alt DESC LIMIT 1";
	$result = $dbt -> select($SQL);
	$rows = count($result); 
	
	for ($i = 0; $i <= ( $rows - 1 ); $i++) {
		$date = MyDateFormat($result[$i]['last_date']);

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell"><span class="bold">First Seen</span></div>';
		echo '<div class="divTableCell">&nbsp;</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Date:</div>';
 		echo '<div class="divTableCell">'. $date . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Station:</div>';
		echo '<div class="divTableCell">'. _Station($result[$i]['station']) . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
 		echo '<div class="divTableCell">Callsign:</div>';
		echo '<div class="divTableCell">'. $result[$i]['callsign'] . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Altitude (M):</div>';
		echo '<div class="divTableCell"><span class="bold">'. _Rounding($result[$i]['alt'],1) . '</span></div>';
		echo '</div>';
	}
	echo '</div></div>';
}

function FirstMinAlt() {
	global $dbt;

	$SQL = "SELECT * FROM first_seen ORDER BY alt ASC LIMIT 1";
	$result = $dbt -> select($SQL);
	$rows = count($result); 
	

	for ($i = 0; $i <= ( $rows - 1 ); $i++) {
		$date = MyDateFormat($result[$i]['last_date']);

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell"><span class="bold">First Seen</span></div>';
		echo '<div class="divTableCell">&nbsp;</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Date:</div>';
 		echo '<div class="divTableCell">'. $date . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Station:</div>';
		echo '<div class="divTableCell">'. _Station($result[$i]['station']) . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
 		echo '<div class="divTableCell">Callsign:</div>';
		echo '<div class="divTableCell">'. $result[$i]['callsign'] . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Altitude (M):</div>';
		echo '<div class="divTableCell"><span class="bold">'. _Rounding($result[$i]['alt'],1) . '</span></div>';
		echo '</div>';
	}
	echo '</div></div>';
}

// Distance
function MaxDistance() {
	global $dbt;

	$SQL = "SELECT * FROM sondedata ORDER BY distance DESC LIMIT 1";
	$result = $dbt -> select($SQL);
	$rows = count($result); 
	
	echo '<div class="divTable myTable">';
	echo '<div class="divTableBody">';
	
	for ($i = 0; $i <= ( $rows - 1 ); $i++) {
		$date = MyDateFormat($result[$i]['last_date']);

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell"><span class="bold">Last Seen</span></div>';
		echo '<div class="divTableCell">&nbsp;</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Date:</div>';
 		echo '<div class="divTableCell">'. $date . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Station:</div>';
		echo '<div class="divTableCell">'. _Station($result[$i]['station']) . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
 		echo '<div class="divTableCell">Callsign:</div>';
		echo '<div class="divTableCell">'. $result[$i]['callsign'] . '</div>';
		echo '</div>';
		
		$_distance = _Rounding($result[$i]['distance'],1);
		$_distance = _getColor($_distance); 
		
		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Distance (KM):</div>';
		echo '<div class="divTableCell"><span class="bold">'. $_distance . '</span></div>';
		echo '</div>';
	}
}

function MinDistance() {
	global $dbt,$_nearbysonde;

	$SQL = "SELECT * FROM sondedata ORDER BY distance ASC LIMIT 1";
	$result = $dbt -> select($SQL);
	$rows = count($result); 

	echo '<div class="divTable myTable">';
	echo '<div class="divTableBody">';
	
	for ($i = 0; $i <= ( $rows - 1 ); $i++) {
		$date = MyDateFormat($result[$i]['last_date']);

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell"><span class="bold">Last Seen</span></div>';
		echo '<div class="divTableCell">&nbsp;</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Date:</div>';
 		echo '<div class="divTableCell">'. $date . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Station:</div>';
		echo '<div class="divTableCell">'. _Station($result[$i]['station']) . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
 		echo '<div class="divTableCell">Callsign:</div>';
		echo '<div class="divTableCell">'. $result[$i]['callsign'] . '</div>';
		echo '</div>';

		$_distance = _Rounding($result[$i]['distance'],1);
		$_distance = _getColor($_distance); 

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Distance (KM):</div>';
		echo '<div class="divTableCell"><span class="bold">'. $_distance. '</span></div>';
		echo '</div>';
	}
}

function FirstMaxDistance() {
	global $dbt;

	$SQL = "SELECT * FROM first_seen ORDER BY distance DESC LIMIT 1";
	$result = $dbt -> select($SQL);
	$rows = count($result); 
	
	for ($i = 0; $i <= ( $rows - 1 ); $i++) {
		$date = MyDateFormat($result[$i]['last_date']);

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell"><span class="bold">First Seen</span></div>';
		echo '<div class="divTableCell">&nbsp;</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Date:</div>';
 		echo '<div class="divTableCell">'. $date . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Station:</div>';
		echo '<div class="divTableCell">'. _Station($result[$i]['station']) . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
 		echo '<div class="divTableCell">Callsign:</div>';
		echo '<div class="divTableCell">'. $result[$i]['callsign'] . '</div>';
		echo '</div>';
		
		$_distance = _Rounding($result[$i]['distance'],1);
		$_distance = _getColor($_distance); 
		
		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Distance (KM):</div>';
		echo '<div class="divTableCell"><span class="bold">'. $_distance . '</span></div>';
		echo '</div>';
	}
	echo '</div></div>';
}

function FirstMinDistance() {
	global $dbt,$_nearbysonde;

	$SQL = "SELECT * FROM first_seen ORDER BY distance ASC LIMIT 1";
	$result = $dbt -> select($SQL);
	$rows = count($result); 
	
	for ($i = 0; $i <= ( $rows - 1 ); $i++) {
		$date = MyDateFormat($result[$i]['last_date']);

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell"><span class="bold">First Seen</span></div>';
		echo '<div class="divTableCell">&nbsp;</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Date:</div>';
 		echo '<div class="divTableCell">'. $date . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Station:</div>';
		echo '<div class="divTableCell">'. _Station($result[$i]['station']) . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
 		echo '<div class="divTableCell">Callsign:</div>';
		echo '<div class="divTableCell">'. $result[$i]['callsign'] . '</div>';
		echo '</div>';

		$_distance = _Rounding($result[$i]['distance'],1);
		$_distance = _getColor($_distance); 

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Distance (KM):</div>';
		echo '<div class="divTableCell"><span class="bold">'. $_distance. '</span></div>';
		echo '</div>';
	}
	echo '</div></div>';
}

function Freq() {
	global $dbt;

	$SQL = 'SELECT freq,COUNT(*) AS cnt FROM sondedata GROUP BY freq ORDER BY freq ASC, cnt DESC;';
	$result = $dbt -> select($SQL);
	$rows = count($result); 
	
	echo '<div class="divTable myTable">';
	echo '<div class="divTableBody">';

	for ($i = 0; $i <= ( $rows - 1 ); $i++) {
		echo '<div class="divTableRow">';
 		echo '<div class="divTableCell half al">'. $result[$i]['freq'] . '</div>';
		echo '<div class="divTableCell half al">'. $result[$i]['cnt'] . 'x</div>';
		echo '</div>';
	}
	echo '</div></div>';
}

function Model() {
	global $dbt;

	$SQL = 'SELECT station, model,COUNT(*) AS cnt FROM sondedata GROUP BY model, station ORDER BY model ASC, station ASC';
	$result = $dbt -> select($SQL);
	$rows = count($result); 
	
	echo '<div class="divTable myTable">';
	echo '<div class="divTableBody">';

	for ($i = 0; $i <= ( $rows - 1 ); $i++) {
		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">'. $result[$i]['model'] . '</div>';
 		echo '<div class="divTableCell">'. $result[$i]['cnt'] . 'x</div>';
		echo '<div class="divTableCell">by '. _Station($result[$i]['station']) .'</div>';
		echo '</div>';
	}
	echo '</div></div>';
}

function SeenSondes() {
	global $dbt;

	$SQL = 'SELECT callsign,COUNT(*) AS cnt FROM sondedata GROUP BY callsign ORDER BY callsign ASC, cnt DESC';
	$result = $dbt -> select($SQL);
	$totalsondes = count($result); 

	$SQL = 'SELECT callsign,COUNT(*) AS cnt FROM sondedata WHERE DATE(last_date) = CURDATE() GROUP BY callsign ORDER BY callsign ASC, cnt DESC';
	$result = $dbt -> select($SQL);
	$today = count($result); 

	$SQL = 'SELECT callsign,COUNT(*) AS cnt FROM sondedata WHERE DATE(last_date) = CURDATE() - INTERVAL 1 DAY GROUP BY callsign ORDER BY callsign ASC, cnt DESC';
	$result = $dbt -> select($SQL);
	$yesterday = count($result); 

	$SQL = 'SELECT callsign,COUNT(*) AS cnt FROM sondedata WHERE YEARWEEK(DATE(last_date)) = YEARWEEK( CURDATE() - INTERVAL 1 WEEK) GROUP BY callsign ORDER BY callsign ASC, cnt DESC';
	$result = $dbt -> select($SQL);
	$lastweek = count($result); 

	$SQL = 'SELECT callsign,COUNT(*) AS cnt FROM sondedata WHERE YEARWEEK(DATE(last_date)) = YEARWEEK(CURDATE()) GROUP BY callsign ORDER BY callsign ASC, cnt DESC';
	$result = $dbt -> select($SQL);
	$currentweek = count($result); 

	$SQL = 'SELECT DAYOFWEEK(DATE(last_date)), COUNT(*) AS cnt FROM sondedata GROUP BY DAYOFWEEK(DATE(last_date))';
	$resultdays = $dbt -> select($SQL);
	$dayrows = count($resultdays); 
	
	echo '<div class="divTable myTable">';
	echo '<div class="divTableBody">';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Total:</div>';
 	echo '<div class="divTableCell">'. $totalsondes . 'x</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Today:</div>';
 	echo '<div class="divTableCell">'. $today . 'x</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Yesterday:</div>';
 	echo '<div class="divTableCell">'. $yesterday . 'x</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Current week:</div>';
 	echo '<div class="divTableCell">'. $currentweek . 'x</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Last week:</div>';
 	echo '<div class="divTableCell">'. $lastweek . 'x</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell"><span class="bold">Daily break down</span></div>';
	echo '<div class="divTableCell"><span class="bold">(all stations)</span></div>';
	echo '</div>';


    $dayMap = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
	for ($i = 0; $i <= ( $dayrows - 1 ); $i++) {
	$day_text = $dayMap[$i];
	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">At ' . $day_text .'\'s</div>';
 	echo '<div class="divTableCell">' . $resultdays[$i]['cnt'] . 'x</div>';
	echo '</div>';
	}
	
	echo '</div></div>';
}

function MonthlyAllRX() {
	global $dbt;
	
	$SQL = "SELECT COUNT(*) as COUNT, MONTH(last_date) AS MONTH, YEAR(last_date) as YEAR FROM sondedata GROUP BY MONTH, YEAR ORDER BY YEAR DESC, MONTH DESC";
	$result = $dbt -> select($SQL);
	$rows = count($result); 

	echo '<div class="divTable myTable">';
	echo '<div class="divTableBody">';


	for ($i = 0; $i <= ( $rows - 1 ); $i++) {
		$m = date('M', mktime(0, 0, 0, $result[$i]['MONTH'], 10));
		$y = $result[$i]['YEAR'];
		$x = $result[$i]['COUNT'];
		
		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">'. $m . ' ' . $y . '</div>';
		echo '<div class="divTableCell">' . $x . 'x</div>';
		echo '</div>';
	}
echo '</div></div>';

}

function NearestTable() {
	global $dbt,$_nearbysonde;

	$SQL = "SELECT alt,callsign,last_date,MIN(distance) distance FROM sondedata GROUP BY distance,alt,callsign,last_date ORDER BY distance ASC LIMIT 10";
	$result = $dbt -> select($SQL);
	$rows = count($result); 

	echo '<div class="divTable myTable">';
	echo '<div class="divTableBody">';



	for ($i = 0; $i <= ( $rows - 1 ); $i++) {
		$d = new DateTime( $result[$i]['last_date'] );
		$date = $d->format( 'd M Y' );
		$callsign = $result[$i]['callsign'];
		$alt = _Rounding($result[$i]['alt'],1);
		$_distance = _Rounding($result[$i]['distance'],1);
		$_distance = _getColor($_distance); 	
		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">' . $date . '</div>';
		echo '<div class="divTableCell">' . $callsign . '</div>';
		echo '<div class="divTableCell">' . $_distance . ' KM</div>';
		echo '<div class="divTableCell">Alt: ' . $alt . ' M</div>';
		echo '</div>';
	}
	echo '</div></div>';
}

function AVGDistanceTable() {
	global $dbt,$_nearbysonde;

	$SQL = "SELECT AVG(distance) distance FROM sondedata";
	$result = $dbt -> select($SQL);
	$rows = count($result); 

	echo '<div class="divTable myTable">';
	echo '<div class="divTableBody">';



	for ($i = 0; $i <= ( $rows - 1 ); $i++) {
		$_distance = _Rounding($result[$i]['distance'],1);
		$_distance = _getColor($_distance); 
		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">' . $_distance . ' KM</div>';
		echo '</div>';
	}
	echo '</div></div>';
}

function AVGAltTable() {
	global $dbt,$_nearbysonde;

	$SQL = "SELECT AVG(alt) alt FROM sondedata";
	$result = $dbt -> select($SQL);
	$rows = count($result); 

	echo '<div class="divTable myTable">';
	echo '<div class="divTableBody">';



	for ($i = 0; $i <= ( $rows - 1 ); $i++) {
		$_alt = _Rounding($result[$i]['alt'],1);
		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">' . $_alt . ' M</div>';
		echo '</div>';
	}
	echo '</div></div>';
}


/*
 *  Round numbers after decimal`
 *	Defaults to 1 decimal
 */
function _Rounding($value, $place = 1){ 
	$value = $value * pow(10, $place + 1); 
	$value = floor($value); 
	$value = (float) $value/10; 
	(float) $modSquad = ($value - floor($value)); 
	$value = floor($value); 
		if ($modSquad > .5){ 
			$value++; 
		}
	return $value / (pow(10, $place)); 
}

// Remove the standard suffix from station name
function _Station($station) {
	return str_replace("_AUTO_RX","",$station);	
}

// Distance coloring
function _getColor($n){
    // between 0 and 6
    if($n>=0 && $n<6) return '<span class="sondeisclose1">' . $n . '</span>';
    // between 6 and 20
    if($n>=6 && $n<20) return '<span class="sondeisclose2">' . $n . '</span>';
    // between 20 and 35
    if($n>=20 && $n<35) return '<span class="sondeisclose3">' . $n . '</span>';
    // Above for 35+
    return '<span class="sondeisfar">' . $n . '</span>';;
}

// Format the date and time
function MyDateFormat($last_date) {
	global $_myformat;
	$d = new DateTime($last_date);
	$date = $d->format($_myformat);
	return $date;
}
