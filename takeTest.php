<?php
session_start();
// if there is no assignment id, get out
if(!$_GET['id']){
	header("location: 404.html");
	exit;
}
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
$id = $_GET['id'];
include "core/sqlconnect.php";
include "core/class.user.php";
$user = new user();
$class2 = $user->fetchAssinClass($id, "class");
// is user in class?
$user->inClass($class2);

// does the test exist?
$lookupTest = mysql_query("SELECT * FROM `tests` WHERE `aid`='" . mysql_real_escape_string($id) . "'");
if(mysql_num_rows($lookupTest) == 0){
	header("Location: 404.html");
	exit;
}
$fetchTest = mysql_fetch_array($lookupTest);
if($fetchTest['questions'] == ""){
	header("Location: assignment.php?id=" . $id . "&error=noquestions");
	exit;
}
$classID = $fetchTest['cid'];
$assignmentID = $id;
$timeCreated = $fetchTest['timeCreated'];

// has this user taken this test yet?
$queryTime = mysql_query("SELECT * FROM `testinfo` WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `cid`='" . mysql_real_escape_string($classID) . "' AND `aid`='" . mysql_real_escape_string($assignmentID) . "'");
$fetchTime = mysql_fetch_array($queryTime);
// if he has
if(mysql_num_rows($queryTime) != 0){
	// first off, we need to know how many attempts hes done, and howmany he has.
	$attemptNumber = mysql_num_rows($queryTime);
	$attemptInfo = mysql_query("SELECT * FROM `testinfo` WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `cid`='" . mysql_real_escape_string($classID) . "' AND `aid`='" . mysql_real_escape_string($assignmentID) . "' and `attempt`='" . mysql_real_escape_string($attemptNumber). "'");
	$fetchAttemptInfo = mysql_fetch_array($attemptInfo);
	
	// lets make sure he hasnt done all of his attempts.
	if($attemptNumber != $fetchTest['attempts']){
		// if he hasnt started yet
		if($fetchAttemptInfo['timeStarted'] == "" || $fetchAttemptInfo['timeStarted'] == "0"){
			// lets make him start
			mysql_query("UPDATE `testinfo` SET `timeStarted`='" . time() . "' WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `cid`='" . mysql_real_escape_string($classID) . "' AND `aid`='" . mysql_real_escape_string($assignmentID) . "' and `attempt`='" . mysql_real_escape_string($attemptNumber). "'");
			$_SESSION['timer'] = (time() + $fetchTest['timeAllowed']);
		}
		else{
			// if he has started... lets make sure he's got time
			if(time() > ($fetchTime['timeStarted'] + $fetchTest['timeAllowed'])){
				// he doesnt.. great, lets add another attempt in!
				$newAttemptNumber = $attemptNumber + 1;
				
				mysql_query("INSERT INTO `testinfo` (`uid`,`aid`,`cid`,`attempt`,`timeStarted`) VALUES('" . mysql_real_escape_string($user->getUserInfo("id")) . "', '" . mysql_real_escape_string($assignmentID) . "','" . mysql_real_escape_string($classID) . "','" . mysql_real_escape_string($newAttemptNumber) . "','" . time() . "')");
				$_SESSION['timer'] = (time() + $fetchTest['timeAllowed']);
				$attemptNumber = $newAttemptNumber;
			}
			else{
				// he's good, has time and can continue.
				$_SESSION['timer'] = ($fetchTime['timeStarted'] + $fetchTest['timeAllowed']);
				$answersExist = true;

			}
			
		}
	}
	else{
		// if he's on that last attempt, let's see if he has time left. if not then gtfo
		$lastAttempt = mysql_query("SELECT * FROM `testinfo` WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `cid`='" . mysql_real_escape_string($classID) . "' AND `aid`='" . mysql_real_escape_string($assignmentID) . "' and `attempt`='" . mysql_real_escape_string($fetchTest['attempts']). "'");
		$fetchLastAttempt = mysql_fetch_array($lastAttempt);
		
		if($fetchLastAttempt['timeStarted'] == "" || $fetchLastAttempt['timeStarted'] == "0"){
			// He's good, let's get him started.
			mysql_query("UPDATE `testinfo` SET `timeStarted`='" . time() . "' WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `cid`='" . mysql_real_escape_string($classID) . "' AND `aid`='" . mysql_real_escape_string($assignmentID) . "' and `attempt`='" . mysql_real_escape_string($fetchTest['attempts']). "'")or die(mysql_error());
			$_SESSION['timer'] = (time() + $fetchTest['timeAllowed']);
		}
		elseif(time() > ($fetchTime['timeStarted'] + $fetchTest['timeAllowed'])){
			header("Location: assignment.php?id=" . $id . "");
			exit;
		}
		else{
			$_SESSION['timer'] = ($fetchTime['timeStarted'] + $fetchTest['timeAllowed']);
		}
		// hes got time

	}
}
else{
	// and if he hasnt taken the test lets make sure he can, and then start.
	mysql_query("INSERT INTO `testinfo` (`uid`,`aid`,`cid`,`attempt`,`timeStarted`) VALUES('" . mysql_real_escape_string($user->getUserInfo("id")) . "', '" . mysql_real_escape_string($assignmentID) . "','" . mysql_real_escape_string($classID) . "','1','" . time() . "')");
	$_SESSION['timer'] = (time() + $fetchTest['timeAllowed']);
	$attemptNumber = "1";
	
}
?>
<!doctype html>
<html>
<head>
<title>Student Portal</title>
<base href="http://<?php print $subdomain; ?>.aristocratlms.com/">
<link rel="stylesheet" type="text/css" href="style/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="style/global.css" />
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
<div class="well text-center">
    <div id="countdown"><h1><?php print $_SESSION['timer']; ?></h1></div>
    <a href="assignment/<?php print $id; ?>" class="btn btn-success"><span class="glyphicon glyphicon-ok"></span> Finish Test</a>
</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title">Take Test</h2>
		</div>
		<div class="panel-body">

			<div class="row">

				<div class="col-md-6">

				<?php
					$attemptInfo1 = mysql_query("SELECT * FROM `testinfo` WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `cid`='" . mysql_real_escape_string($classID) . "' AND `aid`='" . mysql_real_escape_string($assignmentID) . "' and `attempt`='" . mysql_real_escape_string($attemptNumber). "'");
					$fetchAttemptInfo1 = mysql_fetch_array($attemptInfo1);


					$json_data = json_decode($fetchTest['questions'], true);
					$json_answers = json_decode($fetchTest['answers'], true);
					$json_retries = $fetchTest['retries'];
					$userAnswers = json_decode($fetchAttemptInfo1['answers'],true);
					$userAnswerAttempt = json_decode($fetchAttemptInfo1['attempts'],true);
					$correctAnswers = json_decode($fetchTest['correctAnswers'], true);
					$count = 1;

					foreach($json_data as $number=>$question){
						if($userAnswers["" . $number . ""] != ""){
							if($userAnswers["" . $number . ""] == $correctAnswers["" . $number . ""]){
								$rightone = "has-success";
								$righttwo = "disabled=\"disabled\"";
								$rightthree = "<div class=\"alert alert-success\">This answer has been answered correctly!</div>";
								$rightfour = $userAnswers["" . $number . ""];
							}
							else{
								if($userAnswerAttempt["" . $number . ""] != $json_retries){

								}
								else{
									$rightone = "has-error";
									$righttwo = "disabled=\"disabled\"";
									$rightthree = "<div class=\"alert alert-danger\">This answer has been answered wrong and has reached max attempts.</div>";
									$rightfour = $userAnswers["" . $number . ""];
								}
							}
						}
						echo("<div id=\"question_".$number."\" class=\"" . $rightone . "\">");
						echo("<h2>".$count.". " . $question . "</h2>");
								echo("<div class=\"form-group\">");
								echo("<form method=\"POST\" id=\"".$number."\">");
								if(is_array($json_answers[$number])){
									echo("<div class=\"well\">");									
									echo("<input type=\"hidden\" name=\"assignment\" value=\"".$assignmentID."\">");
									echo("<input type=\"hidden\" name=\"class\" value=\"".$classID."\">");
									echo("<input type=\"hidden\" name=\"question\" value=\"".$number."\">");
										foreach($json_answers[$number] as $answer){
											echo("<div class=\"input-group\" id=\"input".$number."\">");
												echo("<span class=\"input-group-addon\">");
												if($rightone && $answer == $userAnswers["" . $number . ""]){
													echo("<input type=\"radio\" id=\"q".$number."answer-".$number2."\" checked class=\"".$rightone."\" name=\"questionAnswer[".$number."]\" value=\"".$answer."\" style=\"cursor:pointer;\" ".$righttwo.">");
												}
												else{														
													echo("<input type=\"radio\" id=\"q".$number."answer-".$number2."\" class=\"".$rightone."\" name=\"questionAnswer[".$number."]\" value=\"".$answer."\" style=\"cursor:pointer;\" ".$righttwo.">");
												}
												echo("</span>");
												echo("<label for=\"q".$number."answer-".$number2."\" class=\"form-control\" style=\"cursor:pointer;\">");
													echo($answer);
												echo("</label>");
											echo("</div>");
											echo("<br />");
											$number2++;
										}
										echo("<button onclick=\"submitAnswer(".$number.");\" id=\"btn".$number."\" class=\"btn btn-default ".$rightone."\" ".$righttwo." type=\"button\">Submit</button>");
									echo("</div>");
								}
								else{
									echo("<div class=\"input-group\" id=\"input".$number."\">");
										echo("<input type=\"text\" class=\"form-control ".$rightone."\" ".$righttwo." id=\"question-".$number."\" value=\"" . $rightfour . "\" name=\"questionAnswer[".$number."]\">");
										echo("<input type=\"hidden\" name=\"assignment\" value=\"".$assignmentID."\">");
										echo("<input type=\"hidden\" name=\"class\" value=\"".$classID."\">");
										echo("<input type=\"hidden\" name=\"question\" value=\"".$number."\">");
										echo("<span class=\"input-group-btn\">");
											echo("<button onclick=\"submitAnswer(".$number.");\" id=\"btn".$number."\" ".$righttwo." class=\"btn btn-default ".$rightone."\" type=\"button\">Submit</button>");
										echo("</span>");
									echo("</div>");
								}							
								echo("</form>");
								echo("</div>");
							echo("</form>");
							echo("<div id=\"debug".$number."\">".$rightthree."</div>");
						echo("</div>");
						echo("<hr>");
						$rightone = "";
						$righttwo = "";
						$rightthree = "";
						$rightfour = "";
						$count++;
					}
				?>
				</div>
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-body">
							<h2>Instructions:</h2>
							<p>
								Select your answer choice on the left and then click the submit button to submit the answer to that question. 
								This is a timed test remember to submit your question as soon as you know the answer to refrain from getting them wrong.
								Refreshing or leaving the page does not stop the timer. The timer will keep couting down until the alotted time given
								per test, and then it will stop and you will not be able to retake the test unless the teacher has specified more attempts.
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
include "core/footer.php";
?>
<script>
var TimeLimit = new Date('<?php echo date('r', $_SESSION['timer']) ?>');
function countdownto() {
  var date = Math.round((TimeLimit-new Date())/1000);
  var hours = Math.floor(date/3600);
  date = date - (hours*3600);
  var mins = Math.floor(date/60);
  date = date - (mins*60);
  var secs = date;
  if (hours<10) hours = '0'+hours;
  if (mins<10) mins = '0'+mins;
  if (secs<10) secs = '0'+secs;
  $("#countdown").html('<h1>'+hours+':'+mins+':'+secs+'</h1>');
/*
  $.ajax({

	  type: "POST",

	  url: "updateTestTime.php?aid=<?php print $id ?>"

  })
*/
  setTimeout("countdownto()",1000);
  
}

countdownto();
function submitAnswer(question){
	var a = <?php print $attemptNumber ?>;
	var posting = $.post( "submitAnswer.php?a=" + a + "", $( "#" + question ).serialize() );
	var totalQuestions = $('div[id^="question_"]').length;
	posting.done(function(data) {
		$("#debug" + question).html(data);
		var str = data;
		if(str.indexOf("error-") >= 0){
			$("#input" + question).addClass( "has-error" );
			$("#btn" + question).addClass( "btn-danger" );
		}
		if(str.indexOf("error-mc") >= 0){
		}
		if(str.indexOf("success-" + question) >= 0){
			$("#input" + question).addClass( "has-success" );
			$("#btn" + question).addClass( "btn-success" );
			$("#question-" + question).prop('disabled', 'disabled');
			$("#question-" + question).attr('disabled', 'disabled');
			$("#btn" + question).addClass( "disabled" );
		}
		if(str.indexOf("error=" + question) >= 0){
			$("#input" + question).addClass( "has-error" );
			$("#btn" + question).addClass( "btn-danger" );
			$("#question-" + question).prop('disabled', 'disabled');
			$("#question-" + question).attr('disabled', 'disabled');
			$("#btn" + question).addClass( "disabled" );
		}
		if(str.indexOf("success-mc" + question) >= 0){
			var answer = $( "input[name='questionAnswer[" + question + "]']:checked" ).val();
			$("#q" + question + "answer-" + answer).attr("checked");
			var selects = $( "input[name='questionAnswer[" + question + "]']" );
			selects.attr('disabled', 'disabled');
			$("#btn" + question).addClass( "disabled" );
		}
	});
}
</script>
</body>
</html>