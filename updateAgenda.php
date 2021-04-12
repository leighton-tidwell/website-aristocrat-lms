<?php
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";

// assign variables
$day = $_GET['day'];
$year = $_GET['year'];
$month = $_GET['month'];
$class = $_GET['class'];
$agenda = $_POST['agenda'];


if(!$day || !$year || !$month || !$class){
	exit;
}

$query = mysql_query("SELECT * FROM `agendas` WHERE `day`='".mysql_real_escape_string($day)."' AND `month`='".mysql_real_escape_string($month)."' AND `year`='".mysql_real_escape_string($year)."' AND `class`='".mysql_real_escape_string($class)."'");



if(mysql_num_rows($query) == 0){

	mysql_query("INSERT INTO `agendas` (text,class,day,month,year) VALUES ('".mysql_real_escape_string($agenda)."','".mysql_real_escape_string($class)."','".mysql_real_escape_string($day)."','".mysql_real_escape_string($month)."','".mysql_real_escape_string($year)."')");
	header("location: agenda/".$year."-".$month."-".$day."/".$class."");
	exit;
}
else{
	mysql_query("UPDATE `agendas` SET `text`='".mysql_real_escape_string($agenda)."' WHERE `day`='".mysql_real_escape_string($day)."' AND `month`='".mysql_real_escape_string($month)."' AND `year`='".mysql_real_escape_string($year)."' AND `class`='".mysql_real_escape_string($class)."'") or die(mysql_error());
	header("location: agenda/".$year."-".$month."-".$day."/".$class."");
	exit;
}
?>