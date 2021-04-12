<?php
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";
$user = new user();

if($_GET['notifications'] == "true"){
	mysql_query("UPDATE `notifications` SET `read`='1' WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' and `read`='0'") or die(mysql_error());

}elseif($_GET['messages'] == "true"){
	mysql_query("UPDATE `messages` SET `read`='1' WHERE `to`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' and `read`='0'") or die(mysql_error());
}
?>