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
?>
<div class="container">
	<div class="panel panel-default">
		<div class="panel-heading">
			<span class="pull-right" style="margin-top:-8px;">
				<select class="form-control" onchange="changeSemester();" id="semChooser">
					<?php
						// Grab current semester
						$semie = mysql_query("SELECT * FROM `semester` WHERE `startTime` < '" . time() . "' AND `endTime` > '" . time() . "' AND `school`='" . mysql_real_escape_string($user->getUserInfo("school")) . "'");
						$femie = mysql_fetch_array($semie);
						$semester = $femie['id'];
						echo("<option value=\"" . $semester . "\">" . $femie['name'] . "</option>");

						// Now lets fetch the other semester and put it in there aswell.
						$getSems = mysql_query("SELECT * FROM `semester` WHERE `school`='" . mysql_real_escape_string($user->getUserInfo("school")) . "'");
						while($fSemers = mysql_fetch_array($getSems)){
							$startTime = $fSemers['startTime'];
							$endTime = $fSemers['endTime'];
							if($fSemers['id'] != $semester){
								echo("<option value=\"" . $fSemers['id'] . "\">" . $fSemers['name'] . "</option>");
							}
						}
					?>
				</select>
				<!--
	                <select onchange="changeSemester();" id="semChooser" class="form-control" id="changeSemester">
	                </select>\
							-->
	    </span>
			<h2 class="panel-title">Grades</h2>
		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-4">
					<select class="form-control" id="subSemesters" onchange="changeSubSemester();">
					</select>
					<hr>
					<table class="table table-striped">
						<tbody id="classes">
						</tbody>
					</table>
				</div>
				<div class="col-md-8" id="cAssignments">
				</div>
			</div>
   	</div>
</div>
<?php
include "core/footer.php";
?>
	<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>-->
	<script>
	/*
	function showGrades(classID){
		if($("#grades-" + classID + "").attr("style") != "display: none;"){
			$("#grades-" + classID + "").hide();
		}
		else{
			$("[id^='grades-'").hide();
			$("#grades-" + classID + "").show();
		}

	}
	$("#graderzo").load("./getGrades.php");
	function changeSemester(){
		var semester = $("#semChooser").val();
		$("#graderzo").html("");
		$("#graderzo").load("./getGrades.php?semester=" + semester + "");
	}
	*/
	function changeSemester(){
		var semester = $("#semChooser").val();
		$.get( "./getGrades.php?ss=" + semester + "", function( data) {
			$("#subSemesters").html(data);
			var subsemm = $("#subSemesters").val();
			$("#classes").load("./getGrades.php?classes=" + subsemm + "");
		});
	}
	function changeSubSemester(){
		var subsemm = $("#subSemesters").val();
		$("#classes").load("./getGrades.php?classes=" + subsemm + "");
	}
	function loadClass(classID){
		$.get( "./getGrades.php?cid=" + classID + "", function( data ) {
			$("#cAssignments").html(data);
		});
	}
	$( document ).ready(changeSemester);
	</script>
</body>
<?php
include "core/modal.php";
?>
</html>
