<?php
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
$user = new user();

if($_GET['ss'] != ""){
	$ssid = $_GET['ss'];
	$getcSS = mysql_query("SELECT * FROM `subsemester` WHERE `startTime` < '" . time() . "' AND `endTime` > '" . time() . "' AND `semid`='" . mysql_real_escape_string($ssid) . "'");
	$currentSS = mysql_fetch_array($getcSS);
	if(mysql_num_rows($getcSS) != 0){
		echo("<option value=\"" . $currentSS['id'] . "\">" . $currentSS['name'] . "</option>");

		$getSS = mysql_query("SELECT * FROM `subsemester` WHERE `semid`='" . mysql_real_escape_string($ssid) . "'");
		while($fss = mysql_fetch_array($getSS)){
			if($fss['id'] != $currentSS['id']){
				echo("<option value=\"" . $fss['id'] . "\">" . $fss['name'] . "</option>");
			}
		}
	}
	else{
		$getSS = mysql_query("SELECT * FROM `subsemester` WHERE `semid`='" . mysql_real_escape_string($ssid) . "'");
		while($fss = mysql_fetch_array($getSS)){
			if($fss['id'] != $currentSS['id']){
				echo("<option value=\"" . $fss['id'] . "\">" . $fss['name'] . "</option>");
			}
		}
	}
}
else if($_GET['classes'] != ""){
	$ssid = $_GET['classes'];
	// get user classes
	$classes = $user->getUserInfo("classes");

	if($classes == ""){
		$classes = null;
	}
	$classes = json_decode($classes,true);

	// get selected SEMESTER from SS\
	$gSemid = mysql_query("SELECT * FROM `subsemester` WHERE `id`='" . mysql_real_escape_string($ssid) . "'");
	$fSemid = mysql_fetch_array($gSemid);
	$sid = $fSemid['semid'];

	if($classes[$sid][$ssid] != ""){
		//pull class information from user using semid and ssid
		foreach($classes[$sid][$ssid] as $class){
			// get class information
			$cInfo = mysql_query("SELECT * FROM `classes` WHERE `id`='" . mysql_real_escape_string($class) . "'");
			$cFetch = mysql_fetch_array($cInfo);
			echo("<tr onclick=\"loadClass(" . $cFetch['id'] . ")\">");
				echo("<td class=\"col-md-12\">");
					echo("<b>" . $cFetch['name'] . "</b><br />");
					echo("Teacher</b><br />");
					echo("Grade:</b>&nbsp;" . $user->calcGrade($user->getUserInfo("id"),$class). "<br />");
				echo("</td>");
			echo("</tr>");
		}
	}
	else{
		echo("<tr><td>No classes for this period.</td></tr>");
	}
}
else if($_GET['cid'] != ""){
	$cid = $_GET['cid'];
	echo("<table class=\"table table-striped\">");
		echo("<thead>");
			echo("<th>");
				echo("Assignment");
			echo("</th>");
			echo("<th>");
				echo("Grade");
			echo("</th>");
			echo("<th>");
				echo("Comment");
			echo("</th>");
		echo("</thead>");
	// get assignments for that class
	$alis = mysql_query("SELECT * FROM `assignments` WHERE `class`='" . mysql_real_escape_string($cid) . "'");

	if(mysql_num_rows($alis) != 0){
		// got assignments lets step by it 1by1 and look for a grade
		while($flis = mysql_fetch_array($alis)){
			// go by each assignment n look for a grade
			$aid = $flis['id'];

			//search grades for assign by that ID
			$agds = mysql_query("SELECT * FROM `grades` WHERE `id`='" . mysql_real_escape_string($aid) . "' AND `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "'");
			$fgds = mysql_fetch_array($agds);
			echo("<tr>");
				echo("<td>");
					echo($flis['name']);
				echo("</td>");
				echo("<td>");
					echo($fgds['grade']);
				echo("</td>");
				echo("<td>");
					echo($fgds['comment']);
				echo("</td>");
			echo("</tr>");
		}
	}
	else{
		echo("<tr><td>No assignments.</td><td></td><td></td></tr>");
	}
}
else{

}

?>
