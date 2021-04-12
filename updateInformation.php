<?php
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";
$profile = $_POST['profile'];

// assign values
$aboutMe = $_POST['AboutMe'];
$goals = $_POST['GoalsAfterHighschool'];
$movies = $_POST['Movies'];
$tv = $_POST['TV'];
$music = $_POST['Music'];
$books = $_POST['Books'];
$interests = $_POST['Interests'];
$funFact = $_POST['FunFact'];
$quote = $_POST['quote'];
$profilePicture = $_POST['profilePicture'];

//contact
$email = $_POST['Email'];
$website = $_POST['Website'];
$im = $_POST['IM'];
$phoneNumber = $_POST['PhoneNumber'];
$website2 = $_POST['AnotherWebsite'];


mysql_query("UPDATE `users` SET `aboutMe`='".htmlspecialchars(mysql_real_escape_string($aboutMe))."' WHERE `username`='".$profile."'");
mysql_query("UPDATE `users` SET `goalsAfterHighschool`='".htmlspecialchars(mysql_real_escape_string($goals))."' WHERE `username`='".$profile."'");
mysql_query("UPDATE `users` SET `movies`='".htmlspecialchars(mysql_real_escape_string($movies))."' WHERE `username`='".$profile."'");
mysql_query("UPDATE `users` SET `tv`='".htmlspecialchars(mysql_real_escape_string($tv))."' WHERE `username`='".$profile."'");
mysql_query("UPDATE `users` SET `music`='".htmlspecialchars(mysql_real_escape_string($music))."' WHERE `username`='".$profile."'");
mysql_query("UPDATE `users` SET `books`='".htmlspecialchars(mysql_real_escape_string($books))."' WHERE `username`='".$profile."'");
mysql_query("UPDATE `users` SET `interests`='".htmlspecialchars(mysql_real_escape_string($interests))."' WHERE `username`='".$profile."'");
mysql_query("UPDATE `users` SET `funFact`='".htmlspecialchars(mysql_real_escape_string($funFact))."' WHERE `username`='".$profile."'");
mysql_query("UPDATE `users` SET `quote`='".htmlspecialchars(mysql_real_escape_string($quote))."' WHERE `username`='".$profile."'");
mysql_query("UPDATE `users` SET `profilePicture`='".htmlspecialchars(mysql_real_escape_string($profilePicture))."' WHERE `username`='".$profile."'");

mysql_query("UPDATE `users` SET `email`='".htmlspecialchars(mysql_real_escape_string($email))."' WHERE `username`='".$profile."'");
mysql_query("UPDATE `users` SET `website`='".htmlspecialchars(mysql_real_escape_string($website))."' WHERE `username`='".$profile."'");
mysql_query("UPDATE `users` SET `im`='".htmlspecialchars(mysql_real_escape_string($im))."' WHERE `username`='".$profile."'");
mysql_query("UPDATE `users` SET `phoneNumber`='".htmlspecialchars(mysql_real_escape_string($phoneNumber))."' WHERE `username`='".$profile."'");
mysql_query("UPDATE `users` SET `website2`='".htmlspecialchars(mysql_real_escape_string($website2))."' WHERE `username`='".$profile."'");



header("Location: profile/".$profile."");
exit;
?>