<?php
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
$user = new user();

if($_POST['code'] != ""){
	$code = $_POST['code'];
	$findCourse = mysql_query("SELECT * FROM `classes` WHERE `code`='" . mysql_real_escape_string($code) . "'");
	$fetchInfo = mysql_fetch_array($findCourse);

	if(mysql_num_rows($findCourse) == 0){
		// no course
	}else{
		if($fetchInfo['school'] != $user->getUserInfo("school")){
			// wrong school
		}
		else{
			$classes = json_decode($user->getUserInfo("classes"),true);
			$classes[$fetchInfo['semester']][$fetchInfo['subsemesters']][$fetchInfo['period']] = $fetchInfo['id'];
			$newClasses = json_encode($classes,JSON_FORCE_OBJECT);
			echo($newClasses);
			mysql_query("UPDATE `users` SET `classes`='" . mysql_real_escape_string($newClasses) . "' WHERE `id`='" . mysql_real_escape_string($user->getUserInfo("id")) . "'");
			
		}
	}
}
else{
	exit;
}
?>