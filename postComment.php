<?php
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
$comment = $_POST['comment'];
$id = $_POST['id'];
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";
$user = new user();

if($_POST['aid'] == "true"){
	mysql_query("INSERT INTO `comments` (`uid`,`aid`,`timestamp`,`comment`) VALUES('" . mysql_real_escape_string($user->getUserInfo("id")) . "','" . mysql_real_escape_string($id) . "','" . time() . "','" . mysql_real_escape_string($comment) . "')") or die(mysql_error());
}
?>