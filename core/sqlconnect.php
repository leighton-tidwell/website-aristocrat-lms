<?php
error_reporting(0);
$mysql_host = "localhost";
$mysql_database = "";
$mysql_user = "";
$mysql_password = "";

	
//establish Mysql Connection
mysql_connect($mysql_host, $mysql_user, $mysql_password);
mysql_select_db($mysql_database);
?>