<?php
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
$user = new user();

$id = $_GET['id'];

//check for from id
if($id == ""){
	exit;
}

// find messages from user to me
$messages = mysql_query("SELECT * FROM `messages` WHERE `to`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `from`='" . mysql_real_escape_string($id) . "' ORDER BY `timestamp` DESC"); 

// find messages from me to user
$to = mysql_query("SELECT * FROM `messages` WHERE `to`='" . mysql_real_escape_string($id) . "' AND `from`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' ORDER BY `timestamp` DESC");

$convoIDs = array();
while($fMessages = mysql_fetch_array($messages)){
	$convoIDs[$fMessages['id']] = $fMessages['timestamp'];
}


while($two = mysql_fetch_array($to)){
	$convoIDs[$two['id']] = $two['timestamp'];
}

asort($convoIDs, SORT_NUMERIC);

$query = mysql_query("SELECT * FROM `messages` WHERE `from`='" . mysql_real_escape_string($id) . "'");
$fetch = mysql_fetch_array($query);

if(count($convoIDs) != 0){
	echo("<div>");
	echo("<h3>Conversation with " . $user->fetchProfileInfo($id,"firstName") . " " . $user->fetchProfileInfo($id,"lastName") . "</h3>");
	echo("<hr>");
	echo("</div>");

	foreach(array_keys($convoIDs) as $cid){
		$query2 = mysql_query("SELECT * FROM `messages` WHERE `id`='" . mysql_real_escape_string($cid) . "'") or die(mysql_error());
		$fetch2 = mysql_fetch_array($query2);
		
		if($fetch2['read'] != "1" || $fetch2['to'] == $user->getUserInfo("id")){
			mysql_query("UPDATE `messages` SET `read`='1' WHERE `id`='" . mysql_real_escape_string($cid) . "' and `to`='" . mysql_real_escape_string($user->getUserInfo("id")) . "'");
		}
		
		if($user->getUserInfo("id") == $fetch2['from']){
			echo("<div class=\"row\">");
				echo("<div class=\"col-md-9\">");
					echo("<div class=\"well\">");
						echo($fetch2['message']);
					echo("</div>");
					echo("<span class=\"pull-left\"><small>Sent at " . date("F j Y, g:i a",$fetch2['timestamp']) . ".</small></span>");
				echo("</div>");
				echo("<div class=\"col-md-3\">");
					echo("<img class=\"media-object\" width=\"75px\" src=\"" . $user->fetchProfileInfo($fetch2['from'], "profilePicture") . "\">");
				echo("</div>");
			echo("</div>");
		}
		else{
			echo("<div class=\"row\">");
				echo("<div class=\"col-md-3\">");
					echo("<img class=\"media-object\" width=\"75px\" src=\"" . $user->fetchProfileInfo($fetch2['from'], "profilePicture") . "\">");
				echo("</div>");
				echo("<div class=\"col-md-9\">");
					echo("<div class=\"well\">");
						echo($fetch2['message']);
					echo("</div>");
					echo("<span class=\"pull-right\"><small>Sent at " . date("F j Y, g:i a",$fetch2['timestamp']) . ".</small></span>");
				echo("</div>");
			echo("</div>");
		}
		echo("<hr>");
	}
	echo("<div class=\"row\">");
		echo("<div class=\"col-md-9\">");
			echo("<br />");
			echo("<textarea class=\"form-control\" id=\"messageBox-" . $id . "\"></textarea>");
		echo("</div>");
		echo("<div class=\"col-md-3\">");
		echo("</div>");
	echo("</div>");
	echo("<div class=\"row\">");
		echo("<div class=\"col-md-9\">");
			echo("<br />");
			echo("<button type=\"button\" class=\"btn btn-success\" onclick=\"sendMessage(" . $id . ");\">Send Message</button>");
		echo("</div>");
		echo("<div class=\"col-md-3\">");
		echo("</div>");
	echo("</div>");
}
else{
	exit;
}

?>