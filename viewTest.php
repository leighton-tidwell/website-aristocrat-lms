<?php
session_start();
// if there is no assignment id, get out
if(!$_GET['id']){
	header("location: 404.html");
	exit;
}
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
$id = $_GET['id'];
include "core/sqlconnect.php";
include "core/class.user.php";
$user = new user();
$class2 = $user->fetchAssinClass($id, "class");
// is user in class?
$user->inClass($class2);

// does the test exist?
$lookupTest = mysql_query("SELECT * FROM `tests` WHERE `aid`='" . mysql_real_escape_string($id) . "'");
if(mysql_num_rows($lookupTest) == 0){
	header("Location: 404.html");
	exit;
}
$fetchTest = mysql_fetch_array($lookupTest);
$classID = $fetchTest['cid'];
$assignmentID = $id;
$timeCreated = $fetchTest['timeCreated'];

?>
<!doctype html>
<html>
<head>
<title>Student Portal</title>
<base href="http://<?php print $subdomain; ?>.aristocratlms.com/">
<link rel="stylesheet" type="text/css" href="style/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="style/global.css" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<script src="js/jquery.min.js"></script>
<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/circle-progress.js"></script>
<script src="js/calendar.js"></script>
<script src="js/notifications.js"></script>
<script src="js/canvas-to-blob.js"></script>
<script src="js/filepicker.js"></script>
<script src="js/quill.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
<script src="https://www.google.com/jsapi?key=AIzaSyAx7MDzGK4nor3q95taan5a0fZA6TApNh8"></script>
<script src="https://cdn.datatables.net/s/bs/dt-1.10.10,r-2.0.0/datatables.min.js"></script>
<script src="https://apis.google.com/js/client.js?onload=initPicker"></script>
<script>
$.widget.bridge('uibutton', $.ui.button);
$.widget.bridge('uitooltip', $.ui.tooltip);
</script>
<!--

Website created, designed, and coded by: Leighton Tidwell;

-->
</head>
<body>
<?php
include "core/navigation.php";
?>
<div class="container">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="pull-right well" id="countdown"><h3 class="panel-title"><?php print $_SESSION['timer']; ?></h3></div>
			<h2 class="panel-title">Take Test</h2>
		</div>
		<div class="panel-body">
		</div>
	</div>
</div>
<?php
include "core/footer.php";
?>
<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>-->
</body>
</html>