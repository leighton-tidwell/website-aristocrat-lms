<?php
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";
include "core/passHash.php";
$user = new user();

$email = $_POST['email'];
$currPass = $_POST['currPass'];
$newPass = $_POST['newPass'];
$conPass = $_POST['conPass'];
if($email){
	$query = mysql_query("SELECT * FROM `users` WHERE `id`='".mysql_real_escape_string($user->getUserInfo("id"))."'");
	if($email != ""){
		mysql_query("UPDATE `users` SET `email`='".mysql_real_escape_string($email)."' WHERE `id`='".mysql_real_escape_string($user->getUserInfo("id"))."'");
		header("Location: settings/email=success");
		exit;
	}
}
if($currPass && $newPass && $conPass){
	$query = mysql_query("SELECT * FROM `users` WHERE `id`='".mysql_real_escape_string($user->getUserInfo("id"))."'");
	$fetch = mysql_fetch_array($query);
	
	if(!password_verify($currPass,$fetch['password'])){
		header("Location: settings/password=nomatch");
		exit;
	}
	if($newPass != $conPass){
		header("Location: settings/password=noconfirm");
		exit;
	}
	
	mysql_query("UPDATE `users` SET `password`='".password_hash($newPass,PASSWORD_BCRYPT)."' WHERE `id`='".mysql_real_escape_string($user->getUserInfo("id"))."'");
	header("Location: settings/password=success");
	exit;
}
else{
	header("Location: settings/password=blank");
	exit;
}

?>