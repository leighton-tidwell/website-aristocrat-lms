<?php
session_start();
$id = $_GET['id'];
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
include "core/sqlconnect.php";
include "core/class.user.php";
$user = new user();
$user->inClass($id);
if(!$user->isTeacher()){
	header("Location 404.html");
	exit;
}
$qInfo = mysql_query("SELECT * FROM `classes` WHERE `id`='" . mysql_real_escape_string($_GET['id']) . "'");
$qFetch = mysql_fetch_array($qInfo);
?>
<!doctype html>
<html>

<head>
<title>Student Portal</title>
<base href="http://<?php print $subdomain; ?>.aristocratlms.com/">
<link rel="stylesheet" type="text/css" href="style/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="style/global.css" />
<link rel="stylesheet" href="style/style.css" />
<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet" />


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
				<ol class="breadcrumb panel-title" style="margin:0px; font-size:21px; padding:0px;">
                	<li>
						<a href="http://<?php print $subdomain; ?>.aristocratlms.com/course/<?php print $id ?>"><?php print $user->getClassInfo($id,"name"); ?></a>
                    </li>
                    <li class="active"><i>Edit</i></li>
                </ol>
			</div>
			<div class="panel-body">
				<ul class="nav nav-tabs nav-justified">
					<li role="presentation" class="active"><a href="#information" data-toggle="tab" >Information</a></li>
					<li role="presentation"><a href="#assignments" data-toggle="tab">Assignments</a></li>
					<li role="presentation"><a href="#students" data-toggle="tab" >Students</a></li>
				</ul>
				<hr>
				<form id="profileForm" method="post" action="http://<?php print $subdomain; ?>.aristocratlms.com/updateInformation">
				<div class="tab-content">
					<div id="information" class="tab-pane fade in active">
						<form>
						  <div class="form-group">
						    <label for="exampleInputEmail1">Class Name</label>
						    <input type="text" class="form-control" value="<?php print $qFetch['name']; ?>">
						  </div>
						  <div class="form-group">
						    <label for="enrollmentType">Enrollment Type</label>
						    <select id="enrollmentType" class="form-control" disabled>
								<option>Closed</option>
								<option>Open</option>
							</select>
						  </div>
						  <button type="submit" class="btn btn-default">Submit</button>
						</form>
			        	</div>
			        	<div id="assignments" class="tab-pane fade in">
			        		<table class="table table-striped">
							<thead>
								<tr>
									<th>Assignment</th>
                                    <th>Type</th>
									<th>Due Date</th>
                                    <th>Due Time</th>
                                    <th></th>
								</tr>
							</thead>
                            <tr>
                            	<form action="addAssignment.php" id="assignment" method="post">
                                	<input type="hidden" value="<?php print $id; ?>" name="class">
                                	<td class="col-md-5">
	                                	<input type="text" class="form-control" name="assignment">
                                    </td>
                                    <td class="col-md-2">
                                    	<select name="type" class="form-control">
                                        	<option value="1">Regular</option>
                                            <option value="2">Test</option>
                                        </select>
                                    </td>
                                    <td class="col-md-2">
                                    	<input type="date" class="form-control" placeholder="mm/dd/yyyy" name="date">
                                    </td>
                                    <td class="col-md-2">
                                    	<input type="time" class="form-control" placeholder="00:00 AM/PM" name="time">
                                    </td>
                                    <td class="col-md-1">
                                    	<button type="submit" class="btn btn-default">
                                          <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                          Add
                                        </button>
                                   </td>
                                </form>
                            </tr>
							<?php
								$user->getAssignmentsCourse($id);
							?>
						</table>
					</div>
			        	<div id="students" class="tab-pane fade in">
							<div id="debug"></div>
			        		<table class="table table-striped">
							<thead>
								<tr>
									<th></th>
									<th>Name</th>
									<th>Role</th>
								</tr>
                                <tr>
                                	<td class="col-md-1">
                                    Enroll A Student:<br />
                                    </td>
                                    <td class="col-md-9">
                                        <form>
                                            <select multiple="true" name="enroll" id="choose_usr_email" style="width:100%;" class="form-control select2">				
                                                <?php
													$school = $user->getUserInfo("school");
													$query = mysql_query("SELECT * FROM `users` WHERE `school`='".mysql_real_escape_string($school)."' ORDER BY `lastName` ASC");
													while(($fetch = mysql_fetch_array($query)) != NULL){
														$newID = "\"" . $id . "\"";
														$carryup = strpos($fetch['classes'], $newID);
														if($carryup == false){
															if($fetch['email'] == ""){
																$fetch['email'] = "No Email";
															}
															echo("<option value=\"" . $fetch['id'] . "\">" . $fetch['firstName']. " " . $fetch['lastName'] . " <" . $fetch['email'] . "></option>");
														}
													}
												?>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="col-md-2">
                                    	<button type="button" onclick="addStudent();" class="btn btn-default">
                                              <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                              Add
                                        </button>
                                    </td>
                                </tr>
								<?php
									foreach($user->getUsers() as $userID){
										$test = false;
										if($user->fetchProfileInfo($userID,"school") == $user->getUserInfo("school")){
											if($user->fetchProfileInfo($userID,"permissions") != "2"){
												$classes = $user->fetchProfileInfo($userID, "classes");
												$classes = json_decode($classes,true);

												foreach($classes as $semester){
													if($semester != ""){
														foreach($semester as $subdomain){
															if($subdomain != ""){
																foreach($subdomain as $period=>$class){
																	if($class == $id){
																		$test = true;
																	}
																}
															}
														}
													}
												}
											}
										}
									

										if($test){
								?>
								
								<tr>
									<td class="col-md-1"><img width="50px" src="<?php print $user->fetchProfileInfo($userID, "profilePicture") ; ?>" /></td>
									<td class="col-md-9"><a href="http://<?php print $subdomain; ?>.aristocratlms.com/profile/<?php print $user->fetchProfileInfo($userID, "id") ?>"><?php print $user->fetchProfileInfo($userID, "firstName") . " " . $user->fetchProfileInfo($userID, "lastName"); ?></a></td>
									<td class="col-md-2"><?php print $type; ?></td>
								</tr>
								<?php
								} } 
								?>
							</thead>
						</table>
			        	</div>
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
      $(function(){
        $('#choose_usr_email').select2({
        });
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

function addStudent(){
	var htmlString = $("#choose_usr_email").val();
	if(htmlString == null || htmlString == ""){
		$("#debug").html("<div class='alert alert-danger'>You must select a student</div>");
	}else{
		$.post("addStudent.php", { enroll: htmlString, cid:'<?php print $id; ?>', sid:'<?php print $qFetch['semester'] ?>',ssid: '<?php print $qFetch['subsemesters'] ?>',pid:'<?php print $qFetch['period']; ?>' }).done(function(data){
			$("#debug").html(data);
		});
	}
}
    </script>
	
</body>
<?php
include "core/modal.php";
?>
</html>