<?php
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
$user = new user();

if(!$user->isTeacher()){
	$query = mysql_query("SELECT * FROM `grades` WHERE `aid`='" . mysql_real_escape_string($_GET['id']) . "' AND `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "'");
		if(mysql_num_rows($query) == 0){
			header("Location: 404.html");
			exit;
		}
		else{
			$student = true;
		}
}

// declare variables
if(!$_GET['id']){
	header("Location: 404.html");
	exit;
}
$assignmentID = $_GET['id'];
$userID = $_GET['uid'];
$classID = $_GET['cid'];
if($student){
	$userID = $user->getUserInfo("id");
}

$lookupTest = mysql_query("SELECT * FROM `tests` WHERE `aid`='" . mysql_real_escape_string($assignmentID) . "'");
if(mysql_num_rows($lookupTest) != 0){
	$test = true;
	$fetchTest = mysql_fetch_array($lookupTest);
	$timeCreated = $fetchTest['timeCreated'];
}
?>
<doctype html>
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
	<div class="panel panel-default">
		<div class="panel-heading">
			<ol class="breadcrumb panel-title" style="margin:0px; padding:0px;">
               	<li>
					<a href="http://<?php print $subdomain; ?>.aristocratlms.com/course/<?php print $classID; ?>"><?php print $user->getClassInfo($classID,"name"); ?></a>
                </li>
                <li>
					<a href="http://<?php print $subdomain; ?>.aristocratlms.com/assignment/<?php print $assignmentID; ?>">
						<?php print $user->fetchAssinClass($assignmentID, "name"); ?>
					</a>
				</li>
				<li class="active">
					<?php
						echo($user->fetchProfileInfo($userID, "firstName") . " " . $user->fetchProfileInfo($userID, "lastName"));
					?>
				</li>
            </ol>
		</div>
		<div class="panel-body">
			<?php
			if(!$test){
			?>
			<?php
				$lateErNot = mysql_query("SELECT * FROM `turnin` WHERE `class`='" . mysql_real_escape_string($classID) . "' AND `aid`='" . mysql_real_escape_string($assignmentID) . "' AND `uid`='" . mysql_real_escape_string($userID)."' ORDER BY `time` DESC LIMIT 1") or die(mysql_error());
				$fetchLate = mysql_fetch_array($lateErNot);
				if($fetchLate['time'] > $user->fetchAssinClass($assignmentID,"timeDue")){
				?>
					<div class="alert alert-warning" role="alert"><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;<strong>Warning:</strong> This assignment was submitted late!</div>
				<?php
				}
				if($_GET['success'] == "true"){
					?>
						<div class="alert alert-success" role="alert"><span class="glyphicon glyphicon-star"></span>&nbsp;<strong>Success:</strong> Grade was successfully submitted!</div>
					<?php
				}
				elseif($_GET['success'] == "false"){
					?>
						<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-warning-sign"></span>&nbsp;<strong>Error:</strong> There was an error processing your request.</div>
					<?php
				}
				// check to see if assignment is graded.
				if($user->checkifGraded($userID,$classID,$assignmentID)){
					?>
						<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-ok"></span>&nbsp;This assignment has been graded.</div>
					<?php
					// if it is, lets take down some variables while we are at it.
					$gradeQuery = mysql_query("SELECT * FROM `grades` WHERE `uid`='" . mysql_real_escape_string($userID)."' AND `cid`='" . mysql_real_escape_string($classID)."' AND `aid`='".mysql_real_escape_string($assignmentID)."'");
					$gradeFetch = mysql_fetch_array($gradeQuery);
					$grade = $gradeFetch['grade'];
					$weight = $gradeFetch['weight'];
					$comment = $gradeFetch['comment'];
				}
			?>
			<?php
				$assinArrayModerate = mysql_query("SELECT * FROM `turnin` WHERE `class`='" . mysql_real_escape_string($classID) . "' AND `aid`='" . mysql_real_escape_string($assignmentID) . "' AND `uid`='" . mysql_real_escape_string($userID)."' ORDER BY `time` DESC") or die(mysql_error());
				if(mysql_num_rows($assinArrayModerate) == 0){
					?>
					<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-warning-sign"></span><strong> Attention:</strong>&nbsp; This user did not submit anything!</div>
					<?php
				}
				else{
			?>
			<h3>Submitted Files</h3>
			<div class="well">
				<?php				
				echo("<div class=\"row\">");
				while($assin = mysql_fetch_array($assinArrayModerate)){
					if($assin['name'] == "creation"){
						echo("<div class=\"col-sm-6 col-md-4\">");
							echo("<div class=\"panel panel-primary\">");
								echo("<div class=\"panel-heading\">");
									echo("Text entry on " . date("F j Y, g:i a",$assin['time']) . "");
								echo("</div>");
								echo("<div class=\"panel-body\">");
									echo($assin['href']);
									// echo("<iframe style=\"border: 1px solid #DDD;padding:5px;\" id=\"d" . $assin['id'] . "\">");
									// echo("</iframe>");
								echo("</div>");
							echo("</div>");
						echo("</div>");
						// echo("<script>document.getElementById(\"d" . $assin['id'] . "\").contentWindow.document.write('<link rel=\"stylesheet\" type=\"text/css\" href=\"style/bootstrap.min.css\" /><link rel=\"stylesheet\" type=\"text/css\" href=\"style/global.css\" /><div class=\"container\">" . $assin['href'] . "</div>');</script>");
					}
					else{
						if(strlen($assin['name']) > 20){
							$stringCut = substr($assin['name'], 0, 20);
							$string = $stringCut . "...";
						}
						else{
							$string = $assin['name'];
						}
							if($counter == 0){
								$activeClass = true;
							}
							echo("<div class=\"col-sm-6 col-md-4\">");
								echo("<div class=\"thumbnail\">");
								if(getimagesize($assin['href'])){
									echo("<img class=\"img-responsive\" style=\"max-height: 200px;\" src=\"".$assin['href']."\" alt=\"".$assin['name']."\">");
								}
								else{
									echo("<img class=\"img-responsive\" style=\"max-height: 200px;\" src=\"http://images.freeimages.com/images/previews/d33/folder-1236642.jpg\" alt=\"".$assin['name']."\">");
								}
	      								echo("<div class=\"caption\">");
	      									echo("<center>");
	      										echo("<h3>" . $string . "</h3>");
	      										echo("<p><small>" . date("F j Y, g:i a", $assin['time']) . "</small></p>");
	      										echo("<p><a href=\"".$assin['href']."\" class=\"btn btn-primary\" role=\"button\" download>Download</a></p>");
	      									echo("</center>");
	      								echo("</div>");
								echo("</div>");
							echo("</div>");
							$counter++;
						}
						
				}
				echo("</div>");
				?>
			</div>
			<?php
				}
				// end the regular assignment grading
			}
			else{
				// if its a test.
				// let's see if the user has a test.

				// show test results for selected attempt
				echo("<div class=\"row\">");
					echo("<div class=\"col-md-6\">");
						echo("<div class=\"well\">");
						$findTest = mysql_query("SELECT * FROM `testinfo` WHERE `cid`='" . mysql_real_escape_string($classID) . "' AND `uid`='" . mysql_real_escape_string($userID) . "' AND `aid`='" . mysql_real_escape_string($assignmentID) . "'");
							echo("<span class=\"pull-right\">");
								echo("<select id=\"pickAttempt\" onchange=\"changeAttempt();\"class=\"form-control\">");
								$attempts = array();
									while($fetch = mysql_fetch_array($findTest)){
										array_push($attempts, $fetch['id']);
										echo("<option value=\"" . $fetch['attempt'] . "\">Attempt " . $fetch['attempt'] . "</option>");
									}
								echo("</select>");
							echo("</span>");
							foreach($attempts as $attempt){
							$query = mysql_query("SELECT * FROM `testinfo` WHERE `id`='" . mysql_real_escape_string($attempt) . "'");
							$fetchQ = mysql_fetch_array($query);

							$gatherGrade = mysql_query("SELECT * FROM `grades` WHERE `uid`='" . mysql_real_escape_string($userID) . "' AND `aid`='" . mysql_real_escape_string($assignmentID) . "'");
							$fetchGrade = mysql_fetch_array($gatherGrade);
							$grade = $fetchGrade['grade'];
							$weight = $fetchGrade['weight'];
							$comment = $fetchGrade['comment'];
							if($fetchQ['attempt'] != 1){
								$hidden = "style=\"display:none;\"";
							}
								echo("<div " . $hidden . " id=\"attempt-" . $fetchQ['attempt']. "\" class=\"divAtt\">");
									echo("<h1>Attempt " . $fetchQ['attempt'] . ": " . $fetchQ['grade'] . "%");

									$questions = json_decode($fetchTest['questions'],true);
									$correctAnswers = json_decode($fetchTest['correctAnswers'],true);
									$answerChoices = json_decode($fetchTest['answers'],true);
									$overwrites = json_decode($fetchQ['overWrite'], true);

									$uAnswers = json_decode($fetchQ['answers'],true);
									$attemptsPerQ = json_decode($fetchQ['attempts'],true);
									$count = 1;
									foreach($questions as $question){
										echo("<h3>Question: " . $question . "</h3>");
										if($uAnswers[$count] == $correctAnswers[$count]){
											print $uAnswers[$count];
											print $correctAnswers[$count];
											echo("<h4>Answer:</h4><input type=\"text\" class=\"has-success form-control\" value=\"" . $uAnswers[$count] . "\" disabled>");
											echo("<br />");
											echo("<span class=\"btn btn-success\">This question is correct.</span>");
										}
										else{
											print $answerChoices[$count];
											if(!is_array($answerChoices[$count])){
												echo("<h4>Answer:</h4><input type=\"text\" class=\"has-danger form-control\" value=\"" . $uAnswers[$count] . "\" disabled>");	
												echo("<br />");
												if($overwrites[$count] != "" || $overwrites[$count] == "0"){
													if($overwrites[$count] == "1"){
														echo("<span class=\"btn btn-success\" id=\"leReview-" . $count . "\">Manually marked correct.</span>");
													}
													else if($overwrites[$count] == "0"){
														echo("<span class=\"btn btn-danger\" id=\"leReview-" . $count . "\">Manually marked wrong.</span>");
													}
												}
												else{
													echo("<span class=\"btn btn-warning\" id=\"leReview-" . $count . "\">This question needs reviewed</span>");
												}
												if(!$student){
													echo("<span class=\"pull-right\">");
														echo("<button onclick=\"overwrite(".$count.",".$fetchQ['attempt'].",'a');\" class=\"btn btn-success\" type=\"button\"><span class=\"glyphicon glyphicon-ok\"></span></button>&nbsp;<button onclick=\"overwrite(".$count.",".$fetchQ['attempt'].",'b');\" class=\"btn btn-danger\" type=\"button\"><span class=\"glyphicon glyphicon-remove\"></span></button>");
													echo("</span>");
												}
											}
											else{
												echo("<h4>Answer:</h4><input type=\"text\" class=\"has-danger form-control\" value=\"" . $uAnswers[$count] . "\" disabled>");
												echo("<br />");
												echo("<span class=\"btn btn-danger\">This question is incorrect.</span>");
											}
										}
										
										echo("<br/>");
										echo("<hr>");
										$count++;
									}

								echo("</div>");
						}
						echo("</div>");
					echo("</div>");
					echo("<div class=\"col-md-6\">");
						echo("<div class=\"panel panel-default\">");
							echo("<div class=\"panel-body\">");
								echo("<h3>Testing Statistics</h3>");
								echo("");
							echo("</div>");
						echo("</div>");
					echo("</div>");
				echo("</div>");

			}
			?>
			<?php
				if(!$student){
			?>
			<hr>
			<h3>Grading Options</h3>
			<div class="well">
			<form class="form-horizontal" action="gradeAssignment.php" method="POST">
			 <div class="form-group">
			    <label for="gradeField" class="col-sm-2 control-label">Grade</label>
			    <div class="col-sm-2">
			      <input type="number" class="form-control" id="gradeField" name="grade" value="<?php if($grade || $grade == "0"){ print $grade; } ?>">
			    </div>
			  </div>
			  <div class="form-group">
			    <label for="weightField" class="col-sm-2 control-label">Weight</label>
			    <div class="col-sm-2">
			      <input type="number" class="form-control" id="weightField" name="weight" value="<?php if($weight){ print $weight; } ?>">
			    </div>
			  </div>
			  <div class="form-group">
			    <label for="commentField" class="col-sm-2 control-label">Comment</label>
			    <div class="col-sm-5">
			      <textarea class="form-control" id="commentField" name="comment"><?php if($comment){ print $comment; } ?></textarea>
			    </div>
			  </div>
			  <div class="form-group">
			    <div class="col-sm-offset-2 col-sm-5">
			      <button type="submit" class="btn btn-default"><?php if($grade){ print "Update Grade"; } else { print "Submit Grade"; } ?></button>
			    </div>
			  </div>
			  <input type="hidden" value="<?php print $assignmentID ?>" name="aid">
			  <input type="hidden" value="<?php print $userID ?>" name="uid">
			  <input type="hidden" value="<?php print $classID ?>" name="cid">
			  <?php
			  	}elseif($student){
			  ?>
			  <hr>
			  <h3>Grading Results</h3>
			  <div class="well">
			  	<b>Grade:&nbsp;</b><?php if($grade || $grade == "0"){ print $grade; } ?><br />
			  	<b>Weight:&nbsp;</b><?php if($weight){ print $weight; } ?><br />
			  	<b>Comment:&nbsp;</b><?php if($comment){ print $comment; } ?><br />
			  </div>
			  <?php
			  	}
			  ?>
			</form>
			</div>
		</div>
	</div>
</div>
<?php
include "core/footer.php";
?>
	<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>-->
	<?php if(!$student){ ?>
	<script>
		function overwrite(question,attempt,bool){
			var assignment = <?php print $assignmentID ?>;
			var classs = <?php print $classID ?>;
			var userID = <?php print $userID ?>;
			$.get('./overWrite.php?aid=' + assignment + '&cid=' + classs + '&uid=' + userID + '&q=' + question + '&attempt=' + attempt + '&bool=' + bool + '',function(data){
				$("#attempt-" + attempt + " #leReview-" + question).html(data);
			});
		}
	</script>
	<?php 
	}
	if($test){
	?>
	<script>
		function changeAttempt(){
			var attempt = $("#pickAttempt").val()
			$(".divAtt").hide();
			$("#attempt-" + attempt).show();

		}
	</script>
	<?php } ?>
</body>

</html>