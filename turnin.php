<?php
session_start();
if(!$_GET['id']){
	header("location: 404.html");
	exit;
}
$id = $_GET['id'];
include "core/sqlconnect.php";
include "core/class.user.php";
$user = new user();
$class2 = $user->fetchAssinClass($id, "class");
$user->inClass($class2);
$myseql = mysql_query("SELECT * FROM `classes` WHERE `id`='" . mysql_real_escape_string($class2) . "'");
$fetck = mysql_fetch_array($myseql);
$uid = "".$user->getUserInfo("id")."";
// Define Post Variables
$assignment = $_GET['id'];

$teacher = json_decode($fetck['teacher'],true);
if(count($teacher) == 1){
	$teacher = $teacher[0];
}

if($_POST['creation']){
	$user->addNotification($uid,$teacher,"" . $user->getUserInfo("firstName") . " " . $user->getUserInfo("lastName") . " has submitted an assignment to " . $user->fetchAssinClass($id,"name") . "");
	mysql_query("INSERT INTO `turnin` (`aid`,`name`,`class`,`uid`,`href`,`time`) VALUES ('" . mysql_real_escape_string($assignment) . "','creation','" . mysql_real_escape_string($class2) . "', '" . mysql_real_escape_string($uid) . "','" . mysql_real_escape_string($_POST['creation']) . "','" . time() . "')") or die(mysql_error());
	exit;
}

if($_FILES['assignments']){
	
	for($i=0; $i<count($_FILES['assignments']['name']);$i++){
		$filePath = "./datafiles/class/" . $class2 . "/" . $assignment . "/";
		$maxSize = 100000000;
		$whiteListTypes = array('application/zip','image/png','image/jpeg','application/x-zip-compressed','application/pdf','text/plain');
		$whiteListExtensions = array('zip','png','jpeg','zip','pdf','txt');
		
		if(!is_dir("./datafiles/class/" . $class2 . "/" . $assignment . "/")){
			mkdir("./datafiles/class/" . $class2 . "/" . $assignment . "/",0777,true);
		}
		
		$tmpFilePath = $_FILES['assignments']['tmp_name'][$i];
		$fileName = $_FILES['assignments']['name'][$i];
		$fileSize = $_FILES['assignments']['size'][$i];
		$fileType = $_FILES['assignments']['type'][$i];
		
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
					$user->addNotification($uid,$teacher,"" . $user->getUserInfo("firstName") . " " . $user->getUserInfo("lastName") . " has submitted an assignment titled " . $fileName . " to " . $user->fetchAssinClass($id,"name") . "");
					mysql_query("INSERT INTO `turnin` (`aid`,`name`,`class`,`uid`,`href`,`time`) VALUES ('" . mysql_real_escape_string($assignment) . "','" . mysql_real_escape_string($fileName) . "','" . mysql_real_escape_string($class2) . "', '" . mysql_real_escape_string($uid) . "','" . $newFilePath . "','" . time() . "')") or die(mysql_error());
					$output = ['filebatchuploadsuccess'=>'Done nicely'];
					echo(json_encode($output));
					exit;
				}
		}
		
	}
	
}
else{
	$output = ['error'=>'No file found.'];
	echo(json_encode($output));
	exit;
}
?>