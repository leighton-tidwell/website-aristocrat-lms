<?php
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";
$user = new user();
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
if(!$user->isTeacher()){
	header("Location 404.html");
	exit;
}
$id = $_POST['id'];
$class2 = $user->fetchAssinClass($id, "class");

$query = mysql_query("SELECT * FROM `tests` WHERE `aid`='" . mysql_real_escape_string($id) . "'");
$isTest = mysql_num_rows($query);

if($isTest == 0){
	$picture = $_POST['picture'];
	$name = $_POST['name'];
	$description = $_POST['description'];
	$dueDate = $_POST['dueDate'];
	$visible = $_POST['visible'];
	$resources = $_POST['resources'];
	$file = $_POST['resource'];

	if($_FILES['resource']){
		for($i=0; $i<count($_FILES['resource']['name']);$i++){
			if($_FILES['resource']['name'][$i] != ""){
		$filePath = "./datafiles/class/" . $class2 . "/" . $id . "/";
		$maxSize = 100000000;
		$whiteListTypes = array('application/zip','image/png','image/jpeg','application/x-zip-compressed');
		$whiteListExtensions = array('zip','png','jpeg','zip');
		
		if(!is_dir("./datafiles/class/" . $class2 . "/" . $id. "/")){
			mkdir("./datafiles/class/" . $class2 . "/" . $id . "/",0777,true);
		}
		
		$tmpFilePath = $_FILES['resource']['tmp_name'][$i];
		$fileName = $_FILES['resource']['name'][$i];
		$fileSize = $_FILES['resource']['size'][$i];
		$fileType = $_FILES['resource']['type'][$i];

		if(!in_array($fileType, $whiteListTypes)){
			$output = ['error'=>'File type of ' . $fileName . ' is not supported. (RESCODE: ' . $fileType . ')'];
			echo(json_encode($output));
			exit;
		}
		
		$location = array_search($fileType, $whiteListTypes);
		// check for array_search == false!
		
		$extension = $whiteListExtensions[$location];
		
		if($fileSize > $maxSize){
			$output = ['error'=>'File is too large. (RESCODE: ' . $fileSize . ')'];
			echo(json_encode($output));
			exit;
		}
		
		if($fileType == "image/png" || $fileType == "image/jpeg" || $fileType == "image/png"){
			if(!getimagesize($tmpFilePath)){
				$output = ['error'=>'File is not a valid image.'];
				echo(json_encode($output));
				exit;
			}
		}
		
		$newName = md5($fileName + time());
		
		$newFilePath = "" . $filePath . "" . $newName . "." . $extension;
		
		if($tmpFilePath != ""){
				if(move_uploaded_file($tmpFilePath, $newFilePath)){
					if($fileName != "" && $newFilePath != "" && $resources[0] != ""){
						$counts = count($resources);
						$resources[$counts] = $fileName;
						$resources[$counts + 1] = $newFilePath;
					}
					else{
						$counts = 0;
						$resources[$counts] = $fileName;
						$resources[$counts + 1] = $newFilePath;
					}
				}
		}
	}
		
	}
	}
	$dater = "" . $dueDate . " " . $_POST['dueTime'] . "";

	$newDate = strtotime($dater);


	$finalResources = json_encode($resources, JSON_FORCE_OBJECT);

	mysql_query("UPDATE `assignments` SET `picture`='" . mysql_real_escape_string($picture) . "' WHERE `id`='" . mysql_real_escape_string($id) . "'");
	mysql_query("UPDATE `assignments` SET `name`='" . mysql_real_escape_string($name) . "' WHERE `id`='" . mysql_real_escape_string($id) . "'");
	mysql_query("UPDATE `assignments` SET `description`='" . mysql_real_escape_string($description) . "' WHERE `id`='" . mysql_real_escape_string($id) . "'");
	mysql_query("UPDATE `assignments` SET `timeDue`='" . mysql_real_escape_string($newDate) . "' WHERE `id`='" . mysql_real_escape_string($id) . "'");
	mysql_query("UPDATE `assignments` SET `visible`='" . mysql_real_escape_string($visible) . "' WHERE `id`='" . mysql_real_escape_string($id) . "'");
	mysql_query("UPDATE `assignments` SET `resources`='" . mysql_real_escape_string($finalResources) . "' WHERE `id`='" . mysql_real_escape_string($id) . "'");
	$arry = $user->getUsersFrom($user->getUserInfo("school"));
	foreach($arry as $users){
		$find = strpos($user->fetchProfileInfo($users,"classes"),$class2);
		if($find !== false){
			if($users != $user->getUserInfo("id")){
				$user->addNotification($user->getUserInfo("id"),$users,"" . $user->getUserInfo("firstName") . " " . $user->getUserInfo("lastName") . " has edited an assignment titled " . $name . ".");
			}
		}
	}
}
else{
	$name = $_POST['name'];
	$description = $_POST['description'];
	$attempts = $_POST['attempts'];
	$timeAllowed = $_POST['timeAllowed'];
	$timeDue = $_POST['timeDue'];
	$timeDueTime = $_POST['timeDueTime'];
	$type = $_POST['IUD'];
	$newTime = "" . $timeDue . " " .$timeDueTime ."";
	
	$newDate = strtotime($newTime);
	
	if($type == "3"){
		$timeAllowed = "-1";
	}
	
	mysql_query("UPDATE `assignments` SET `name`='" . mysql_real_escape_string($name) . "' WHERE `id`='" . mysql_real_escape_string($id) . "'");
	mysql_query("UPDATE `assignments` SET `description`='" . mysql_real_escape_string($description) . "' WHERE `id`='" . mysql_real_escape_string($id) . "'");
	mysql_query("UPDATE `assignments` SET `timeDue`='" . mysql_real_escape_string($newDate) . "' WHERE `id`='" . mysql_real_escape_string($id) . "'");
	mysql_query("UPDATE `tests` SET `timeDue`='" . mysql_real_escape_string($newDate) . "' WHERE `aid`='" . mysql_real_escape_string($id) . "'");
	mysql_query("UPDATE `tests` SET `timeAllowed`='" . mysql_real_escape_string($timeAllowed) . "' WHERE `aid`='" . mysql_real_escape_string($id) . "'");
	mysql_query("UPDATE `tests` SET `attempts`='" . mysql_real_escape_string($attempts) . "' WHERE `aid`='" . mysql_real_escape_string($id) . "'");
	
	$arry = $user->getUsersFrom($user->getUserInfo("school"));
	foreach($arry as $users){
		$find = strpos($user->fetchProfileInfo($users,"classes"),$id);
		if($find){
			if($users != $user->getUserInfo("id")){
				$user->addNotification($user->getUserInfo("id"),$users,"" . $user->getUserInfo("firstName") . " " . $user->getUserInfo("lastName") . " has edtied an assignment titled '" . $name . "'.");
			}
		}
	}
	
}

header("Location: editAssignment/" . $id . "&succcess=true");
exit;