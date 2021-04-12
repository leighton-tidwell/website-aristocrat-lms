<?php
session_start();
$id = $_GET['id'];
include "core/sqlconnect.php";
include "core/class.user.php";
$user = new user();
?>
<doctype html>
<html>

<head>
<title>Student Portal</title>
<link rel="stylesheet" type="text/css" href="style/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="style/global.css" />
<link rel="stylesheet" href="style/style.css" />


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

</div>
<?php
include "core/footer.php";
?>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>