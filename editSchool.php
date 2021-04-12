<?php
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
$user = new user();
if(!$user->isTeacher()){
	header("Location: 404.html");
	exit;
}
if($_GET['edit'] == "school"){
	$name = $_POST['schoolName'];
	$logo = $_POST['schoolLogo'];
	$login = $_POST['schoolLoginLogo'];

	if($name != ""){
		mysql_query("UPDATE `school` SET `name`='" . mysql_real_escape_string($name) . "' WHERE `id`='" . mysql_real_escape_string($user->getUserInfo("school")) . "'");
	}
	else{
		header("Location: editSchool?error=noname");
		exit;
	}

	mysql_query("UPDATE `school` SET `logo`='" . mysql_real_escape_string($logo) . "' WHERE `id`='" . mysql_real_escape_string($user->getUserInfo("school")) . "'");
	mysql_query("UPDATE `school` SET `login_logo`='" . mysql_real_escape_string($login) . "' WHERE `id`='" . mysql_real_escape_string($user->getUserInfo("school")) . "'");
	header("Location: editSchool#tab1");
	exit;
}
if($_GET['edit'] == "course"){
	function get_random_string($valid_chars, $length)
	{
		// start with an empty random string
		$random_string = "";

		// count the number of chars in the valid chars string so we know how many choices we have
		$num_valid_chars = strlen($valid_chars);

		// repeat the steps until we've created a string of the right length
		for ($i = 0; $i < $length; $i++)
		{
			// pick a random number from 1 up to the number of valid chars
			$random_pick = mt_rand(1, $num_valid_chars);

			// take the random character out of the string of valid chars
			// subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
			$random_char = $valid_chars[$random_pick-1];

			// add the randomly-chosen char onto the end of our string so far
			$random_string .= $random_char;
		}
		// return our finished random string
		return $random_string;
	}

	$name = $_POST['courseName'];
	$period = $_POST['period'];
	$admin = $_POST['administrator'];
	$semester = $_POST['semester'];
	$ssemester = $_POST['ssemester'];
	if($name != "" && $period != "-1" && $admin != "-1" && $semester != "-1"){
		$aArray = array();
		array_push($aArray,$admin);
		$admin = json_encode($aArray,JSON_FORCE_OBJECT);

		if($semester == "0"){
			$start = "0";
			$end = "0";
		}else{
			$se = mysql_query("SELECT * FROM `semester` WHERE `id`='" .mysql_real_escape_string($semester) . "'");
			$fe = mysql_fetch_array($se);

			$start = $fe['startTime'];
			$end = $fe['endTime'];
		}

		$code = get_random_string("qwertyu-iop1234567890as-dfghjklzxcvb-nmQWERTYUIO-PASDFG-HJKLZ-XCVBNM",14);

		mysql_query("INSERT INTO `classes` (`name`,`teacher`,`period`,`school`,`startDate`,`endDate`,`semester`,`subsemesters`,`code`) VALUES ('" . mysql_real_escape_string($name) . "','" . mysql_real_escape_string($admin) . "','" . mysql_real_escape_string($period) . "','" . mysql_real_escape_string($user->getUserInfo("school")) . "','" . mysql_real_escape_string($start) . "','" . mysql_real_escape_string($end) . "','" . mysql_real_escape_string($semester) . "','" . mysql_real_escape_string($ssemester) . "','" . mysql_real_escape_string($code) . "')");

		$classID = mysql_insert_id();
		$clasz = $user->fetchProfileInfo($_POST['administrator'],"classes");
		
			$new = json_decode($clasz,true);
			$new[$semester][$ssemester][$period] = $classID;
			$newClasses = json_encode($new,JSON_FORCE_OBJECT);
			mysql_query("UPDATE `users` SET `classes`='" . mysql_real_escape_string($newClasses) . "' WHERE `id`='" . mysql_real_escape_string($user->getUserInfo("id")) . "'");
		/*
		else{
			$new = array();
			$smster = array();
			$smster[$semester] = $classID;
			$new[$period] = $smster;
			$cluss = json_encode($new,JSON_FORCE_OBJECT);
			mysql_query("UPDATE `users` SET `classes`='" . mysql_real_escape_string($cluss) . "' WHERE `id`='" . mysql_real_escape_string($_POST['administrator']) . "'") or die(mysql_error());
		}
		*/
		header("Location: editSchool#tab2");

	}else{
		header("Location: editSchool?error=course");
		exit;
	}

}
if($_GET['edit'] == "semester"){
	$name = $_POST['semesterName'];
	$start = strtotime($_POST['semesterStart']);
	$end = strtotime($_POST['semesterEnd']);

	if($name != "" && $start != "" && $end != ""){
		mysql_query("INSERT INTO `semester` (`school`,`name`,`startTime`,`endTime`) VALUES('" . mysql_real_escape_string($user->getUserInfo("school")) . "','" . mysql_real_escape_string($name) . "', '" . mysql_real_escape_string($start) . "','" . mysql_real_escape_string($end) . "')") or die(mysql_error());
	}
	else{
		header("Location: editSchool?error=semester");
		exit;
	}
	header("Location: editSchool#tab3");
	exit;
}
if($_GET['edit'] == "ssemester"){
	$name = $_POST['ssemesterName'];
	$parentSemester = $_POST['ssemesterParent'];
	$start = strtotime($_POST['ssemesterStart']);
	$end = strtotime($_POST['ssemesterEnd']);

	if($name != "" && $parentSemester != "" && $start != "" && $end != ""){
		// check to make sure within bounds of parent semmy
		$findSem = mysql_query("SELECT * FROM `semester` WHERE `id`='" . mysql_real_escape_string($parentSemester) . "'");
		$fetSem = mysql_fetch_array($findSem);

		if($start >= $fetSem['startTime'] && $end <= $fetSem['endTime']){
			mysql_query("INSERT INTO `subsemester` (`school`,`semid`,`name`,`startTime`,`endTime`) VALUES('" . mysql_real_escape_string($user->getUserInfo("school")) . "','" . mysql_real_escape_string($parentSemester) . "','" . mysql_real_escape_string($name) . "','" . mysql_real_escape_string($start) . "','" . mysql_real_escape_string($end) . "')");
		}	
		else{
			header("Location: editSchool?error=ssemester");
			exit;
		}
	}
	header("Location: editSchool#tab3");
	exit;
}
if($_GET['edit'] == "user"){
	$username = $_POST['username'];
	$firstName = $_POST['firstName'];
	$lastName = $_POST['lastName'];
	$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
	$email = $_POST['email'];
	$permissions = $_POST['permission'];
	$clsed = array();


	//lets take each class and make the final json string
	if($_POST['classes']){
		foreach($_POST['classes'] as $cid){
			// lets fetch that class info so we can put it into the proper place.
			$qCinfo = mysql_query("SELECT * FROM `classes` WHERE `id`='" . mysql_real_escape_string($cid) . "'");
			$fCinfo = mysql_fetch_array($qCinfo);

			$cSem = $fCinfo['semester'];
			$csSem = $fCinfo['subsemesters'];
			$cPeriod = $fCinfo['period'];

			$clsed[$cSem][$csSem][$cPeriod] = $cid;
		}
		// encode classes
		$classes = json_encode($clsed, JSON_FORCE_OBJECT);
	}

	// create user
	if($username != "" && $firstName != "" && $lastName != "" && $password != ""){
		//insert
		mysql_query("INSERT INTO `users` (`username`,`password`,`firstName`,`lastName`,`permissions`,`school`) VALUES ('" . mysql_real_escape_string($username) . "','" . mysql_real_escape_string($password) . "','" . mysql_real_escape_string($firstName) . "','" . mysql_real_escape_string($lastName) . "','" . mysql_real_escape_string($permissions) . "','" . mysql_real_escape_string($user->getUserInfo("school")) . "')") or die(mysql_error());
		$userID = mysql_insert_id();

		// make user file
		mkdir("./datafiles/users/" . $userID . "",0777,true);
		$fileMake = fopen("./datafiles/users/" . $userID . "/index.php","w");
		fclose($fileMake);

		// if extra values added, update the user
		if($email != ""){
			mysql_query("UPDATE `users` SET `email`='" .mysql_real_escape_string($email) . "' WHERE `id`='" . $userID . "'");
		}
		if($classes != ""){
			mysql_query("UPDATE `users` SET `classes`='" . mysql_real_escape_string($classes) . "' WHERE `id`='" . $userID . "'");
		}
		header("Location: editSchool#tab4");
		exit;
	}
	else{
		header("Location: editSchool");
		exit;
	}
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
	<div class="panel panel-default">
		<div class="panel-heading">
			School Configuration
		</div>
		<div class="panel-body">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab1" data-toggle="tab">Configuration</a></li>
                <li><a href="#tab2" data-toggle="tab">Courses</a></li>
                <li><a href="#tab3" data-toggle="tab">Semesters</a></li>
                <li><a href="#tab4" data-toggle="tab">Users</a></li>
			</ul>
			<hr>
			<div class="tab-content">
				<div class="tab-pane fade in active" id="tab1">
					<?php
						$query = mysql_query("SELECT * FROM `school` WHERE `id`='" . mysql_real_escape_string($user->getUserInfo("school")) . "'");
						$fetch = mysql_fetch_array($query);
					?>
					<form class="form-horizontal" method="POST" action="editSchool.php?edit=school">
						<div class="form-group">
						    <label for="inputPassword1" class="col-sm-2 control-label">Name&nbsp;<a style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="The name that will display at the top of the page and on the login page."><span class="glyphicon glyphicon-info-sign"></span></a></label>
						    <div class="col-sm-10">
						      <input type="text" class="form-control" value="<?php print $fetch['name'] ?>" name="schoolName">
						    </div>
						</div>
						<div class="form-group">
						    <label for="inputPassword1" class="col-sm-2 control-label">Login Page Logo&nbsp;<a style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="The image that will be displayed on the login page."><span class="glyphicon glyphicon-info-sign"></span></a></label>
						    <div class="col-sm-10">
						      <input type="text" class="form-control" value="<?php print $fetch['login_logo'] ?>" name="schoolLoginLogo">
						    </div>
						</div>
						<div class="form-group">
						    <label for="inputPassword1" class="col-sm-2 control-label">Logo&nbsp;<a style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="The image that, if chosen, will be displayed at the top instead of the name."><span class="glyphicon glyphicon-info-sign"></span></a></label>
						    <div class="col-sm-10">
						      <input type="text" class="form-control" value="<?php print $fetch['logo'] ?>" name="schoolLogo">
						    </div>
						</div>
						<div class="form-group">
						    <div class="col-sm-offset-2 col-sm-10">
						      <button type="submit" class="btn btn-primary">Update Settings</button>
						    </div>
						</div>
					</form>
				</div>
				<div class="tab-pane fade" id="tab2">
					<div class="well">
						<h2>Add Course</h2>
						<form class="form-horizontal" method="POST" action="editSchool.php?edit=course">
							<div class="form-group">
							    <label for="inputPassword1" class="col-sm-2 control-label">Name</label>
							    <div class="col-sm-10">
							      <input type="text" class="form-control" value="" name="courseName">
							    </div>
							</div>
							<div class="form-group">
							    <label for="inputPassword1" class="col-sm-2 control-label">Administrator</label>
							    <div class="col-sm-10">
							    	<select name="administrator" class="form-control">
							    		<option value="-1">Select Administrator</option>
							    		<?php
							    			$gAd = mysql_query("SELECT * FROM `users` WHERE `school`='" . mysql_real_escape_string($user->getUserInfo("school")) . "' AND `permissions`>'1'");
							    			if(mysql_num_rows($gAd) == 0){
							    				echo("<option value=\"-2\">An error has occured</option>");
							    			}
							    			else{
							    				while($fAd = mysql_fetch_array($gAd)){
							    					echo("<option value=\"" . $fAd['id'] . "\">" . $fAd['firstName'] . " " . $fAd['lastName'] . "</option>");
							    				}
							    			}
							    		?>
							      	</select>
							    </div>
							</div>
							<div class="form-group">
							    <label for="inputPassword1" class="col-sm-2 control-label">Semester</label>
							    <div class="col-sm-10">
							      	<select name="semester" class="form-control">
							      		<option value="-1">Select Semester</option>
							      		<?php
							      			$sems = mysql_query("SELECT * FROM `semester` WHERE `school`='" . mysql_real_escape_string($user->getUserInfo("school")) . "'");
							      			if(mysql_num_rows($sems) != 0){
							      				while($fsems = mysql_fetch_array($sems)){
							      					echo("<option value=\"" . $fsems['id'] . "\">");
							      						echo($fsems['name']);
							      					echo("</option>");
							      				}
							      			}
							      		?>
							     	</select>
							    </div>
							</div>
							<div class="form-group">
							    <label for="inputPassword1" class="col-sm-2 control-label">Sub Semester</label>
							    <div class="col-sm-10">
							      	<select name="ssemester" class="form-control">
							      		<option value="-1">Select Sub Semester</option>
							      		<?php
							      			$sems = mysql_query("SELECT * FROM `subsemester` WHERE `school`='" . mysql_real_escape_string($user->getUserInfo("school")) . "'");
							      			if(mysql_num_rows($sems) != 0){
							      				while($fsems = mysql_fetch_array($sems)){
							      					echo("<option value=\"" . $fsems['id'] . "\">");
							      						echo($fsems['name']);
							      					echo("</option>");
							      				}
							      			}
							      		?>
							     	</select>
							    </div>
							</div>
							<div class="form-group">
							    <label for="inputPassword1" class="col-sm-2 control-label">Period</label>
							    <div class="col-sm-10">
							    	<select name="period" class="form-control">
							    		<option value="-1">Select Period</option>
								    	<?php
								    		for($i = 1; $i < 10; $i++){
								    			echo("<option value=\"" . $i . "\">Period " . $i . "</option>");
								    		}
							      		?>
							      	</select>
							    </div>
							</div>
							<div class="form-group">
							    <div class="col-sm-offset-2 col-sm-10">
							      <button type="submit" class="btn btn-primary">Add Course</button>
							    </div>
							</div>
						</form>
					</div>
					<hr>
					<table class="table">
							<thead>
									<tr>
											<th>Name</th>
											<th>Teacher(s)</th>
											<th>Semester</th>
											<th>Sub Semester</th>
											<th>Period</th>
											<th>Join Code</th>
											<th>Delete</th>
									</tr>
							</thead>
							<tbody>
								<?php
									$gCas = mysql_query("SELECT * FROM `classes` WHERE `school`='" . mysql_real_escape_string($user->getUserInfo("school")) . "'  ORDER BY `name` ASC");
									while($mCas = mysql_fetch_array($gCas)){
										echo("<tr>");
											echo("<td>");
												echo($mCas['name']);
											echo("</td>");
											echo("<td>");
												$tch = json_decode($mCas['teacher'],true);
												if($count == 1){
													foreach($tch as $td){
														echo($user->fetchProfileInfo($td,"firstName") . " " . $user->fetchProfileInfo($td, "lastName"));
													}
												}
												else{
													$o = 1;
													foreach($tch as $td){
														if($o != count($tch)){
															echo($user->fetchProfileInfo($td,"firstName") . " " . $user->fetchProfileInfo($td, "lastName") . ", ");
														}
														else{
															echo($user->fetchProfileInfo($td,"firstName") . " " . $user->fetchProfileInfo($td, "lastName"));
														}
														$o++;
													}
												}
											echo("</td>");
											echo("<td>");
												$mi = mysql_query("SELECT * FROM `semester` WHERE `id`='" . mysql_escape_string($mCas['semester']) . "'");
												$mf = mysql_fetch_array($mi);
												echo($mf['name']);
											echo("</td>");
											echo("<td>");
												$mi = mysql_query("SELECT * FROM `subsemester` WHERE `id`='" . mysql_escape_string($mCas['subsemesters']) . "'");
												$mf = mysql_fetch_array($mi);
												echo($mf['name']);
											echo("</td>");
											echo("<td>");
												echo($mCas['period']);
											echo("</td>");
											echo("<td>");
												echo($mCas['code']);
											echo("</td>");
											echo("<td>");
												echo("<a href=\"#\"><span class=\"glyphicon glyphicon-trash\"></span></a>");
											echo("</td>");
										echo("</tr>");
									}
								?>
							</tbody>
					</table>
				</div>
				<div class="tab-pane fade" id="tab3">
					<div class="well">
						<form class="form-horizontal" method="POST" action="editSchool.php?edit=semester">
							<h2>Add Semester</h2>
							<div class="form-group">
							    <label class="col-sm-2 control-label">Name</label>
							    <div class="col-sm-10">
							      <input type="text" class="form-control" placeholder="Name" name="semesterName">
							    </div>
							</div>
							<div class="form-group">
							    <label class="col-sm-2 control-label">Start Date</label>
							    <div class="col-sm-10">
							      <input type="date" class="form-control" placeholder="mm/dd/yyyy" name="semesterStart">
							    </div>
							</div>
							<div class="form-group">
							    <label class="col-sm-2 control-label">End Date</label>
							    <div class="col-sm-10">
							      <input type="date" class="form-control" placeholder="mm/dd/yyyy" name="semesterEnd">
							    </div>
							</div>
							<div class="form-group">
							    <div class="col-sm-offset-2 col-sm-10">
							      <button type="submit" class="btn btn-primary">Add Semester</button>
							    </div>
							</div>
						</form>
					</div>
					<div class="well">
						<form class="form-horizontal" method="POST" action="editSchool.php?edit=ssemester">
							<h2>Add Sub Semester</h2>
							<div class="form-group">
							    <label class="col-sm-2 control-label">Name</label>
							    <div class="col-sm-10">
							      <input type="text" class="form-control" placeholder="Name" name="ssemesterName">
							    </div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Parent Semester</label>
								<div class="col-sm-10">
									<select class="form-control" name="ssemesterParent">
										<option value="-1">
											Select Semester
										</option>
										<?php
											$findss = mysql_query("SELECT * FROM `semester` WHERE `school`='" . mysql_real_escape_string($user->getUserInfo("school")) . "'");
											while($fetss = mysql_fetch_array($findss)){
												echo("<option value=\"" . $fetss['id'] . "\">" . $fetss['name'] . "</option>");
											}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
							    <label class="col-sm-2 control-label">Start Date</label>
							    <div class="col-sm-10">
							      <input type="date" class="form-control" placeholder="mm/dd/yyyy" name="ssemesterStart">
							    </div>
							</div>
							<div class="form-group">
							    <label class="col-sm-2 control-label">End Date</label>
							    <div class="col-sm-10">
							      <input type="date" class="form-control" placeholder="mm/dd/yyyy" name="ssemesterEnd">
							    </div>
							</div>
							<div class="form-group">
							    <div class="col-sm-offset-2 col-sm-10">
							      <button type="submit" class="btn btn-primary">Add Sub Semester</button>
							    </div>
							</div>
						</form>
					</div>
					<hr>
					<table class="table">
						<thead>
							<tr>
									<td>Name</td>
									<td>Start Date</td>
									<td>End Date</td>
									<td>Delete</td>
							</tr>
						</thead>
						<tbody>
								<?php
									$gmcs = mysql_query("SELECT * FROM `semester` WHERE `school`='" . mysql_real_escape_string($user->getUserInfo("school")) . "' ORDER BY `name` ASC");
									while($fmcs = mysql_fetch_array($gmcs)){
										$sscm = mysql_query("SELECT * FROM `subsemester` WHERE `semid`='" . mysql_real_escape_string($fmcs['id']) . "'");
										echo("<tr>");
											echo("<td>");
												echo($fmcs['name']);
											echo("</td>");
											echo("<td>");
												echo(date("F j Y, g:i a",$fmcs['startTime']));
											echo("</td>");
											echo("<td>");
												echo(date("F j Y, g:i a",$fmcs['endTime']));
											echo("</td>");
											echo("<td>");
												echo("<a href=\"#\"><span class=\"glyphicon glyphicon-trash\"></span></a>");
											echo("</td>");
										echo("</tr>");
										if(mysql_num_rows($sscm) != 0){
											while($ffms = mysql_fetch_array($sscm)){
												echo("<tr>");
													echo("<td>");
														echo("--");
														echo($ffms['name']);
													echo("</td>");
													echo("<td>");
														echo(date("F j Y, g:i a",$ffms['startTime']));
													echo("</td>");
													echo("<td>");
														echo(date("F j Y, g:i a",$ffms['endTime']));
													echo("</td>");
													echo("<td>");
														echo("<a href=\"#\"><span class=\"glyphicon glyphicon-trash\"></span></a>");
													echo("</td>");
												echo("</tr>");
											}
										}
									}
								?>
						</tbody>
					</table>
				</div>
				<div class="tab-pane fade" id="tab4">
					<div class="well">
						<form class="form-horizontal" method="POST" action="editSchool.php?edit=user">
							<h2>Create User</h2>
							<div class="form-group">
							    <label class="col-sm-2 control-label">Username</label>
							    <div class="col-sm-10">
							      <input type="text" class="form-control" name="username">
							    </div>
							</div>
							<div class="form-group">
							    <label class="col-sm-2 control-label">First Name</label>
							    <div class="col-sm-10">
							      <input type="text" class="form-control" name="firstName">
							    </div>
							</div>
							<div class="form-group">
							    <label class="col-sm-2 control-label">Last Name</label>
							    <div class="col-sm-10">
							      <input type="text" class="form-control" name="lastName">
							    </div>
							</div>
							<div class="form-group">
							    <label class="col-sm-2 control-label">Password</label>
							    <div class="col-sm-10">
							      <input type="text" class="form-control" name="password">
							    </div>
							</div>
							<div class="form-group">
							    <label for="inputPassword1" class="col-sm-2 control-label">Classes</label>
							    <div class="col-sm-10">
							      	<select multiple="true" name="classes[]" id="chooseClass" style="width:100%;" class="form-control select2">
							      		<?php
							      			$gCaes = mysql_query("SELECT * FROM `classes` WHERE `school`='" . mysql_real_escape_string($user->getUserInfo("school")) . "'");
							      			while($mFet = mysql_fetch_array($gCaes)){
							      				echo("<option value=\"" . $mFet['id'] . "\">" . $mFet['name'] . " Period " . $mFet['period'] . "</option>");
							      			}
							      		?>
							     	</select>
							    </div>
							</div>
							<div class="form-group">
							    <label for="inputPassword1" class="col-sm-2 control-label">Email</label>
							    <div class="col-sm-10">
							      <input type="text" class="form-control" name="email">
							    </div>
							</div>
							<div class="form-group">
							    <label for="inputPassword1" class="col-sm-2 control-label">Permissions</label>
							    <div class="col-sm-10">
							      <select name="permission" class="form-control">
							      		<option value="1">Student</option>
							      		<option value="2">Teacher</option>
							      </select>
							    </div>
							</div>
							<div class="form-group">
							    <div class="col-sm-offset-2 col-sm-10">
							      <button type="submit" class="btn btn-primary">Add User</button>
							    </div>
							</div>
						</form>
					</div>
					<table class="table">
							<thead>
									<tr>
											<td>Username</td>
											<td>Full Name</td>
											<td>Classes</td>
											<td>Email</td>
											<td>Permissions</td>
											<td>Delete</td>
									</tr>
							</thead>
							<tbody>
									<?php
										$use = mysql_query("SELECT * FROM `users` WHERE `school`='" .mysql_real_escape_string($user->getUserInfo("school")) . "' AND `permissions`='1' ORDER BY `lastName` ASC");
										while($fuse = mysql_fetch_array($use)){
											echo("<tr>");
												echo("<td>");
													echo($fuse['username']);
												echo("</td>");
												echo("<td>");
													echo($fuse['firstName'] . " " . $fuse['lastName']);
												echo("</td>");
												echo("<td>");
													echo("badcode...");
												echo("</td>");
												echo("<td>");
													echo($fuse['email']);
												echo("</td>");
												echo("<td>");
													echo($fuse['permissions']);
												echo("</td>");
												echo("<td>");
													echo("<a href=\"#\"><span class=\"glyphicon glyphicon-trash\"></span></a>");
												echo("</td>");
											echo("</tr>");
										}
									?>
							</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
include "core/footer.php";
?>
</div>
<!--
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script> -->
	<script>
	$(function(){
        $('#chooseClass').select2({
        });
      });
	$(document).ready(function(){
		$('[data-toggle="tooltip"]').tooltip(); 
	});
	// Javascript to enable link to tab
	var hash = document.location.hash;
	var prefix = "tab_";
	if (hash) {
	    $('.nav-tabs a[href='+hash.replace(prefix,"")+']').tab('show');
	} 

	// Change hash for page-reload
	$('.nav-tabs a').on('shown', function (e) {
	    window.location.hash = e.target.hash.replace("#", "#" + prefix);
	});
	</script>
</body>
<?php
include "core/modal.php";
?>
</html>