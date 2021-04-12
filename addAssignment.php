<?php
session_start();
$id = $_POST['class'];
include "core/sqlconnect.php";
include "core/class.user.php";
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
$user = new user();
$user->inClass($id);
if(!$user->isTeacher()){
	header("Location 404.html");
	exit;
}
$class = $id;

// Let's check for empty values
if($class != ""){
	if($_POST['assignment'] != ""){
		$name = $_POST['assignment'];
		if($_POST['type'] == "1" || $_POST['type'] == "2"){
			$type = $_POST['type'];
			if($_POST['date']){
				$date = "" . $_POST['date'] . " " .  $_POST['time'] . "";
					$newDate = strtotime($date);
					$time = strtotime($postTime);
					mysql_query("INSERT INTO `assignments` (`name`,`timeAssigned`,`timeDue`,`class`,`type`) VALUES ('" . mysql_real_escape_string($name) . "', '" . time() . "', '" . mysql_real_escape_string($newDate) . "', '" . mysql_real_escape_string($class) . "', '" . mysql_real_escape_string($type) . "')");
					mkdir("./datafiles/class/" . $id . "/" . mysql_insert_id()."/",0777,true);
					fopen("./datafiles/class/" . $id . "/" . mysql_insert_id()."/index.php", "w");
					$danewwun = mysql_insert_id();
					$arry = $user->getUsersFrom($user->getUserInfo("school"));
					foreach($arry as $users){
						$find = strpos($user->fetchProfileInfo($users,"classes"),$class);
						if($find !== false){
							if($users != $user->getUserInfo("id")){
								$user->addNotification($user->getUserInfo("id"),$users,"" . $user->getUserInfo("firstName") . " " . $user->getUserInfo("lastName") . " has added a new assignment titled " . $name . " due " . date("F j Y, g:i a", $newDate) . "");
							}
						}
					}
					header("Location: http://" . $subdomain . ".aristocratlms.com/editAssignment.php?id=" . $danewwun . "");
					exit;
			}
			else
			{
				header("Location: http://" . $subdomain . ".aristocratlms.com/editCourse.php?id=" . $class . "&error=date");
				exit;
			}
		}
		else
		{
			header("Location: http://alpha.aristocratlms.com/editCourse.php?id=" . $class . "&error=type");
			exit;
		}
	}
	else
	{
		header("Location: http://alpha.aristocratlms.com/editCourse.php?id=" . $class . "&error=name");
		exit;
	}
}
?>