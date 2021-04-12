<?php
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";
$user = new user();

// is it a teacher?
if(!$user->isTeacher()){
	header("Location 404.html");
	exit;
}

// make sure a class is selected
if($_GET['id'] == ""){
	header("Location: 404.html");
	exit;
}

// assign class id
$cid = $_GET['id'];

// is teacher in class?
$user->inClass($cid);


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
		<div class="row">
			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-heading">
						Choose Day
					</div>
					<div class="panel-body">
					</div>
				</div>
			</div>
			<div class="col-md-8">
				<div class="panel panel-default">
					<div class="panel-heading">
						Attendance for {CLASS} on {DATE}
					</div>
					<div class="panel-body">
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
		include "core/footer.php";
	?>
	</body>
	<?php
		include "core/modal.php";
	?>
</html>