<?php
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
$user = new user();
if(!$user->isTeacher()){
	header("Location 404.html");
	exit;
}

$assignmentID = $_GET['aid'];
$questionNumber = $_GET['qid'];
$questionName = $_POST['questionName'];
$questionAnswers = $_POST['questionAnswer'];
$correctAnswer = $_POST['correctAnswer'] - 1;

if($_GET['delete'] == "true"){
	if($questionNumber){
		if($assignmentID){
			$query = mysql_query("SELECT * FROM `tests` WHERE `aid`='" . mysql_real_escape_string($assignmentID) . "'");
			$fetch = mysql_fetch_array($query);
			
			$dbQ = $fetch['questions'];
			$dbA = $fetch['answers'];
			$dbC = $fetch['correctAnswers'];
				
			if($dbQ == "" && $dbA == ""  && $dbC == ""){
				print "No questions found to delete.";
				exit;
			}
			else{
				$array = json_decode($dbQ,true);
				$array2 = json_decode($dbA,true);
				$array3 = json_decode($dbC,true);
				
				unset($array[$questionNumber]);
				unset($array2[$questionNumber]);
				unset($array3[$questionNumber]);
				
				$jsonQ = json_encode($array, JSON_FORCE_OBJECT);
				$jsonA = json_encode($array2, JSON_FORCE_OBJECT);
				$jsonC = json_encode($array3, JSON_FORCE_OBJECT);
				mysql_query("UPDATE `tests` SET `correctAnswers`='" . mysql_real_escape_string($jsonC) . "' WHERE `aid`='" . mysql_real_escape_string($assignmentID) . "'");
				mysql_query("UPDATE `tests` SET `questions`='" . mysql_real_escape_string($jsonQ) . "' WHERE `aid`='" . mysql_real_escape_string($assignmentID) . "'");
				mysql_query("UPDATE `tests` SET `answers`='" . mysql_real_escape_string($jsonA) . "' WHERE `aid`='" . mysql_real_escape_string($assignmentID) . "'");
				exit;
			}
		}
		else{
			print "No assignment data.";
		}
	}
	else{
		print "No question submitted for deletion.";
		exit;
	}
}
if($assignmentID){
	if($questionNumber){
		if($questionName){
			if($questionAnswers){
				
				$query = mysql_query("SELECT * FROM `tests` WHERE `aid`='" . mysql_real_escape_string($assignmentID) . "'");
				$fetch = mysql_fetch_array($query);
				
				$dbQ = $fetch['questions'];
				$dbA = $fetch['answers'];
				$dbC = $fetch['correctAnswers'];
				
				if($dbQ == "" && $dbA == ""  && $dbC == ""){
					$array = array();
					$array[$questionNumber] = $questionName;
					$jsonQ = json_encode($array, JSON_FORCE_OBJECT);
					
					if(!is_array($questionAnswers)){
						$array2 = array();
						$array2[$questionNumber] = $questionAnswers;
						$jsonA = json_encode($array2, JSON_FORCE_OBJECT);
						
						$array3 = array();
						$array3[$questionNumber] = $questionAnswers;
						$jsonC = json_encode($array3);
						mysql_query("UPDATE `tests` SET `correctAnswers`='" . mysql_real_escape_string($jsonC) . "' WHERE `aid`='" . mysql_real_escape_string($assignmentID) . "'");
					}
					else{
						$array2 = array();
						$array2[$questionNumber] = $questionAnswers;
						$jsonA = json_encode($array2, JSON_FORCE_OBJECT);
						
						
						
						$array3 = array();
						$array3[$questionNumber] = "" . $array2[$questionNumber][$correctAnswer] . "";
						$jsonC = json_encode($array3);
						
						mysql_query("UPDATE `tests` SET `correctAnswers`='" . mysql_real_escape_string($jsonC) . "' WHERE `aid`='" . mysql_real_escape_string($assignmentID) . "'");
					}
				mysql_query("UPDATE `tests` SET `questions`='" . mysql_real_escape_string($jsonQ) . "' WHERE `aid`='" . mysql_real_escape_string($assignmentID) . "'");
				mysql_query("UPDATE `tests` SET `answers`='" . mysql_real_escape_string($jsonA) . "' WHERE `aid`='" . mysql_real_escape_string($assignmentID) . "'");
				
				echo("<div class=\"alert alert-success\">This question was saved successfully</div>");
				}
				else{
					$array = json_decode($dbQ,true);
					$array2 = json_decode($dbA,true);
					$array3 = json_decode($dbC,true);
					
					$array[$questionNumber] = $questionName;
					$jsonQ = json_encode($array);
					if(!is_array($questionAnswers)){
						$array2[$questionNumber] = $questionAnswers;
						$jsonA = json_encode($array2, JSON_FORCE_OBJECT);
						
						$array3[$questionNumber] = $questionAnswers;
						$jsonC = json_encode($array3, JSON_FORCE_OBJECT);
						mysql_query("UPDATE `tests` SET `correctAnswers`='" . mysql_real_escape_string($jsonC) . "' WHERE `aid`='" . mysql_real_escape_string($assignmentID) . "'");
					}
					else{
						$array2[$questionNumber] = $questionAnswers;
						$jsonA = json_encode($array2, JSON_FORCE_OBJECT);
						
						$array3[$questionNumber] = "" . $array2[$questionNumber][$correctAnswer] . "";
						$jsonC = json_encode($array3, JSON_FORCE_OBJECT);
						
						mysql_query("UPDATE `tests` SET `correctAnswers`='" . mysql_real_escape_string($jsonC) . "' WHERE `aid`='" . mysql_real_escape_string($assignmentID) . "'");
					}
				mysql_query("UPDATE `tests` SET `questions`='" . mysql_real_escape_string($jsonQ) . "' WHERE `aid`='" . mysql_real_escape_string($assignmentID) . "'");
				mysql_query("UPDATE `tests` SET `answers`='" . mysql_real_escape_string($jsonA) . "' WHERE `aid`='" . mysql_real_escape_string($assignmentID) . "'");
				
				echo("<div class=\"alert alert-success\">This question was saved successfully</div>");
				}
				
			}
			else{
				echo("<div class=\"alert alert-danger\">Invalid Assignment..</div>");
				exit;
			}
		}
		else{
			echo("<div class=\"alert alert-danger\">Question name was left blank.</div>");
			exit;
		}
	}
	else{
		echo("<div class=\"alert alert-danger\">Invalid Question..</div>");
		exit;
	}
}
else{
	echo("<div class=\"alert alert-danger\">Invalid Assignment..</div>");
	exit;
}
?>