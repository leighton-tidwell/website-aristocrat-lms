<?php
session_start();
if(!$_GET['aid']){
	header("location: 404.html");
	exit;
}
$id = $_GET['aid'];
include "core/sqlconnect.php";
include "core/class.user.php";
$user = new user();
$class2 = $user->fetchAssinClass($id, "class");
$uid = "".$user->getUserInfo("id")."";
$user->inClass($class2);
$myseql = mysql_query("SELECT * FROM `classes` WHERE `id`='" . mysql_real_escape_string($class2) . "'");
$fetck = mysql_fetch_array($myseql);



$teacher = json_decode($fetck['teacher'],true);
if(count($teacher) == 1){
    $teacher = $teacher[0];
}



if($_POST['exportLinks']['application/pdf'] || $_POST['downloadUrl']){

    $accessToken = $_POST['accessToken'];
    // $downloadUrl = $_POST['downloadUrl'];
	$fileSize = $_POST['fileSize'];
	$fileType = $_POST['mimeType'];
	if($fileType == "application/pdf"){
		$downloadUrl = $_POST['downloadUrl'];
	}else{
		$downloadUrl = $_POST['exportLinks']['application/pdf'];
	}
	
	$ext = "pdf";
    $fileName =  md5($downloadUrl + time()).'.'.$ext;

    /// IF YOU WANNA USE THE ORIGINAL FILE NAME USE file.title

    // Create a stream
    $opts = array(
    'http'=>array(
    'method'=>"GET",
    'header' => "Authorization: Bearer " . $accessToken                 
    )
    );

    $context = stream_context_create($opts);

    try {
            if($downloadUrl != ""){
                $content = file_get_contents($downloadUrl, false, $context);
            }
            if (!empty($content)) 
            {
				$whiteListTypes = array('application/zip','image/png','image/jpeg','application/x-zip-compressed','application/vnd.google-apps.document','application/pdf','application/vnd.google-apps.spreadsheet','application/vnd.google-apps.presentation');
				if(!in_array($fileType,$whiteListTypes)){
					echo("<div class=\"alert alert-danger\">Incorrect filetype: " . $fileType . "</div>");
					exit;
				}
				if(!is_dir("./datafiles/class/" . $class2 . "/" . $id . "/")){
					mkdir("./datafiles/class/" . $class2 . "/" . $id . "/",0777,true);
				}
				
				$newFilePath = "./datafiles/class/" . $class2 . "/" . $id . "/" . $fileName . "";
                $upload = file_put_contents($newFilePath,$content);
                $user->addNotification($uid,$teacher,"" . $user->getUserInfo("firstName") . " " . $user->getUserInfo("lastName") . " has submitted an assignment to " . $user->fetchAssinClass($id,"name") . " titled " . $_POST['fileTitle'] . ".");
				mysql_query("INSERT INTO `turnin` (`aid`,`name`,`class`,`uid`,`href`,`time`) VALUES ('" . mysql_real_escape_string($id) . "','" . mysql_real_escape_string($fileName) . "','" . mysql_real_escape_string($class2) . "', '" . mysql_real_escape_string($uid) . "','" . $newFilePath . "','" . time() . "')") or die(mysql_error());
                echo("<div class=\"alert alert-success\">File uploaded successfully..</div>");

            } else {
                // An error occurred.
                echo("<div class=\"alert alert-danger\">Invalid filetype. Please try again or try uploading manually.</div>");
				exit;
            }
    }catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
}else{
    echo("<div class=\"alert alert-danger\">Invalid file.</div>");
	exit;
}

?>