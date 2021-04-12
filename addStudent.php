<?php
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
$user = new user();


$cid = $_POST['cid'];
$sid = $_POST['sid'];
$ssid = $_POST['ssid'];
$pid = $_POST['pid'];
$uid = $_POST['enroll'][0];


if($cid != "" && $sid != "" && $uid != ""){
	$classes = json_decode($user->fetchProfileInfo($uid,"classes"),true);
	$classes[$sid][$ssid][$pid] = $cid;
	$newClasses = json_encode($classes,JSON_FORCE_OBJECT);
	mysql_query("UPDATE `users` SET `classes`='" . mysql_real_escape_string($newClasses) . "' WHERE `id`='" . mysql_real_escape_string($uid). "'");
	echo("<div class=\"alert alert-success\">Student added.. Results will update when page refreshes!</div>");
}
else{
	echo("<div class=\"alert alert-danger\">Invalid data.</div>");
	exit;
}

