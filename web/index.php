<?php
//
// ######################## TURN OFF ALL ERRORS #############################
//
//
//
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
include("class-database.php");
include("db_data.php");

?>
<!DOCTYPE html>
<html>
<head>
<title>SondeData</title>
<meta charset="utf-8" />
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!-- Post Script-->
    <script>
 		$(document).ready(function() {
 			$("#Data").load("sondedata.php");
   			var refreshId = setInterval(function() { $("#Data").load('sondedata.php'); }, 15000);
   			$.ajaxSetup({ cache: false });
		});
	</script>
</head>
<body>
<!-- Wrapper -->
<div id="container"> 
  <!-- Header -->
  <div id="header"><?php include("header.php"); ?></div>
  <!-- Main -->
  <div id="body"> 
  <!-- Data that automaticly refreshes--> 
  <div id="Data"></div>
  </div>
  <!-- Footer -->
  <div id="footer"><?php include("footer.php"); ?></div>
</div>
</body>
</html>
