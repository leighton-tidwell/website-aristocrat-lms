<?php
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";
$user = new user();

// First off, lets make sure youre a teacher.
if(!$user->isTeacher()){
	header("Location: 404.html");
	exit;
}

// Define Post Variables

$aid = mysql_real_escape_string($_GET['aid']);
$uid = mysql_real_escape_string($_GET['uid']);
$cid = mysql_real_escape_string($_GET['cid']);
$attempt = mysql_real_escape_string($_GET['attempt']);
$q = mysql_real_escape_string($_GET['q']);
$bool = $_GET['bool'];

// check to make sure variables exist
if($aid == "" || $uid == "" || $cid == "" || $attempt = "" || $q = "" || $bool = ""){
		exit;
}

// make sure the teacher is the owner of the class, or (in the class)
$user->inClass($cid);

//make sure the student is in the class
$classes = $user->fetchProfileInfo($uid,"classes");
$classes = explode(":",$classes);

$attempt = $_GET['attempt'];

$select = mysql_query("SELECT * FROM `testinfo` WHERE `uid`='" . $uid . "' AND `aid` ='" . $aid . "' AND `attempt`='" . mysql_real_escape_string($attempt) . "' AND `cid`='" . $cid . "'");
$fetch = mysql_fetch_array($select);


if($fetch['overWrite'] == ""){
	$bool = $_GET['bool'];
	$q = $_GET['q'];
	$attempt = $_GET['attempt'];
	$overwrite = array();
	if($bool == "a"){
		$overwrite[$q] = 1;
		$de = "right";
	}else{
		$overwrite[$q] = 0;
		$de = "wrong";
	}
	$overwritess = json_encode($overwrite, JSON_FORCE_OBJECT);
	mysql_query("UPDATE `testinfo` SET `overWrite`='" . mysql_real_escape_string($overwritess) . "' WHERE `uid`='" . $uid . "' AND `aid`='" . $aid . "' AND `attempt`='" . mysql_real_escape_string($attempt) . "' AND `cid`='" . $cid . "'");
	echo("Question marked " . $de . ".");
}
else{
	$bool = $_GET['bool'];
	$q = $_GET['q'];
	$attempt = $_GET['attempt'];
	$overwrites = json_decode($fetch['overWrite'],true);
	if($bool == "a"){
		$overwrites[$q] = 1;
		$de = "right";
	}else{
		$overwrites[$q] = 0;
		$de = "wrong";
	}
	$nO = json_encode($overwrites,JSON_FORCE_OBJECT);

	mysql_query("UPDATE `testinfo` SET `overWrite`='" . mysql_real_escape_string($nO) . "' WHERE `uid`='" . $uid . "' AND `aid` ='" . $aid . "' AND `attempt`='" . $attempt . "' AND `cid`='" . $cid . "'");
	echo("Question marked " . $de . ".");
}
?>