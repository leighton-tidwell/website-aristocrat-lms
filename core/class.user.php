<?php
date_default_timezone_set('America/Chicago');
if(!$_SESSION['OK'])
{
	header("location: login.php");
	exit;
}
class user
{
	function getUserInfo($variable){
		$query = mysql_query("SELECT * FROM `users` WHERE `username`='".mysql_real_escape_string($_SESSION['username'])."'");
		$fetch = mysql_fetch_array($query);
		return $fetch[$variable];
	}
	function fetchProfileInfo($variable, $fetchItem){
		$query = mysql_query("SELECT * FROM `users` WHERE `username`='".mysql_real_escape_string($variable)."'");
		if(mysql_num_rows($query) == 0){
			$query = mysql_query("SELECT * FROM `users` WHERE `id`='".mysql_real_escape_string($variable)."'");
		}
		$fetch = mysql_fetch_array($query);
		return $fetch[$fetchItem];
	}
	function getSchool($school){
		$query = mysql_query("SELECT * FROM `school` WHERE `id`='".mysql_real_escape_string($school)."'");
		$fetch = mysql_fetch_array($query);
		print $fetch["name"];
	}
	function isTeacher(){
		if($this->getUserInfo("permissions") == "2"){
			return true;
		}
		else{
			return false;
		}
	}
	function calculateGrades($class,$user){
		if($user){
			$query = mysql_query("SELECT * FROM `grades` WHERE `uid`='" . mysql_real_escape_string($user). "' AND `cid`='" . mysql_real_escape_string($class) ."'");
			if(mysql_num_rows($query) == 0){
				$finalAverage = 0.0;
			}
			else{
				
				// define placeholder variables
				$grade = 0;
				$grades = 0;
				
				// for each grade found
				while($fetch = mysql_fetch_array($query)){

					// do math to calculate average
					$placeholder = $fetch['grade'];
					$newGrade = $grade + $placeholder;
					$grade = $newGrade;
					$grades++;
				}
				
				// do final calculation.. total grade divided by total grade(s)
				$finalAverage = $grade/$grades;
			}
		}
		else{
			$query = mysql_query("SELECT * FROM `grades` WHERE `cid`='" . mysql_real_escape_string($class) . "'");
			if(mysql_num_rows($query) == 0){
				$finalAverage = 0.0;
			}
			else{
				// define placeholder variables
				$grade = 0;
				$grades = 0;
				
				// for each grade found
				while($fetch = mysql_fetch_array($query)){

					// do math to calculate average
					$placeholder = $fetch['grade'];
					$newGrade = $grade + $placeholder;
					$grade = $newGrade;
					$grades++;
				}
				
				// do final calculation.. total grade divided by total grade(s)
				$finalAverage = $grade/$grades;
			}
		}
		return $finalAverage;
	} 
	function getUsers(){
		$query = mysql_query("SELECT `id` FROM `users` ORDER BY `firstName`");
		$tmp = array();
		while(($fetch = mysql_fetch_array($query)) != NULL)
		{
			array_push($tmp, $fetch['id']);
		}
		return $tmp;
	}
	function getUsersFrom($variable){
		$query = mysql_query("SELECT `id` FROM `users` WHERE `school`='".mysql_real_escape_string($variable)."' ORDER BY `firstName`");
		$tmp = array();
		while(($fetch = mysql_fetch_array($query)) != NULL)
		{
			array_push($tmp, $fetch['id']);
		}
		return $tmp;
	}
	function inClass($variable){
		$classes = $this->getUserInfo("classes");
		$classes = json_decode($classes,true);

		foreach($classes as $semester){
			if($semester != ""){
				foreach($semester as $subdomain){
					if($subdomain != ""){
						foreach($subdomain as $period=>$class){
							if($class == $variable){
								$test = true;
							}
						}
					}
				}
			}
		}
		if(!$test){
			$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
			header("Location: http://" . $subdomain . ".aristocratlms.com/404.html");
			exit;
		}
	}
	function getClassInfo($variable, $fetchItem){
		$query = mysql_query("SELECT * FROM `classes` WHERE `id`='".mysql_real_escape_string($variable)."'");
		$fetch = mysql_fetch_array($query);
		return $fetch[$fetchItem];
	}
	function getYear(){
		$datetime = new DateTime($dateTimeString);
		$year = $datetime->format('Y');
		return $year;
	}
	function getMonth(){
		$datetime = new DateTime($dateTimeString);
		$month = $datetime->format('m');
		return $month;
	}
	function getDay(){
		$datetime = new DateTime($dateTimeString);
		$day = $datetime->format('d');
		return $day;
	}
	function monthName($month){
		if($month == "01"){
			$final = "January";
		}
		if($month == "02"){
			$final = "February";
		}
		if($month == "03"){
			$final = "March";
		}
		if($month == "04"){
			$final = "April";
		}
		if($month == "05"){
			$final = "May";
		}
		if($month == "06"){
			$final = "June";
		}
		if($month == "07"){
			$final = "July";
		}
		if($month == "08"){
			$final = "August";
		}
		if($month == "09"){
			$final = "September";
		}
		if($month == "10"){
			$final = "October";
		}
		if($month == "11"){
			$final = "November";
		}
		if($month == "12"){
			$final = "December";
		}
		return $final;
	}
	function getAssignments($class){
		$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
		$class = mysql_real_escape_string($class);
		$thirtyDay = time() + 2592000;
		$classList = mysql_query("SELECT * FROM `assignments` WHERE `class`='".$class."' AND `delete`='0' AND `timeDue` BETWEEN '" . time() . "' AND '" . $thirtyDay . "' ORDER BY `timeDue` ASC");	
		while($classArray = mysql_fetch_array($classList)){
				echo("<tr>");
					echo("<td><a href=\"http://" . $subdomain . ".aristocratlms.com/assignment.php?id=" . $classArray['id'] . "\">" . $classArray['name']. "</a></td>");
					echo("<td>" . date("F j", $classArray['timeDue']). "</td>");
				echo("</tr>");
	
		}
		if(mysql_num_rows($classList) == 0){
			echo("<tr><td>No upcoming</td><td>assignments</td></tr>");
		}
	}
    function getAssignmentsIndex($class){
		$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
		$class = mysql_real_escape_string($class);
		$seven = time() + 604800;
		$classList = mysql_query("SELECT * FROM `assignments` WHERE `class`='".$class."' AND `delete`='0' AND `timeDue` BETWEEN '" . time() . "' AND '" . $seven . "' ORDER BY `timeDue` ASC") or die(mysql_error());	
        if(mysql_num_rows($classList) == 0){
            echo("There are no upcoming assignments.");
        }
		while($classArray = mysql_fetch_array($classList)){
            echo("<dt><a href=\"http://" . $subdomain . ".aristocratlms.com/assignment.php?id=" . $classArray['id'] . "\">" . $classArray['name']. "</a></dt>");
            echo("<dd>" . date("F j, Y", $classArray['timeDue']). " at " . date("g:i a", $classArray['timeDue']). "</dd>");
		}
	}
	function getAssignmentsCourse($class){
		$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
		$class = mysql_real_escape_string($class);
		$classList = mysql_query("SELECT * FROM `assignments` WHERE `class`='".$class."' AND `delete`='0' ORDER BY `timeDue` DESC");	
		while($classArray = mysql_fetch_array($classList)){
			echo("<tr>");
				echo("<td><a href=\"http://" . $subdomain . ".aristocratlms.com/assignment.php?id=".$classArray['id']."\">" . $classArray['name']. "</a></td>");
				if($classArray['type'] == "1"){
					echo("<td>Assignment</td>");
				}
				else{
					echo("<td>Test</td>");
				}
				echo("<td>" . date("F j", $classArray['timeDue']). "</td>");
				echo("<td>" . date("g:i a", $classArray['timeDue']) . "</td>");
				echo("<td><a href=\"editAssignment.php?id=" . $classArray['id'] . "&delete=true\"><span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span></a>&nbsp;<a href=\"editAssignment.php?id=" . $classArray['id'] . "\"><span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span></a></td>");
			echo("</tr>");
		}
		if(mysql_num_rows($classList) == 0){
			echo("<tr><td>No upcoming</td><td>assignments</td></tr>");
		}
	}
	function fetchAssinClass($aid, $string){
		$aid = mysql_real_escape_string($aid);
		$string = mysql_real_escape_string($string);
		$ainfo = mysql_query("SELECT * FROM `assignments` WHERE `id`='".$aid."'");
		$afetch = mysql_fetch_array($ainfo);
		return $afetch[$string];
	}
	function checkifGraded($uid,$cid,$aid){
		$gradeQuery = mysql_query("SELECT * FROM `grades` WHERE `uid`='" . mysql_real_escape_string($uid)."' AND `cid`='" . mysql_real_escape_string($cid)."' AND `aid`='".mysql_real_escape_string($aid)."'");
		if(mysql_num_rows($gradeQuery) == 0){
			return false;
		}
		else{
			return true;
		}
	}
	function calcGrade($uid,$class){
		// find all grades for this class
		$query = mysql_query("SELECT * FROM `grades` WHERE `cid`='" . mysql_real_escape_string($class) . "' AND `uid`='" . mysql_real_escape_string($uid) . "'");

		if(mysql_num_rows($query) != 0){
			// initialize grade variable
			$gradeSum = 0;
			$totalGrades = 0;
			$finalGrade = 0;
			while($fetch = mysql_fetch_array($query)){
				// use is not exempt
				if($fetch['exempt'] == 0){
					// how many times is it weighted?
					$sum = $fetch['grade'] * $fetch['weight'];

					$gradeSum = $gradeSum + $sum;
					$totalGrades = $totalGrades + $fetch['weight'];
				}
			}

			// calculate the final average
			$finalGrade = $gradeSum / $totalGrades;
		}
		else{
			return "no data";
		}
			return $finalGrade;
	}
	function addNotification($from,$to,$string){
		// Lets make sure the User has permissions set to recieve this notification
		
		$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
		$messg = $string;
		$from = mysql_real_escape_string($from);
		$to = mysql_real_escape_string($to);
		$string = mysql_real_escape_string($string);
		// add notification
		mysql_query("INSERT INTO `notifications` (`tid`,`uid`,`string`,`timestamp`) VALUES ('" . $from . "', '" . $to . "', '" . $string . "','" . time() . "'");

		// email notification
			$subject = $messg;
			$email = $this->fetchProfileInfo($to,"email");
$body = '
<html>
<head>
  <title>You\'ve received a notification!</title>
  </head>
<body>
	<div style="padding-right:15px;padding-left:15px;margin-right:auto;margin-left:auto;">
	  <div style="margin-bottom:20px;border-color:#337ab7;border:1px solid #337ab7;border-radius:4px;-webkit-box-shadow:0 1px 1px rgba(0,0,0,.05);box-shadow:0 1px 1px rgba(0,0,0,.05);">
	  	<div style="padding:10px 15px;border-bottom:1px solid transparent;border-top-left-radius:3px;border-top-right-radius:3px;color:#FFF;background-color:#337ab7;border-color:#337ab7;">
	  		<h3>Aristocrat LMS&nbsp;|&nbsp;Notification
	  	</div>
	  	<div style="padding:15px;">
	  		<h3>Hi ' . $this->fetchProfileInfo($to,"firstName") . '!</h3>
	  		<p>You\'ve received a notification!</p>
			<blockquote style="display: block;-webkit-margin-before: 1em;-webkit-margin-after: 1em;-webkit-margin-start: 40px;-webkit-margin-end: 40px;padding: 10px 20px;margin: 0 0 20px;font-size: 17.5px;border-left: 5px solid #eee;">
			  <p>' . $messg . '</p>
			</blockquote>
			<p><a href="' . $subdomain . '.aristocratlms.com/login.php">Click here to check this out!</a></p>
	  	</div>
	  	<div style="padding:10px 15px;background-color:#f5f5f5;border-top:1px solid #ddd;border-bottom-right-radius:3px;border-bottom-left-radius:3px;border-bottom-color:#bce8f1;">
	  		<center>
				This email was sent on ' . date("F j, Y",time()) . '.<br />
				You are recieving this email because you have opted in for email updates per your account settings. If you would like to opt out you can do so here: <a href="http://' . $subdomain . '.aristocratlms.com/settings/">Unsubscribe</a>. You can also login to your account and go to the settings page and uncheck the email notifications check boxes.
			</center>
	  	</div>
	  </div>
	</div>
</body>
</html>
';
			$headers = "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			$headers .= "From: Aristocrat LMS <notifications@aristocratlms.com>\r\n";
			$headers .= "Reply-To: Aristocrat LMS <notifications@aristocratlms.com>\r\n";

			$success = mail($email, $subject, $body, $headers);
			
			if($success){

			}else{
				
			}
	}
}
?>