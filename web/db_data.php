<?php
$version = "SondeData 1.0";

$dbt = new MyDb();
$_nearbysonde = 10; // range within 10km
//

function MyDateFormat($last_date) {
	$d = new DateTime($last_date);
	$date = $d->format('d-M-Y H:i:s');
	return $date;
}

function TableCurrentSonde() {
global $dbt,$_nearbysonde;

$SQL = "SELECT * FROM `sondedata` WHERE `last_date` >= NOW() - INTERVAL 10 MINUTE ORDER BY `last_date` DESC";
		
$result = $dbt -> select($SQL);
$sondes = count($result); 

echo '<table class="blueTable">';
echo '<thead><tr>';
echo '<th>Last Date</th>';
echo '<th>Station</th>';
echo '<th>Callsign</th>';
echo '<th>Frequency</th>';
echo '<th>Altitude (M)</th>';
echo '<th>Direction</th>';
echo '<th>Distance (KM)</th>';
echo '</tr></thead><tbody>';

for ($i = 0; $i <= ( $sondes - 1 ); $i++) {
	$date = MyDateFormat($result[$i]['last_date']);
	echo '<tr>';
 	echo '<td>'. $date . '</td>';
	echo '<td>'. $result[$i]['station'] . '</td>';
	echo '<td>'. $result[$i]['callsign'] . '</td>';
	echo '<td>'. $result[$i]['freq'] . '</td>';
	echo '<td>'. _Rounding($result[$i]['alt'],1) . '</td>';
	echo '<td>'. $result[$i]['direction'] . '</td>';
	$_distance = _Rounding($result[$i]['distance'],1);
	// Closer then 10 KM? give it a color
	if ( $_distance < $_nearbysonde ) {
	$_distance = '<span class="sondeisclose">' . $_distance . '</span>'; 	
	}
	echo '<td>'. $_distance . '</td>';
	echo '</tr>';
}
echo '</tbody></table>';
}

function TableLatestSondes($si) {
global $dbt,$_nearbysonde;

$result = $dbt -> getlatestSondes($si);
$sondes = count($result); 

echo '<table class="blueTable">';
echo '<thead><tr>';
echo '<th>Last Date</th>';
echo '<th>Station</th>';
echo '<th>Callsign</th>';
echo '<th>Frequency</th>';
echo '<th>Altitude (M)</th>';
echo '<th>Direction</th>';
echo '<th>Distance (KM)</th>';
echo '</tr></thead><tbody>';

for ($i = 0; $i <= ( $sondes - 1 ); $i++) {
	$date = MyDateFormat($result[$i]['last_date']);
	echo '<tr>';
 	echo '<td>'. $date . '</td>';
	echo '<td>'. $result[$i]['station'] . '</td>';
	$sondenumber = $result[$i]['callsign'];
	// Seems radiosondy and auto_rx uses different serial numbers?
	// ToDo: DFM06 and IMET? is it always DFM09-18 to replace?
	if ($result[$i]['model'] == "DFM") {
		$sondenumber = str_replace("DFM09-18","DF9",$sondenumber);
	}
	echo '<td><a href="https://radiosondy.info/sonde.php?sondenumber='. $sondenumber . '" target="new">' . $result[$i]['callsign'] . '</a></td>';
	echo '<td>'. $result[$i]['freq'] . '</td>';
	echo '<td>'. _Rounding($result[$i]['alt'],1) . '</td>';
	echo '<td>'. $result[$i]['direction'] . '</td>';
	$_distance = _Rounding($result[$i]['distance'],1);
	// Closer then 10 KM from your QTH? give it a color
	if ( $_distance < $_nearbysonde ) {
	$_distance = '<span class="sondeisclose">' . $_distance . '</span>'; 	
	}
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
		echo '<div class="divTableCell"></div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Date:</div>';
 		echo '<div class="divTableCell">'. $date . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Station:</div>';
		echo '<div class="divTableCell">'. $result[$i]['station'] . '</div>';
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
		echo '<div class="divTableCell"></div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Date:</div>';
 		echo '<div class="divTableCell">'. $date . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Station:</div>';
		echo '<div class="divTableCell">'. $result[$i]['station'] . '</div>';
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
	echo '</div></div>';}

function FirstMaxAlt() {
	global $dbt;

	$SQL = "SELECT * FROM first_seen ORDER BY alt DESC LIMIT 1";
	$result = $dbt -> select($SQL);
	$rows = count($result); 
	
	echo '<div class="divTable myTable">';
	echo '<div class="divTableBody">';
	
	for ($i = 0; $i <= ( $rows - 1 ); $i++) {
		$date = MyDateFormat($result[$i]['last_date']);

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell"><span class="bold">First Seen</span></div>';
		echo '<div class="divTableCell"></div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Date:</div>';
 		echo '<div class="divTableCell">'. $date . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Station:</div>';
		echo '<div class="divTableCell">'. $result[$i]['station'] . '</div>';
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
	
	echo '<div class="divTable myTable">';
	echo '<div class="divTableBody">';
	
	for ($i = 0; $i <= ( $rows - 1 ); $i++) {
		$date = MyDateFormat($result[$i]['last_date']);

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell"><span class="bold">First Seen</span></div>';
		echo '<div class="divTableCell"></div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Date:</div>';
 		echo '<div class="divTableCell">'. $date . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Station:</div>';
		echo '<div class="divTableCell">'. $result[$i]['station'] . '</div>';
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
		echo '<div class="divTableCell"></div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Date:</div>';
 		echo '<div class="divTableCell">'. $date . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Station:</div>';
		echo '<div class="divTableCell">'. $result[$i]['station'] . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
 		echo '<div class="divTableCell">Callsign:</div>';
		echo '<div class="divTableCell">'. $result[$i]['callsign'] . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Distance (KM):</div>';
		echo '<div class="divTableCell"><span class="bold">'. _Rounding($result[$i]['distance'],1) . '</span></div>';
		echo '</div>';
	}
	echo '</div></div>';
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
		echo '<div class="divTableCell"></div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Date:</div>';
 		echo '<div class="divTableCell">'. $date . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Station:</div>';
		echo '<div class="divTableCell">'. $result[$i]['station'] . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
 		echo '<div class="divTableCell">Callsign:</div>';
		echo '<div class="divTableCell">'. $result[$i]['callsign'] . '</div>';
		echo '</div>';

		$_distance = _Rounding($result[$i]['distance'],1);
		if ( $_distance < $_nearbysonde ) {
			$_distance = '<span class="sondeisclose">' . $_distance . '</span>'; 	
		}

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Distance (KM):</div>';
		echo '<div class="divTableCell"><span class="bold">'. $_distance. '</span></div>';
		echo '</div>';
	}
	echo '</div></div>';
}

function FirstMaxDistance() {
	global $dbt;

	$SQL = "SELECT * FROM first_seen ORDER BY distance DESC LIMIT 1";
	$result = $dbt -> select($SQL);
	$rows = count($result); 
	
	echo '<div class="divTable myTable">';
	echo '<div class="divTableBody">';
	
	for ($i = 0; $i <= ( $rows - 1 ); $i++) {
		$date = MyDateFormat($result[$i]['last_date']);

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell"><span class="bold">First Seen</span></div>';
		echo '<div class="divTableCell"></div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Date:</div>';
 		echo '<div class="divTableCell">'. $date . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Station:</div>';
		echo '<div class="divTableCell">'. $result[$i]['station'] . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
 		echo '<div class="divTableCell">Callsign:</div>';
		echo '<div class="divTableCell">'. $result[$i]['callsign'] . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Distance (KM):</div>';
		echo '<div class="divTableCell"><span class="bold">'. _Rounding($result[$i]['distance'],1) . '</span></div>';
		echo '</div>';
	}
	echo '</div></div>';
}

function FirstMinDistance() {
	global $dbt,$_nearbysonde;

	$SQL = "SELECT * FROM first_seen ORDER BY distance ASC LIMIT 1";
	$result = $dbt -> select($SQL);
	$rows = count($result); 
	
	echo '<div class="divTable myTable">';
	echo '<div class="divTableBody">';
	
	for ($i = 0; $i <= ( $rows - 1 ); $i++) {
		$date = MyDateFormat($result[$i]['last_date']);

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell"><span class="bold">First Seen</span></div>';
		echo '<div class="divTableCell"></div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Date:</div>';
 		echo '<div class="divTableCell">'. $date . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
		echo '<div class="divTableCell">Station:</div>';
		echo '<div class="divTableCell">'. $result[$i]['station'] . '</div>';
		echo '</div>';

		echo '<div class="divTableRow">';
 		echo '<div class="divTableCell">Callsign:</div>';
		echo '<div class="divTableCell">'. $result[$i]['callsign'] . '</div>';
		echo '</div>';

		$_distance = _Rounding($result[$i]['distance'],1);
		if ( $_distance < $_nearbysonde ) {
			$_distance = '<span class="sondeisclose">' . $_distance . '</span>'; 	
		}

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
		echo '<div class="divTableCell">by '. $result[$i]['station'] .'</div>';
		echo '</div>';
	}
	echo '</div></div>';
}

function SeenSondes() {
	global $dbt;

	$SQL = 'SELECT callsign,COUNT(*) AS cnt FROM sondedata GROUP BY callsign ORDER BY callsign ASC, cnt DESC';
	$result = $dbt -> select($SQL);
	$totalsondes = count($result); 

	$SQL = 'SELECT callsign,COUNT(*) AS cnt FROM sondedata WHERE date(last_date) = CURDATE() GROUP BY callsign ORDER BY callsign ASC, cnt DESC';
	$result = $dbt -> select($SQL);
	$today = count($result); 

	$SQL = 'SELECT callsign,COUNT(*) AS cnt FROM sondedata WHERE date(last_date) = CURDATE() - INTERVAL 1 DAY GROUP BY callsign ORDER BY callsign ASC, cnt DESC';
	$result = $dbt -> select($SQL);
	$yesterday = count($result); 

	echo '<div class="divTable myTable">';
	echo '<div class="divTableBody">';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Total:</div>';
 	echo '<div class="divTableCell">'. $totalsondes . '</div>';
	echo '<div class="divTableCell">Sondes</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Today:</div>';
 	echo '<div class="divTableCell">'. $today . '</div>';
	echo '<div class="divTableCell">Sondes</div>';
	echo '</div>';

	echo '<div class="divTableRow">';
	echo '<div class="divTableCell">Yesterday:</div>';
 	echo '<div class="divTableCell">'. $yesterday . '</div>';
	echo '<div class="divTableCell">Sondes</div>';
	echo '</div>';
	
	echo '</div></div>';
}

function MonthlyAllRX() {
global $dbt;
	
	$SQL = "SELECT MONTH(last_date) AS MONTH, COUNT(*) as COUNT, YEAR(last_date) as YEAR FROM sondedata GROUP BY MONTH(last_date), YEAR(last_date) ORDER BY station ASC,last_date DESC";
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

	$SQL = "SELECT alt,callsign,MIN(distance) distance,last_date FROM sondedata GROUP BY station,last_date ORDER BY station ASC, distance ASC LIMIT 3";
	$result = $dbt -> select($SQL);
	$rows = count($result); 

	echo '<div class="divTable myTable">';
	echo '<div class="divTableBody">';



	for ($i = 0; $i <= ( $rows - 1 ); $i++) {
		$d = new DateTime( $result[$i]['last_date'] );
		$date = $d->format( 'd-M-Y' );
		$callsign = $result[$i]['callsign'];
		$alt = _Rounding($result[$i]['alt'],1);
		$_distance = _Rounding($result[$i]['distance'],1);
		if ( $_distance < $_nearbysonde ) {
			$_distance = '<span class="sondeisclose">' . $_distance . '</span>'; 	
		}
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
		if ( $_distance < $_nearbysonde ) {
			$_distance = '<span class="sondeisclose">' . $_distance . '</span>'; 	
		}
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
