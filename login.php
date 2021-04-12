<?php
include "core/sqlconnect.php";
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
session_start();
$submit = $_GET['submit'];
$qrt = mysql_query("SELECT * FROM `school` where `subdomain`='" . mysql_real_escape_string($subdomain) . "'");
$ftch = mysql_fetch_array($qrt);
if($_SESSION['OK']){
	header("location: index");
	exit;
}
if($submit){
	$username = $_POST['username'];
	$password = $_POST['password'];

	if(!$username || !$password){
		header("location: login?error=true");
		exit;
	}

	include "core/sqlconnect.php";
	include "core/passHash.php";
	
	$query = mysql_query("SELECT * FROM `users` WHERE `username`='".$username."'");
	$fetch = mysql_fetch_array($query);
	
	if(password_verify($password,$fetch['password'])){
		$_SESSION['username'] = $username;
		$_SESSION['OK'] = true;
		header("location: index");
		exit;
	}else{
		header("location: login?error=true");
		exit;
	}
}
if($_GET['username']){
	$username = $_GET['username'];
}
?>
<!doctype html>
<html>

<head>
<title>Student Portal</title>
<base href="http://<?php print $subdomain ?>.aristocratlms.com/">
<link rel="stylesheet" type="text/css" href="style/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="style/global.css" />
<link rel="stylesheet" type="text/css" href="style/login.css" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<!--
Website created, designed, and coded by: Leighton Tidwell;
-->
</head>

<body>
<div class="container">
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<h4 class="text-center">
				<?php
					if($ftch['login_logo'] == ""){
						print $ftch['name'];
					}else{
						echo("<img class=\"img-responsive\" src=\"" . $ftch['login_logo'] > "\" />");
					}
				?>
			</h4>
			<div class="wrap panel panel-default">
				<div class="panel-heading">
                	<h1 class="panel-title">Sign In</h1>
                </div>
                <div class="panel-body">
   					<form class="form-vertical <?php if($_GET['error']){ print "has-error"; } ?>" action="login.php?submit=true" method="post" style="margin-top:25px;">
                    	<label for="username" class="control-label">Username:</label>
                        <div class="input-group">
							<span class="input-group-addon" id="addon1"><span class="glyphicon glyphicon-user"></span></span>
							<input type="text" name="username" class="form-control" value="<?php print $username ?>" placeholder="Username" aria-describedby="addon1"/>
                        </div>
                        <br />
                        <label for="password" class="control-label">Password:</label>
                        <div class="input-group">
							<span class="input-group-addon" id="addon1"><span class="glyphicon glyphicon-asterisk"></span></span>
							<input type="password" name="password" placeholder="Password" class="form-control" aria-describedby="addon1"/>
                        </div>
                        <br />
                        <div class="form-group">
						  <input type="submit" value="Sign In" class="btn btn-primary"/>
                        </div>
					</form>
					<?php if($_GET['error']) : ?>
						<br />
						<div class="alert alert-danger" role="alert">
							<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
							<span class="sr-only">Error:</span>
							Invalid username or password.
						</div>
					<?php endif; ?>
               </div>
               <div class="panel-footer">
               		<a href="/register">Register for an account</a>
               </div>
			</div>
		</div>
	</div>
</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>	
</body>

</html>