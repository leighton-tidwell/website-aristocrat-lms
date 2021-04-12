<?php
	session_start();
	$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
	// if(!$_SESSION['OK']){
	// 	header("location: login.php");
 //	}
	session_destroy();
?>
<!doctype html>
<html>

<head>
<title>Logging Out...</title>
<link rel="stylesheet" type="text/css" href="style/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="style/global.css" />
<base href="http://<?php print $subdomain; ?>.aristocratlms.com/">
<!--
Website created, designed, and coded by: Leighton Tidwell;
-->
</head>

<body>
<script language="javascript" type="text/javascript">
	var w = 0;
	var foo = setInterval(function () {
	if(w>1000) cancelInterval(foo)
		var rand = Math.floor((Math.random() * 100) + 1);
		w = w + rand;
		document.getElementById('progressbar').style.width = w + '%';
	}, 200);
	setTimeout(function(){
		if(document.getElementById('progressbar').style.width = 100 + '%'){
			window.location.assign("login");
		}
	}, 1000);
</script>
<div class="container">
	<div class="page-header">
		<h1>Logging Out...</h1>
	</div>
	<div class="progress">
		<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" id="progressbar" aria-valuemax="100" style="width: 0%">
		</div>
	</div>
</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>	
</body>

</html>