<?php
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";
$user = new user();
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
?>
<!doctype html>
<html>

<head>
<title>Student Portal</title>
<base href="http://<?php print $subdomain ?>.aristocratlms.com/">
<link rel="stylesheet" type="text/css" href="style/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="style/global.css" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<!--
Website created, designed, and coded by: Leighton Tidwell;
-->
</head>
<body>
<?php
include "core/navigation.php";
?>
<div class="container">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Privacy Information</h3>
		</div>
		<div class="panel-body">
			<h1>
				How are my passwords stored?
			</h1>
			<blockquote>
				All passwords are stored using MD5 Algorithms. This means that your password is never actually seen or acknowledged by the server or anyone else. As soon as you change or create your password it becomes encrypted.<br />
				Here are some examples of MD5 encrypted passwords:<br />
			</blockquote>
				<div class="well">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Password</th>
								<th>MD5 Algorithm</th>
							</tr>
						</thead>
						<tr>
							<td>test123</td>
							<td>cc03e747a6afbbcbf8be7668acfebee5</td>
						</tr>
						<tr>
							<td>password</td>
							<td>5f4dcc3b5aa765d61d8327deb882cf99</td>
						</tr>						 
					</table>
				</div>
			<hr>
			<h1>
				Can anyone see my profile?
			</h1>
			<blockquote>
				Only the people who are logged into the ATEMS Student Portal will be able to view a profile that is listed on here.
			</blockquote>
		</div>
	</div>
</div>
<?php
	include "core/footer.php";
?>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>