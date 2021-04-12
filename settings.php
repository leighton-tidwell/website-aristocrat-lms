<?php
session_start();
$id = $_GET['id'];
include "core/sqlconnect.php";
include "core/class.user.php";
$user = new user();
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
?>
<!doctype html>
<html>

<head>
<title>Student Portal</title>
<base href="http://<?php print $subdomain; ?>.aristocratlms.com/">
<link rel="stylesheet" type="text/css" href="style/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="style/global.css" />
<link rel="stylesheet" href="style/style.css" />


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
	<div class="panel panel-default" style="margin-left: 15px; margin-right: 15px">
		<div class="panel-heading">
			<h1 class="panel-title">Settings</h1><span class="glyphicon glyphicon-cog settingsButton pull-right customChevron"></span></span> 
		</div>
		<div class="panel-body">
			<h2>Email</h2>
		<div class="well">
		<fieldset>
			<form class="form-horizontal" method="POST" action="updateSettings.php">
			  <div class="form-group">
			    <label for="inputEmail" class="col-sm-2 control-label">Email</label>
			    <div class="col-sm-10">
			      <input type="email" class="form-control" id="inputEmail" value="<?php print $user->getUserInfo("email"); ?>" name="email">
			    </div>
			  </div>
			  <div class="form-group">
			    <div class="col-sm-offset-2 col-sm-10">
			      <button type="submit" class="btn btn-default">Change Email</button>
			    </div>
			  </div>
			</form>
		</fieldset>
		</div>
		<h2>Password</h2>
		 <div class="well">
			<form class="form-horizontal" method="POST" action="updateSettings.php">
			<div class="form-group">
			    <label for="inputPassword1" class="col-sm-2 control-label">Current Password</label>
			    <div class="col-sm-10">
			      <input type="password" class="form-control" id="inputPassword1" placeholder="Password" name="currPass">
			    </div>
			  </div>
			  <div class="form-group">
			    <label for="inputPassword2" class="col-sm-2 control-label">New Password</label>
			    <div class="col-sm-10">
			      <input type="password" class="form-control" id="inputPassword2" placeholder="Password" name="newPass">
			    </div>
			  </div>
			  <div class="form-group">
			    <label for="inputPassword3" class="col-sm-2 control-label">Confirm Password</label>
			    <div class="col-sm-10">
			      <input type="password" class="form-control" id="inputPassword3" placeholder="Password" name="conPass">
			    </div>
			  </div>
			  <div class="form-group">
			    <div class="col-sm-offset-2 col-sm-10">
			      <button type="submit" class="btn btn-default">Change Password</button>
			    </div>
			  </div>
			</form>
		</div>
		<h2>Notifications</h2>
		<div class="well">
			<form>
				<div class="input-group">
				  <span class="input-group-addon">
					<input type="checkbox" aria-label="...">
				  </span>
				  <span class="list-group-item">Receive All Notifications</span>
				</div>
				<h3>Email</h3>
				<div class="input-group">
				  <span class="input-group-addon">
					<input type="checkbox" aria-label="...">
				  </span>
				  <span class="list-group-item">Receive email when user turns in assignment.</span>
				</div>
				<div class="input-group">
				  <span class="input-group-addon">
					<input type="checkbox" aria-label="...">
				  </span>
				  <span class="list-group-item">Receive email when user comments on assignment.</span>
				</div>
				<div class="input-group">
				  <span class="input-group-addon">
					<input type="checkbox" aria-label="...">
				  </span>
				  <span class="list-group-item">Receive email when user sends you a message.</span>
				</div>
				<h3>Notification</h3>
				<div class="input-group">
				  <span class="input-group-addon">
					<input type="checkbox" aria-label="...">
				  </span>
				  <span class="list-group-item">Receive notification when user turns in assignment.</span>
				</div>
				<div class="input-group">
				  <span class="input-group-addon">
					<input type="checkbox" aria-label="...">
				  </span>
				  <span class="list-group-item">Receive notification when user comments on assignment.</span>
				</div>
				<div class="input-group">
				  <span class="input-group-addon">
					<input type="checkbox" aria-label="...">
				  </span>
				  <span class="list-group-item">Receive notification when user sends you a message.</span>
				</div>
			</form>
		</div>
		</div>
	</div>
</div>
<?php
include "core/footer.php";
?>
	<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>-->
</body>
<?php
include "core/modal.php";
?>
</html>