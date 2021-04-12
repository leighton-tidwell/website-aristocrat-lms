<?php
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";
$user = new user();

$id = $_GET['id'];

//check for from id
if($id == ""){
	exit;
}
if($_POST['message'] == ""){
	exit;
}

$message = $_POST['message'];

mysql_query("INSERT INTO `messages` (`to`,`from`,`message`,`timestamp`) VALUES('" . mysql_real_escape_string($id) . "','" . mysql_real_escape_string($user->getUserInfo("id")) . "','" . mysql_real_escape_string($message) . "','" .  time() . "')");


?>