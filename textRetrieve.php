<?php
session_start();
if(!$_GET['id']){
	header("location: 404.html");
	exit;
}

include "core/sqlconnect.php";
include "core/class.user.php";
$user = new user();
$findCreation = mysql_query("SELECT * FROM `turnin` WHERE `id`='" . mysql_real_escape_string($_GET['id']) . "'");
$go = mysql_fetch_array($findCreation);

echo($go['href']);

?>