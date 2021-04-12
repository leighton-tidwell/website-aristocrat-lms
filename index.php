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
$classes = $user->getUserInfo("classes");
if($classes == ""){
	$classes = null;
}
$classes = json_decode($classes,true);
$cycles = array();
$i = 0;
if($user->getUserInfo("classes") == ""){
	?>
		<div class="panel panel-default" style="margin-left: 15px; margin-right: 15px;">
				<div class="panel-heading">
						<h2 class="panel-title">You are not enrolled</h2>
				</div>
				<div class="panel-body">
						<div class="alert alert-danger">You do not have any classes enrolled for this semester. To enroll in a class, go <a onclick="$('#addCourse').modal();" href="#">here</a> and type in your class enroll code.</div>
				</div>
		</div>
	<?php
}
if($classes != null){
	// check if user has classes
	// sort them
	$semie = mysql_query("SELECT * FROM `semester` WHERE `startTime` < '" . time() . "' AND `endTime` > '" . time() . "' AND `school`='" . mysql_real_escape_string($user->getUserInfo("school")) . "'");
	$femie = mysql_fetch_array($semie);
	$semester = $femie['id'];
	// get current primary semester

	//now lets get current subsemester
	$subsemie = mysql_query("SELECT * FROM `subsemester` WHERE `startTime` < '" . time() . "' AND `endTime` > '" . time() . "' AND `semid`='" . mysql_real_escape_string($semester) . "'");
	$subfemie = mysql_fetch_array($subsemie);
	$subsemester = $subfemie['id'];
if($classes[$semester][$subsemester] == null){
	echo("<div class=\"container\"><div class=\"alert alert-info\">You are not enrolled in a class.</div></div>");
}
else{
	//now lets get the periods and classes from that
ksort($classes[$semester][$subsemester]);
foreach($classes[$semester][$subsemester] as $period=>$class){
	/*
	if($class[0] != ""){
		$class[$semester] = $class[0];
	}
	*/
	if($class != ""){
	$i = $period;
	$query2 = mysql_query("SELECT * FROM `classes` WHERE `id`='".$class."'");
	$fetch2 = mysql_fetch_array($query2);
?>
<div class="panel panel-default" style="margin-left: 15px; margin-right: 15px">
	<div class="panel-heading">
		<h2 class="panel-title"><a href="http://<?php print $subdomain; ?>.aristocratlms.com/course/<?php print $fetch2['id']; ?>"><?php print $fetch2['name'];?></a> <small>
		<?php
		if($i == "1"){
			$h = "First";
		}
		else if($i == "2"){
			$h = "Second";
		}
		else if($i == "3"){
			$h = "Third";
		}
		else if($i == "4"){
			$h = "Fourth";
		}
		else if($i == "5"){
			$h = "Fifth";
		}
		else if($i == "6"){
			$h = "Sixth";
		}
		else if($i == "7"){
			$h = "Seventh";
		}
		else if($i == "0"){
			$h = false;
		}
		if($h){
			print $h;
		?> Period
		<?php
		}
		?>
		</small></h2>
		<span class="glyphicon glyphicon-chevron-down pull-right customChevron" data-toggle="collapse" data-target="#collapseClass<?php print $fetch2['id']; ?>" aria-expanded="false" aria-controls="collapseExample"></span>
	</div>
	<div class="panel-body collapse in" id="collapseClass<?php print $fetch2['id']; ?>">
		<div class="row circleAlign">
			<div class="col-md-4"> 
				<?php 
					if(!$user->isTeacher()){
						$grade = $user->calcGrade($user->getUserInfo("id"), $fetch2['id']);
					}
					else{
						$grade = $user->calculateGrades($fetch2['id'],"");
					}
					if($grade == 0.0){
						$cycles[$fetch2['id']] = "0.0";
					}
					elseif($grade == 100){
						$cycles[$fetch2['id']] = "1.0";
					}else{
						$cycles[$fetch2['id']] = "0.".intval($grade); 
					}
				?>
				<div id="cycle-<?php print $fetch2['id']; ?>" class="center-block" style="margin-top: 5%; margin-bottom: 5px; ">
				        <strong></strong>
				</div>
			</div>
			<div class="col-md-8">
				<div class="alert alert-info agendar" role="alert">
				<h4><a href="http://<?php print $subdomain; ?>.aristocratlms.com/course/<?php print $fetch2['id']; ?>">Agenda</a></h4>
				<hr>
				<?php
								$query3 = mysql_query("SELECT * FROM `agendas` WHERE `day`='".$user->getDay()."' AND `year`='".$user->getYear()."' AND `month`='".$user->getMonth()."' AND `class`='".$class."'");
								$fetch3 = mysql_fetch_array($query3);
								if($fetch3['text']){
										print $fetch3['text'];
								}
								else
								{
									print "There is no agenda for today.";
								}
				 ?>
				</div>
				<div class="alert alert-warning" role="alert">
					<h4>Upcoming Assignments<small>&nbsp;<a href="assignments/<?php print $fetch2['id']; ?>">View All</a></small></h4>
					<hr>
					<dl class="dl-horizontal">
	 					<?php
                            $user->getAssignmentsIndex($fetch2['id']);
                        ?>
					</dl>
				</div>
					<?php
						$qry = mysql_query("SELECT * FROM `assignments` WHERE `class`='" . $fetch2['id'] . "' AND `timeDue` < '" . time() . "'");
						$count = 0;
						while($fth = mysql_fetch_array($qry)){
							$qrt = mysql_query("SELECT * FROM `turnin` WHERE `aid`='" . $fth['id'] . "' AND `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "'");
							if(mysql_num_rows($qrt) == 0){
								$count++;
							}
						}
						if($count != 0){
					?>
					<div class="alert alert-danger alert-dismissable" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4>Overdue<small>&nbsp;View All</small></h4>
					<hr>
					<?php
						$qry = mysql_query("SELECT * FROM `assignments` WHERE `class`='" . $fetch2['id'] . "' AND `timeDue` < '" . time() . "'");
						$cr = 0;
						while($fth = mysql_fetch_array($qry)){
							$qrt = mysql_query("SELECT * FROM `turnin` WHERE `aid`='" . $fth['id'] . "' AND `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "'");
							if(mysql_num_rows($qrt) == 0){
								if($cr != 5){
									echo("<span class=\"glyphicon glyphicon-exclamation-sign\" aria-hidden=\"true\"></span>");
									echo("<span class=\"sr-only\">Error:</span>");
									echo("&nbsp;<a href=\"assignment.php?id=" . $fth['id'] . "\">" . $fth['name'] . "</a>.");
									echo("<br />");
									$cr++;
								}
							}
						}
						if($cr == 5){
							echo("View More...");
						}
					?>
				</div>
					<?php
						}
					?>
			</div>
		</div>
	</div>
</div>
<hr>
<?} }  } }?>
<?php
include "core/footer.php";
?>
</div>
<!--
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/circle-progress.js"></script>
    <script src="js/notifications.js"></script>
-->
<script>
<?php
foreach($cycles as $key=>$value)
{
?>
$('#cycle-<?php print $key; ?>').circleProgress(
	{
		value: <?php print $value; ?>,
		fill: 
		{
			gradient: [['#0681c4', .5], ['#4ac5f8', .5]],
			gradientAngle: Math.PI / 4
		},
		canvasId: 'cycle-<?php print $key; ?>-can'
	}
).on('circle-animation-progress',
	function(event, progress, stepValue)
	{
		if(stepValue == "1.0"){
			var s = "100";
		}else{
			var d = String(stepValue.toFixed(2)).substr(1);
			var s = d.replace(".", "");
		}	
		var c = document.getElementById("cycle-<?php print $key; ?>-can");

		var context = c.getContext('2d');

		context.font="25px font-family-base";
		context.fillText("Grade:",40,90);
		context.font="80px font-family-base";
		<?php
			if($value != "0.0"){
		?>
			if(stepValue == "1.0"){
				context.fillText(s + "%",40,150);
			}else{
				context.fillText(s + "%",60,150);
			}
		<?php
			}
		else{
		?>
			context.fillText("N/A",60,150);
		<?php
		}
		?>
	}
);
<?php
}
?>
</script> 
</body>
<?php
include "core/modal.php";
?>
</html>