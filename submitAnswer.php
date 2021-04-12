<?php

session_start();

if(!$_POST['assignment']){

	header("location: 404.html");

	exit;

}
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));

$id = $_POST['assignment'];

include "core/sqlconnect.php";

include "core/class.user.php";

$user = new user();

$class2 = $user->fetchAssinClass($id, "class");

$user->inClass($class2);



// declare vars
$assignment = $id;
$class = $_POST['class'];
$question = $_POST['question'];
$answers = $_POST['questionAnswer'];
$newAnswers = $answers[$question];
$attemptNumber = $_GET['a'];


if($newAnswers == ""){

	echo("<div class=\"alert alert-danger\" id=\"error-".$question."\" role=\"alert\"><strong>Error:</strong> You must input a value.</div>");

	exit;

}
if($attemptNumber == ""){
	echo("Invalid");
	exit;
}

$questionToAnswer = array();
$questionToAnswer[$question] = $newAnswers;

$json = json_encode($questionToAnswer, true);
mysql_query("SET CHARACTER SET utf8");

$query = mysql_query("SELECT * FROM `testinfo` WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `cid`='" . mysql_real_escape_string($class) . "' AND `aid`='" . mysql_real_escape_string($assignment) . "'");
$fetch = mysql_fetch_array($query);
$lookupTest = mysql_query("SELECT * FROM `tests` WHERE `aid`='" . mysql_real_escape_string($id) . "'");
$fetchTest = mysql_fetch_array($lookupTest);
$totalTime = $fetch['timeStarted'] + $fetchTest['timeAllowed'];
if(mysql_num_rows($query) == 0){
	if(time() < $totalTime){
		mysql_query("INSERT INTO `testinfo` (`uid`,`aid`,`cid`,`answers`,`attempt`,`timeStarted`) VALUES ('". mysql_real_escape_string($user->getUserInfo("id")) . "','" . mysql_real_escape_string($assignment) . "','" . mysql_real_escape_string($class) . "','" . mysql_real_escape_string($json). "','1','" . time() . "')");
	}else{
		echo("<script>alert('This page has expired.')</script>");
		exit;
	}
}
else{
	print " found the testinfo entry \n";
	if($attemptNumber != mysql_num_rows($query)){
		echo("invalid");
		exit;
	}
	$query = mysql_query("SELECT * FROM `testinfo` WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `cid`='" . mysql_real_escape_string($class) . "' AND `aid`='" . mysql_real_escape_string($assignment) . "' and `attempt`='" . mysql_real_escape_string($attemptNumber) . "'");
	$fetch = mysql_fetch_array($query);
	$totalTime = $fetch['timeStarted'] + $fetchTest['timeAllowed'];
	$oldAnswers = $fetch['answers'];
	$decoded = json_decode($oldAnswers,true);
	$decoded[$question] = $newAnswers;
	$new = json_encode($decoded,true);

	$attempts = $fetch['attempts'];
	$attempted = json_decode($attempts,true);

	if($attempted[$question] >= $fetchTest['retries']){
		echo("<div class=\"alert alert-danger\" id=\"error=".$question."\" role=\"alert\"><strong>Error:</strong> You have reached the max attempts for this problem.</div>");
		exit;
	}

	print "checking if time is valid. \n";
	if(time() < $totalTime){
		print "adding answer to the database \n";
		mysql_query("UPDATE `testinfo` SET `answers`='" . mysql_real_escape_string($new) . "' WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `cid`='" . mysql_real_escape_string($class) . "' AND `aid`='" . mysql_real_escape_string($assignment) . "' and `attempt`='" . mysql_real_escape_string($attemptNumber) . "'");
	}else{
		echo("<script>alert('This page has expired.')</script>");
		exit;
	}
	$findAnswers = mysql_query("SELECT * FROM `tests` WHERE `aid`='" . mysql_real_escape_string($assignment) . "' AND `cid`='" . mysql_real_escape_string($class) . "'");
	$fetchAnswers = mysql_fetch_array($findAnswers);
	$correctAnswers = json_decode($fetchAnswers['correctAnswers'],true);
	$choices = json_decode($fetchAnswers['answers'], true);

	print "checking if answer is right.\n";
	if($correctAnswers[$question] == $newAnswers){
		if($fetch['righttowrong'] != ""){
			$rightWrong = json_decode($fetch['righttowrong'],true);
			if($rightWrong[$attemptNumber] != ""){
				$aRightWrong = $rightWrong[$attemptNumber];
				$aRight = $aRightWrong[right];
				$aWrong = $aRightWrong[wrong];

				$aRight++;
				$aWrong = $aRightWrong[wrong];

				$aRightWrong[right] = $aRight;
				$aRightWrong[wrong] = $aWrong;
				$rightWrong[$attemptNumber] = $aRightWrong;

				$newGrade = ($aRight/count(json_decode($fetchTest['questions'], true)))*100;
				print $newGrade;

				$nRightWrong = json_encode($rightWrong, JSON_FORCE_OBJECT);
				
				mysql_query("UPDATE `testinfo` SET `righttowrong`='".mysql_real_escape_string($nRightWrong)."' WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `cid`='" . mysql_real_escape_string($class) . "' AND `aid`='" . mysql_real_escape_string($assignment) . "' and `attempt`='" . mysql_real_escape_string($attemptNumber) . "'");
				mysql_query("UPDATE `testinfo` SET `grade`='".mysql_real_escape_string($newGrade)."' WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `cid`='" . mysql_real_escape_string($class) . "' AND `aid`='" . mysql_real_escape_string($assignment) . "' and `attempt`='" . mysql_real_escape_string($attemptNumber) . "'");
			
			}
			else{
				$rightorWrong = array();
				$rightorWrong[right] = "1";
				$rightorWrong[wrong] = "0";
				$rightWrong[$attemptNumber] = $rightorWrong;

				$nRightWrong = json_encode($rightWrong, JSON_FORCE_OBJECT);
				mysql_query("UPDATE `testinfo` SET `righttowrong`='".mysql_real_escape_string($nRightWrong)."' WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `cid`='" . mysql_real_escape_string($class) . "' AND `aid`='" . mysql_real_escape_string($assignment) . "' and `attempt`='" . mysql_real_escape_string($attemptNumber) . "'");

			}

		}else{
			$rightWrong = array();
			$rightorWrong = array();
			$rightorWrong[right] = "1";
			$rightorWrong[wrong] = "0";

			$rightWrong[$attemptNumber] = $rightorWrong;

			$nRightWrong = json_encode($rightWrong, JSON_FORCE_OBJECT);
			mysql_query("UPDATE `testinfo` SET `righttowrong`='".mysql_real_escape_string($nRightWrong)."' WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `cid`='" . mysql_real_escape_string($class) . "' AND `aid`='" . mysql_real_escape_string($assignment) . "' and `attempt`='" . mysql_real_escape_string($attemptNumber) . "'");

		}
		if(is_array($choices[$question])){
			echo("<div class=\"alert alert-success\" id=\"success-mc".$question."\" role=\"alert\"><strong>Correct:</strong> This answer is correct!</div>");
		}
		else{
			echo("<div class=\"alert alert-success\" id=\"success-".$question."\" role=\"alert\"><strong>Correct:</strong> This answer is correct!</div>");
		}
	}else{
		print "answer is wrong \n";
		$findUserTest = mysql_query("SELECT * FROM `testinfo` WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `cid`='" . mysql_real_escape_string($class) . "' AND `aid`='" . mysql_real_escape_string($assignment) . "' and `attempt`='" . mysql_real_escape_string($attemptNumber) . "'");
		$fetchUserTest = mysql_fetch_array($findUserTest);
		$attempts = $fetchUserTest['attempts'];
		$maxAttemptPerQuestion = $fetchTest['retries'];
		// add grade defraction
		if($fetchUserTest['righttowrong'] != ""){
			$rightWrong = json_decode($fetchUserTest['righttowrong'], true);
			if($rightWrong[$attemptNumber] != ""){
				$aRightWrong = $rightWrong[$attemptNumber];
				$aRight = $aRightWrong[right];
				$aWrong = $aRightWrong[wrong];

				$aWrong++;
				$aRight = $aRightWrong[right];

				$aRightWrong[right] = $aRight;
				$aRightWrong[wrong] = $aWrong;
				$rightWrong[$attemptNumber] = $aRightWrong;

				$newGrade = ($aRight/count(json_decode($fetchTest['questions'], true)))*100;

				$nRightWrong = json_encode($rightWrong, JSON_FORCE_OBJECT);
				
				mysql_query("UPDATE `testinfo` SET `righttowrong`='".mysql_real_escape_string($nRightWrong)."' WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `cid`='" . mysql_real_escape_string($class) . "' AND `aid`='" . mysql_real_escape_string($assignment) . "' and `attempt`='" . mysql_real_escape_string($attemptNumber) . "'");
				mysql_query("UPDATE `testinfo` SET `grade`='".mysql_real_escape_string($newGrade)."' WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `cid`='" . mysql_real_escape_string($class) . "' AND `aid`='" . mysql_real_escape_string($assignment) . "' and `attempt`='" . mysql_real_escape_string($attemptNumber) . "'");
			
			}
			else{
				$rightorWrong = array();
				$rightorWrong[right] = "0";
				$rightorWrong[wrong] = "1";

				$rightWrong[$attemptNumber] = $rightorWrong;

				$nRightWrong = json_encode($rightWrong, JSON_FORCE_OBJECT);
				mysql_query("UPDATE `testinfo` SET `righttowrong`='".mysql_real_escape_string($nRightWrong)."' WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `cid`='" . mysql_real_escape_string($class) . "' AND `aid`='" . mysql_real_escape_string($assignment) . "' and `attempt`='" . mysql_real_escape_string($attemptNumber) . "'");

			}

		}else{
			$rightWrong = array();
			$rightorWrong = array();
			$rightorWrong[right] = "0";
			$rightorWrong[wrong] = "1";

			$rightWrong[$attemptNumber] = $rightorWrong;

			$nRightWrong = json_encode($rightWrong, JSON_FORCE_OBJECT);
			mysql_query("UPDATE `testinfo` SET `righttowrong`='".mysql_real_escape_string($nRightWrong)."' WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `cid`='" . mysql_real_escape_string($class) . "' AND `aid`='" . mysql_real_escape_string($assignment) . "' and `attempt`='" . mysql_real_escape_string($attemptNumber) . "'");

		}

		print "checking if data exist in db for attempts? idk \n";
		if(!is_array(json_decode($attempts,true))){
			$attemptsNew = array();
			$attemptsNew[$question] = 1;
			$jsonAttempts = json_encode($attemptsNew, JSON_FORCE_OBJECT);
			print "uh... i think we are adding an attempt here.\n";
			if(time() < $totalTime){
				print "time is not expired.. adding that attempt now \n";
				mysql_query("UPDATE `testinfo` SET `attempts`='".mysql_real_escape_string($jsonAttempts)."' WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `cid`='" . mysql_real_escape_string($class) . "' AND `aid`='" . mysql_real_escape_string($assignment) . "' and `attempt`='" . mysql_real_escape_string($attemptNumber) . "'");
			}else{
				echo("<script>alert('This page has expired.')</script>");
				exit;
			}
			print "letting user know its wrong..\n";
			if(is_array($choices[$question])){
				echo("<div class=\"alert alert-danger\" id=\"error-mc-".$question."\" role=\"alert\"><strong>Error:</strong> That was incorrect.</div>");
			}
			else{
				echo("<div class=\"alert alert-danger\" id=\"error-".$question."\" role=\"alert\"><strong>Error:</strong> That was incorrect.</div>");
			}
		}
		else{
			print "data exists. \n";
			$attempted = json_decode($attempts,true);
			$fetchAttempts = $attempts;

			print "is multiple choice. or no?\n";
			if(is_array(json_decode($fetchAttempts,true))){
				$fetchJson = true;
				$fetchAttempts2 = json_decode($fetchAttempts,true);
				$fetchAttempt = $fetchAttempts2[$question];
				echo("is array");
			}
			else{
				$fetchAttempt = $fetchAttempts;
				echo("is not");
			}
			print "if the attempt # of the question is not = to the macx attempts\n";
			if($attempted[$question] != $fetchTest['retries']){
				print "its not \n";
				$attempted[$question]++;
				$newAttempted = json_encode($attempted, JSON_FORCE_OBJECT);
				if(time() < $totalTime){
					mysql_query("UPDATE `testinfo` SET `attempts`='".mysql_real_escape_string($newAttempted)."' WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `cid`='" . mysql_real_escape_string($class) . "' AND `aid`='" . mysql_real_escape_string($assignment) . "' and `attempt`='" . mysql_real_escape_string($attemptNumber) . "'");
				}else{
					echo("<script>alert('This page has expired.')</script>");
					exit;
				}
				if(is_array($choices[$question])){
					echo("<div class=\"alert alert-danger\" id=\"error-mc".$question."\" role=\"alert\"><strong>Error:</strong> That was incorrect.</div>");
				}
				else{
					echo("<div class=\"alert alert-danger\" id=\"error-".$question."\" role=\"alert\"><strong>Error:</strong> That was incorrect.</div>");
				}
				exit;
			}
			else{
				print "max shit\n";
				echo("<div class=\"alert alert-danger\" id=\"error=".$question."\" role=\"alert\"><strong>Error:</strong> You have reached the max attempts for this problem.</div>");
				exit;
			}
		}
	}

}

?>