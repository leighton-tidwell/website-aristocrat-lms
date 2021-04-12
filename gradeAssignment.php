<?php
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
$user = new user();

// First off, lets make sure youre a teacher.
if(!$user->isTeacher()){
	header("Location: 404.html");
	exit;
}



// Define Post Variables
$aid = mysql_real_escape_string($_POST['aid']);
$uid = mysql_real_escape_string($_POST['uid']);
$cid = mysql_real_escape_string($_POST['cid']);

// check to make sure variables exist
if($aid == "" || $uid == "" || $cid == ""){
		exit;
}

// make sure the teacher is the owner of the class, or (in the class)
$user->inClass($cid);



// Before we continue, let's set some defaults

if($_POST['comment'] == ""){

	$comment = "No comment entered";

}

else{

	$comment = $_POST['comment'];

}



//also let's set a default weight value.

if($_POST['weight'] == ""){

	$weight = "1";

}

else{

	$weight = mysql_real_escape_string($_POST['weight']);

}



// and if no grade, we will assume it's ungraded and set the grade to -1

if($_POST['grade'] == ""){

	$grade = "-1";

}

else{

	$grade = mysql_real_escape_string($_POST['grade']);

}

$geee = true;

if($geee == true){

		

		// Now check to see if a grade exist, if so update.. if not create a new query

		$gradeQuery = mysql_query("SELECT * FROM `grades` WHERE `uid`='" . mysql_real_escape_string($uid)."' AND `cid`='" . mysql_real_escape_string($cid)."' AND `aid`='".mysql_real_escape_string($aid)."'");

		if(mysql_num_rows($gradeQuery) == 0){

			// No existing query, create new entry for grade

			mysql_query("INSERT INTO `grades` (`uid`,`aid`,`cid`,`timestamp`,`grade`,`weight`,`comment`,`semester`) VALUES('" . $uid . "','" . $aid . "','" . $cid . "','" . time() . "','" . $grade . "','" . $weight ."','" . $comment . "','" . mysql_real_escape_string($semester) . "')");

			$user->addNotification($user->getUserInfo("id"),$uid,"" . $user->getUserInfo("firstName") . " " . $user->getUserInfo("lastName") . " has added a new grade for '" . $user->fetchAssinClass($aid,"name") . "'");

			header("Location: fileGrader.php?id=" . $aid . "&uid=" . $uid ."&cid=" . $cid . "&success=true");

		}

		else{

			

			//Grade exist, lets update it

			mysql_query("UPDATE `grades` SET `timestamp`='" . time() . "', `grade`='" . $grade . "', `weight`='" . $weight . "', `comment`='" . $comment . "' WHERE `uid`='"  . $uid . "' AND `aid`='" . $aid ."' AND `cid`='" . $cid . "'") or die(mysql_error());

			$user->addNotification($user->getUserInfo("id"),$uid,"" . $user->getUserInfo("firstName") . " " . $user->getUserInfo("lastName") . " has updated the grade for '" . $user->fetchAssinClass($aid,"name") . "'");

			header("Location: fileGrader.php?id=" . $aid . "&uid=" . $uid ."&cid=" . $cid . "&success=true");

		}

}





?>