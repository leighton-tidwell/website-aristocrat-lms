<?php

session_start();

include "core/sqlconnect.php";

include "core/passHash.php";
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));

if($_SESSION['OK']){

	header("location: index");

	exit;

}



if($_GET['id'] == 1){



	$code = $_POST['joinCode'];

	$fName = $_POST['firstName'];

	$lName = $_POST['lastName'];

	$username = $_POST['username'];

	$password = $_POST['password'];

	

	if($code == ""){

		echo("<div class=\"alert alert-danger\">The school code can not be blank.</div>");

		exit;

	}

	if($fName == ""){

		echo("<div class=\"alert alert-danger\">Your first name can not be blank.</div>");

		exit;

	}

	if($lName == ""){

		echo("<div class=\"alert alert-danger\">Your last name can not be blank.</div>");

		exit;

	}

	if($username == ""){

		echo("<div class=\"alert alert-danger\">Your username can not be blank.</div>");

		exit;

	}

	if(preg_match('/\s/',$username)){

		echo("<div class=\"alert alert-danger\">Your username can not contain spaces.</div>");

		exit;

	}

	$valid = array("-","_",".");

	if(!ctype_alnum(str_replace($valid, '', $username))){

		echo("<div class=\"alert alert-danger\">Your username must only have numbers and letters.</div>");

		exit;

	}



	if($password == ""){

		echo("<div class=\"alert alert-danger\">Your password can not be blank.</div>");

		exit;

	}

	

	$findSchool = mysql_query("SELECT * FROM `school` WHERE `code`='" . mysql_real_escape_string($code) . "'");

	if(mysql_num_rows($findSchool) == 0){

		echo("<div class=\"alert alert-danger\">The school code is invalid.</div>");

		exit;

	}

	else{

		$fetchSchool = mysql_fetch_array($findSchool)[id];

		$checkUsername = mysql_query("SELECT * FROM `users` WHERE `username`='" . mysql_real_escape_string($username) . "'");

		if(mysql_num_rows($checkUsername) != 0){

			echo("<div class=\"alert alert-danger\">That username is taken.</div>");

			exit;

		}

		else{
			$pass = password_hash($password, PASSWORD_BCRYPT);
			mysql_query("INSERT INTO `users` (`username`,`password`,`firstName`,`lastName`,`school`) VALUES ('" . mysql_real_escape_string($username) . "','" . $pass . "','" . mysql_real_escape_string($fName) . "','" . mysql_real_escape_string($lName) . "','" . $fetchSchool . "')");

			$userID = mysql_insert_id();

			mkdir("./datafiles/users/" . $userID . "",0777,true);

			$fileMake = fopen("./datafiles/users/" . $userID . "/index.php","w");
			fclose($fileMake);

			echo("<div class=\"alert alert-success\">Successfully registered!<script>window.location='/login?username=" . $username . "';</script></div>");

			exit;

		}

	}

	

	

	exit;

}



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

<div class="container">

	<div class="row">

		<div class="col-md-4 col-md-offset-4">

			<div class="wrap panel panel-primary">

				<div class="panel-heading">

					<h1 class="panel-title">Register For an Account</h1>

				</div>

				<div class="panel-body">

					<form class="form-vertical" method="post" id="reggie" style="margin-top:25px;">

						<label for="joinCode" class="control-label">School Code:</label>

                        <div class="input-group">

							<span class="input-group-addon" id="addon1"><span class="glyphicon glyphicon-qrcode"></span></span>

							<input type="text" name="joinCode" class="form-control" placeholder="Join Code" aria-describedby="addon1"/>

                        </div>

						<br />

						<label for="firstName" class="control-label">First Name:</label>

                        <div class="input-group">

							<span class="input-group-addon" id="addon2"><span class="glyphicon glyphicon-tag"></span></span>

							<input type="text" name="firstName" class="form-control" placeholder="First Name" aria-describedby="addon2"/>

                        </div>

						<br />

						<label for="lastName" class="control-label">Last Name:</label>

                        <div class="input-group">

							<span class="input-group-addon" id="addon3"><span class="glyphicon glyphicon-tag"></span></span>

							<input type="text" name="lastName" class="form-control" placeholder="Last Name" aria-describedby="addon3"/>

                        </div>

						<br />

						<label for="username" class="control-label">Username:</label>

                        <div class="input-group">

							<span class="input-group-addon" id="addon4"><span class="glyphicon glyphicon-user"></span></span>

							<input type="text" name="username" class="form-control" placeholder="Username" aria-describedby="addon4"/>

                        </div>

						<br />

						<label for="password" class="control-label">Password:</label>

                        <div class="input-group">

							<span class="input-group-addon" id="addon1"><span class="glyphicon glyphicon-asterisk"></span></span>

							<input type="password" name="password" class="form-control" placeholder="" aria-describedby="addon1"/>

                        </div>

						<br />

                        <div class="form-group">

						  <button type="button" onclick="register();" id="loader" value="Register" class="btn btn-success">Register!</button>

                        </div>

					</form>

					<div id="debug">

					</div>

				</div>

				<div class="panel-footer">

					By registering you are agreeing to the T&C and our Privacy Agreement.<br />
                   <center><a href="/login.php">Click to login</a></center>

				</div>

			</div>

		</div>

	</div>

</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

	<script src="js/bootstrap.min.js"></script>	

	<script>

		function register(){

			var form = $( "#reggie" ).serialize();

			$.post( "/register.php?id=1", form).done(function(data){

				$("#debug").html(data);

			});

		}

	</script>

</body>



</html>