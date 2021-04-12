<?php
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
if(!$_GET['id']){
	header("Location:404.html");
	exit;
}
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";
$user = new user();
if(!$user->isTeacher()){
	header("Location 404.html");
	exit;
}
$id = $_GET['id'];
$class = $user->fetchAssinClass($id, "class");
if($user->fetchAssinClass($id,"type") == "2"){
	$select = mysql_query("SELECT * FROM `tests` WHERE `aid`='" . mysql_real_escape_string($id) . "'");
	if(mysql_num_rows($select) == 0){
		mysql_query("INSERT INTO `tests` (`aid`,`cid`,`timeCreated`) VALUES('" . mysql_real_escape_string($id) . "','" . mysql_real_escape_string($class) . "','" . time() . "')");
		$select = mysql_query("SELECT * FROM `tests` WHERE `aid`='" . mysql_real_escape_string($id) . "'");
	}
	$fetch = mysql_fetch_array($select);
	$questions = json_decode($fetch['questions'], true);
	$answers = json_decode($fetch['answers'], true);
	$correctA = json_decode($fetch['correctAnswers'], true);
	$attempts = $fetch['attempts'];
	$retries = $fetch['retries'];
	$showOne = $fetch['showOneAtTime'];
	$timeAllowed = $fetch['timeAllowed'];
}
if($_GET['delete'] == "true"){
	mysql_query("UPDATE `assignments` SET `delete`='1' WHERE `id`='" . mysql_real_escape_string($id) . "'");
	header("Location: editCourse/" . $class . "#assignments");
	exit;
}
if($user->fetchAssinClass($id, "delete") == "1"){
	header("Location: 404.html");
	exit;
}
if($_GET['succcess'] == true){
	$alert = "<div class=\"alert alert-success\">Changes saved successfully.</div>";
}
?>
<!doctype html>
<html>

<head>
<title>Student Portal</title>
<base href="http://<?php print $subdomain; ?>.aristocratlms.com/">
<link rel="stylesheet" type="text/css" href="style/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="style/global.css" />
<link rel="stylesheet" href="style/style.css" />
<link rel="stylesheet" type="text/css" href="style/fileinput.css" type="text/css" />
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
			<button onClick="deleteAssignment();" class="glyphicon glyphicon-trash pull-right btn btn-danger" aria-hidden="true"></button>
                <ol class="breadcrumb panel-title" style="margin:0px; font-size:21px; padding:0px;">
                	<li>
						<a href="http://<?php print $subdomain; ?>.aristocratlms.com/course/<?php print $user->fetchAssinClass($id, "class"); ?>"><?php print $user->getClassInfo($user->fetchAssinClass($id,"class"),"name"); ?></a>
                    </li>
                    <li>
                    	<a href="http://<?php print $subdomain; ?>.aristocratlms.com/assignment/<?php print $id ?>"><?php print $user->fetchAssinClass($id, "name"); ?></a>
                    </li>
                    <li class="active"><i>Edit</i></li>
                </ol>
				
            </div>
	        <div class="panel-body">
            	<div class="col-md-12">
                <div class="col-md-8">
                <?PHP
					if($user->fetchAssinClass($id, "type") == 1){
				?>
                <form method="post" enctype="multipart/form-data" action="editAssignmentPost.php">
                	<input type="hidden" name="id" value="<?php print $id ?>" />
                    <div class="form-group">
                		<?php print $alert; ?>
                	</div>
	                <div class="form-group">
	                    <label for="name">Name:</label>
	                    <input class="form-control" name="name" value="<?php print $user->fetchAssinClass($id, "name"); ?>" type="text" />  
	                </div>
	                <div class="form-group">            
	                    <label for="name">Description:</label>
	                    <textarea class="form-control" name="description" value="<?php print $user->fetchAssinClass($id, "description"); ?>" type="text" /></textarea>
	                </div>
	                <div class="form-group">
	                    <label for="name">Due Date:</label>
	                    <input class="form-control" name="dueDate" placeholder="mm/dd/yyyy" value="<?php print date("m/d/Y",$user->fetchAssinClass($id, "timeDue")); ?>" type="date" /> 
	                </div>
	                <div class="form-group">
	                    <label for="name">Due Time:</label>
	                    <input class="form-control" name="dueTime" placeholder="00:00 AM/PM" value="<?php print date("g:i A",$user->fetchAssinClass($id, "timeDue")); ?>" type="time" /> 
	                </div>
	                <!--
	                <div class="form-group"> 
	                    <div class="radio-inline">
		                    <label><input type="radio" name="visible" value="1" checked/> Public</label>
	                    </div>
	                    <div class="radio-inline">
	                   		<label><input type="radio" name="visible" value="0"/> Hidden</label>
	                    </div>
	                </div>
	            -->
                    <br />
                   
                    Add Resources
                    <div data-role="dynamic-fields">
                    	<?php
							$jsonString = $user->fetchAssinClass($id, "resources");
							$jsonArray = json_decode($jsonString, true);
							$number = 0;
							if($jsonString != "null" && $jsonString){
							foreach($jsonArray as $json){
								if($number == 0){
									echo("<div class=\"form-inline\" style=\"margin-bottom:5px\">");
									echo("<div class=\"form-group\">");
										echo("<label class=\"sr-only\" for=\"field-name\">Resource Name</label>");
										echo("<input type=\"text\" class=\"form-control\" id=\"field-name\" value=\"" . $json . "\" name=\"resources[]\" />");
									echo("</div>");
									echo("<span>&nbsp;-&nbsp;</span>");
									$number = 1;
								}else{
									echo("<div class=\"form-group\">");
										echo("<label class=\"sr-only\" for=\"field-name\">Resource URL</label>");
										echo("<input type=\"text\" class=\"form-control\" id=\"field-name\" value=\"" . $json . "\" name=\"resources[]\" />");
									echo("</div>&nbsp;");
									echo("<button class=\"btn btn-danger\" data-role=\"remove\">");
										echo("<span class=\"glyphicon glyphicon-remove\"></span>");
									echo("</button>&nbsp;");
									echo("<button class=\"btn btn-primary\" data-role=\"add\">");
										echo("<span class=\"glyphicon glyphicon-plus\"></span>");
									echo("</button>");
									echo("<br />");
									echo("</div>");
									$number = 0;
								}
							}
							}
							else{
						?>
						<div class="form-inline">
                           	<div class="form-group">
                                <label class="sr-only" for="field-name">Resource Name</label>
                                <input type="text" class="form-control" id="field-name" placeholder="Resource Name" name="resources[]">
                            </div>
                            <span>-</span>
                            <div class="form-group">
                                <label class="sr-only" for="field-value">Resource URL</label>
                                <input type="text" class="form-control" id="field-value" placeholder="Resource URL" name="resources[]">
                            </div>
                            <button class="btn btn-danger" data-role="remove">
                                <span class="glyphicon glyphicon-remove"></span>
                            </button>
                            <button class="btn btn-primary" data-role="add">
                                <span class="glyphicon glyphicon-plus"></span>
                            </button>
                        </div>  <!-- /div.form-inline -->
                       		<?php
                       		}
                       		?>
                    </div>
                    <br />
                    <div class="form-group">
	                    Upload Files
	                    <label class="control-label">Select File</label>
						<input id="input-2" type="file" class="file" multiple data-show-upload="false" data-show-caption="true" name="resource[]">
					</div>
                    <div class="form-group">
                    	<div class="col-md-4">
	                    	<input class="form-control btn btn-primary" type="submit" value="Save Changes"/>
	                   	</div>
                    </div>
                </form>
                <?php
					}
					else if($user->fetchAssinClass($id, "type") == 2){
					
				?>
					<h2>Test Settings</h2>
					<span class="pull-right" style="margin-right: 10px;">
					<?php if($timeAllowed > -1 || !$timeAllowed){ ?>
						<select onchange="changeTest();" id="testChange" name="testType" class="form-control">
							<option value="2">Timed Test</option>
							<option value="3">Untimed Test</option>
						</select>
					<?php }else{ ?>
						<select onchange="changeTest();" id="testChange" name="testType" class="form-control">
							<option value="3">Untimed Test</option>
							<option value="2">Timed Test</option>
						</select>
					<?php } ?>
					</span><br />
                	<form method="post" enctype="multipart/form-data" action="editAssignmentPost.php">
					<input type="hidden" name="id" value="<?php print $id ?>" />
					<input type="hidden" name="type" value="test" />
					<?php if($timeAllowed > -1 || !$timeAllowed){ ?>
					<input type="hidden" id="iud" name="IUD" value="2" />
					<?php }else{?>
					<input type="hidden" id="iud" name="IUD" value="3" />
					<?php } ?>
						<div class="well">
							<label for="name">Name:</label>
							<input class="form-control" name="name" value="<?php print $user->fetchAssinClass($id, "name"); ?>" type="text" /><br/>
							<label for="desc">Description:</label>
							<textarea class="form-control" name="description" value="" type="text"><?php print $user->fetchAssinClass($id, "description"); ?></textarea><br/>
							<label for="attempts">Attempts:</label>
							<input class="form-control" name="attempts" value="<?php print $attempts; ?>" type="text" /><br/>
							<label for="timeAllowed" id="lableTime">Time Allowed (seconds):</label>
							<input class="form-control" name="timeAllowed" id="timeInput" value="<?php print $timeAllowed; ?>" type="text" /><br />
							<label for="timeDue">Time Due:</label>
							<input class="form-control" name="timeDue" value="<?php print date("Y-m-d",$user->fetchAssinClass($id, "timeDue")); ?>" type="date" /><br />
							<input class="form-control" name="timeDueTime" value="<?php print date("H:i:s",$user->fetchAssinClass($id, "timeDue")); ?>" type="time" /><br />
							<button class="btn btn-default" type="submit">Update</button>
						</div>
					</form>
					<hr>

					<div id="questionContainer">
					<h2>Add Question<button onClick="addQuestion();" class="btn btn-info pull-right glyphicon glyphicon-plus"></button></h2>
					<?php
						$number = 1;
						if($questions != ""){
							foreach($questions as $number=>$question){
								if(is_array($answers[$number])){
									echo("<div id=\"" . $number . "\">");
										echo("<div id=\"multipleChoice".$number."\">");
											echo("<span class=\"pull-right\" style=\"margin-right: 10px;\">");
												echo("<select id=\"select" . $number . "\" onchange=\"changeQuestion('" . $number . "');\" class=\"form-control\">");
													echo("<option value=\"2\">Multiple Choice</option>");
													echo("<option value=\"1\">Long Answer</option>");
												echo("</select>");
											echo("</span><br />");
											echo("<form id=\"form-".$number."\" method=\"post\">");
												echo("<div class=\"well\">");
													echo("<label for=\"name\">Question:</label>");
													echo("<input class=\"form-control\" id=\"questionID\" name=\"questionName\" value=\"" . $questions[$number] . "\" type=\"text\" /><br />");
													$number2 = 1;
													echo("<div id=\"answerGroup".$number."\">");
													foreach($answers[$number] as $answer){
															if($answer == $correctA["" . $number . ""]){
															echo("<div class=\"input-group\" id=\"input".$number."\">");
																echo("<span class=\"input-group-addon\">");
																	echo("<input type=\"radio\" id=\"q".$number."answer-".$number2."\" checked name=\"correctAnswer\" value=\"".$number2."\" style=\"cursor:pointer;\">");
																echo("</span>");
																echo("<input type=\"text\" name=\"questionAnswer[]\" value=\"" . $answer . "\" class=\"form-control\">");
															echo("</div>");
															$number2++;
															}
															else{
															echo("<div class=\"input-group\" id=\"input".$number."\">");
																echo("<span class=\"input-group-addon\">");
																	echo("<input type=\"radio\" id=\"q".$number."answer-".$number2."\" name=\"correctAnswer\" value=\"".$number2."\" style=\"cursor:pointer;\">");
																echo("</span>");
																echo("<input type=\"text\" name=\"questionAnswer[]\" value=\"" . $answer . "\" class=\"form-control\">");
															echo("</div>");	
															$number2++;
															}
															
														
														}
														echo("</div>");
													echo("<br />");
												echo("<button onclick=\"submitForm('cache',".$number.");\" class=\"btn btn-default\" type=\"button\">Update</button>&nbsp;<button onclick=\"removeQuestion('cache',".$number.");\" class=\"btn btn-danger\" type=\"button\">Delete</button><button type=\"button\" onClick=\"addAnswerChoice(".$number.");\" class=\"btn btn-success pull-right glyphicon glyphicon-plus\"></button>&nbsp;<button type=\"button\" onClick=\"removeAnswerChoice(".$number.");\" class=\"btn btn-danger pull-right glyphicon glyphicon-minus\"></button>");
											echo("</form>");
										echo("</div>");
										echo("</div>");
									echo("</div>");
									echo("<div id=\"cache-". $number."\" style=\"display:none;\">");
										echo("<div id=\"" . $number . "\">");
											echo("<div id=\"longAnswer".$number."\">");
												echo("<span class=\"pull-right\" style=\"margin-right: 10px;\">");
													echo("<select id=\"select" . $number . "\" onchange=\"changeQuestion('" . $number . "');\" class=\"form-control\">");
														echo("<option value=\"1\">Long Answer</option>");
														echo("<option value=\"2\">Multiple Choice</option>");
													echo("</select>");
												echo("</span><br />");
												echo("<form id=\"form-cache-".$number."\" method=\"post\">");
													echo("<div class=\"well\">");
														echo("<label for=\"name\">Question:</label>");
														echo("<input class=\"form-control\" id=\"questionID\" name=\"questionName\" value=\"" . $questions[$number] . "\" type=\"text\" /><br />");
														echo("<label for=\"desc\">Answer:</label>");
														echo("<textarea class=\"form-control\" name=\"questionAnswer\" type=\"text\" />" . $answers[$number][1] . "</textarea><br />");
														echo("<button onclick=\"submitForm('reg',".$number.");\" class=\"btn btn-default\" type=\"button\">Update</button>&nbsp;<button onclick=\"removeQuestion('reg',".$number.");\" class=\"btn btn-danger\" type=\"button\">Delete</button>");
													echo("</div>");
												echo("</form>");
											echo("</div>");
										echo("</div>");
									echo("</div>");
									echo("<div id=\"" . $number . "-debug\">");
									echo("</div>");

								}else{
									echo("<div id=\"" . $number . "\">");
										echo("<div id=\"longAnswer".$number."\">");
											echo("<span class=\"pull-right\" style=\"margin-right: 10px;\">");
												echo("<select id=\"select" . $number . "\" onchange=\"changeQuestion('" . $number . "');\" class=\"form-control\">");
													echo("<option value=\"1\">Long Answer</option>");
													echo("<option value=\"2\">Multiple Choice</option>");
												echo("</select>");
											echo("</span><br />");
											echo("<form id=\"form-".$number."\" method=\"post\">");
												echo("<div class=\"well\">");
													echo("<label for=\"name\">Question:</label>");
													echo("<input class=\"form-control\" id=\"questionID\" name=\"questionName\" value=\"" . $questions[$number] . "\" type=\"text\" /><br />");
													echo("<label for=\"desc\">Answer:</label>");
													echo("<textarea class=\"form-control\" name=\"questionAnswer\" type=\"text\" />" . $answers[$number] . "</textarea><br />");
													echo("<button onclick=\"submitForm('reg',".$number.");\" class=\"btn btn-default\" type=\"button\">Update</button>&nbsp;<button onclick=\"removeQuestion('reg',".$number.");\" class=\"btn btn-danger\" type=\"button\">Delete</button>");
												echo("</div>");
											echo("</form>");
										echo("</div>");
									echo("</div>");
									echo("<div id=\"cache-". $number."\" style=\"display:none;\">");
										echo("<div id=\"" . $number . "\">");
											echo("<div id=\"multipleChoice".$number."\">");
												echo("<span class=\"pull-right\" style=\"margin-right: 10px;\">");
													echo("<select id=\"select" . $number . "\" onchange=\"changeQuestion('" . $number . "');\" class=\"form-control\">");
														echo("<option value=\"2\">Multiple Choice</option>");
														echo("<option value=\"1\">Long Answer</option>");
													echo("</select>");
												echo("</span><br />");
												echo("<form id=\"form-cache-".$number."\" method=\"post\">");
													echo("<div class=\"well\">");
														echo("<label for=\"name\">Question:</label>");
														echo("<input class=\"form-control\" id=\"questionID\" name=\"questionName\" value=\"" . $questions[$number] . "\" type=\"text\" /><br />");
																echo("<div id=\"answerGroup".$number."\">");
																	echo("<div class=\"input-group\" id=\"input".$number."\">");
																		echo("<span class=\"input-group-addon\">");
																			echo("<input type=\"radio\" value=\"\" checked name=\"correctAnswer\" value=\"".$number2."\" style=\"cursor:pointer;\">");
																		echo("</span>");
																		echo("<input type=\"text\" name=\"questionAnswer[]\" value=\"".$answers[$number]."\" class=\"form-control\">");
																	echo("</div>");
																echo("</div>");
															echo("<br />");
														echo("<button onclick=\"submitForm('cache',".$number.");\" class=\"btn btn-default\" type=\"button\">Update</button>&nbsp;<button onclick=\"removeQuestion('cache',".$number.");\" class=\"btn btn-danger\" type=\"button\">Delete</button><button onClick=\"addAnswerChoice(".$number.");\" type=\"button\" class=\"btn btn-success pull-right glyphicon glyphicon-plus\"></button>&nbsp;<button onClick=\"removeAnswerChoice(".$number.");\" type=\"button\" class=\"btn btn-danger pull-right glyphicon glyphicon-minus\"></button>");
													echo("</div>");
												echo("</form>");
											echo("</div>");
										echo("</div>");
									echo("</div>");
									echo("<div id=\"" . $number . "-debug\">");
									echo("</div>");
								}
							$number++;
							}
						}
					?>
					<div id="default" style="display:none;">
						<div id="longAnswer">
							<span class="pull-right" style="margin-right: 10px;">
								<select id="select" class="form-control">
									<option value="1">Long Answer</option>
									<option value="2">Multiple Choice</option>
								</select>
							</span><br />
							<form method="post">
								<div class="well">
									<label for="name">Question:</label>
									<input class="form-control" name="questionName" value="" type="text" /><br />
									<label for="desc">Answer:</label>
									<textarea class="form-control" name="questionAnswer" value="" type="text" /></textarea><br />
									<button class="btn btn-default" type="button">Update</button>&nbsp;<button class="btn btn-danger" type="button">Delete</button>
								</div>
							</form>
						</div>
					</div>
					<div id="defaultMatch" style="display:none;">
						<div id="multipleChoice">
							<span class="pull-right" style="margin-right: 10px;">
								<select class="form-control">
									<option value="2">Multiple Choice</option>
									<option value="1">Long Answer</option>
								</select>
							</span><br />
							<form method="post">
								<div class="well" id="multipleChoice">
									<label for="name">Question:</label>
									<input class="form-control" name="questionName" value="" type="text" /><br />
									<label for="desc">Answers:</label>
									<div id="answerGroup">
									<div class="input-group">
										<span class="input-group-addon">
											<input type="radio" name="correctAnswer" aria-label="...">
										</span>
										<input type="text" name="questionAnswer[]" class="form-control" aria-label="...">
									</div></div><br />
									<button class="btn btn-default" type="button">Update</button>&nbsp;<button class="btn btn-danger" type="button">Delete</button><button type="button" id="changeMe" class="btn btn-success pull-right glyphicon glyphicon-plus"></button>&nbsp;<button type="button" id="changeMe2" class="btn btn-danger pull-right glyphicon glyphicon-minus"></button>
								</div>
							</form>
						</div>
					</div>
                <?php
					}
				?>
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
		function deleteAssignment(){
			if (window.confirm("Are you sure you want to delete this assignment?"))
			{
				// They clicked Yes
				var idee = <?php print $id; ?>;
				window.location = "editAssignment/" + idee + "&delete=true";
			}
			else
			{
				// They clicked no
			}
		}
	</script>
<script>
$(function() {
    // Remove button click
    $(document).on(
        'click',
        '[data-role="dynamic-fields"] > .form-inline [data-role="remove"]',
        function(e) {
            e.preventDefault();
            $(this).closest('.form-inline').remove();
        }
    );
    // Add button click
    $(document).on(
        'click',
        '[data-role="dynamic-fields"] > .form-inline [data-role="add"]',
        function(e) {
            e.preventDefault();
            var container = $(this).closest('[data-role="dynamic-fields"]');
            new_field_group = container.children().filter('.form-inline:first-child').clone();
            new_field_group.find('input').each(function(){
                $(this).val('');
            });
            container.append(new_field_group);
        }
    );
});
</script>
<script>
function addQuestion() {
	var bottomID = $("#questionContainer").children().length;
	var ID = Math.round(((bottomID - 3) / 2) + 1);
	if($("#" + ID + "").length){
		while($("#" + ID + "").length){
			var placeholder = ID;
			ID = placeholder + 1;
		}
	}
	$("<div></div>").appendTo("#questionContainer").attr("id","" + ID + "");
	$("<div></div>").appendTo("#questionContainer").attr("id","cache-" + ID + "");
	$("<div></div>").appendTo("#questionContainer").attr("id","" + ID + "-debug");
	$("#" + ID + "").append($("#default").html());
	$("#" + ID + " #longAnswer").attr("id","longAnswer" + ID + "");
	$("#" + ID + " select").attr("onchange","changeQuestion(" + ID + ");");
	$("#" + ID + " button").attr("onclick","submitForm('reg'," + ID + ");");
	$("#" + ID + " .btn-danger").attr("onclick","removeQuestion('reg'," + ID + ");");
	$("#cache-" + ID + "").find( "answerGroup").attr("id","answerGroup" + ID + "");
	console.log("finding......");
	$("#cache-" + ID + "").append($("#defaultMatch").html());
	$("#cache-" + ID + " #multipleChoice").attr("id","multipleChoice" + ID + "");
	$("#cache-" + ID + " select").attr("onchange","changeQuestion(" + ID + ");");
	$("#cache-" + ID + " .btn-default").attr("onclick","submitForm('cache'," + ID + ");");
	$("#cache-" + ID + " .btn-danger").attr("onclick","removeQuestion('cache'," + ID + ");");
	$("#cache-" + ID + " #answerGroup").attr("id","answerGroup" + ID + "");
	$("#answerGroup" + ID + " .input-group input[type=radio]").attr("value","1");
	$("#cache-" + ID + " #changeMe").attr("onclick","addAnswerChoice(" + ID + ")");
	$("#cache-" + ID + " #changeMe2").attr("onclick","removeAnswerChoice(" + ID + ")");
	$("#cache-" + ID + "").css("display","none");
}
function removeQuestion(loc,id){
	$("#" + id + "").remove();
	$("#cache-" + id + "").remove();
	$("#" + id + "-debug").remove();
	$.post("./addQuestion.php?aid=<?php print $id; ?>&qid=" + id + "&delete=true");
	
}
function changeTest(){
	testType = $("#testChange").val();
	if(testType == "3"){
		$("#timeInput").hide();
		$("#lableTime").hide();
		$("#iud").val("3");
	}
	else if(testType == "2"){
		$("#timeInput").show();
		$("#lableTime").show();
		$("#iud").val("2");
	}else{
		console.log("Error: Invalid test type!");
	}
}

function changeQuestion(divID){
		var now = $("#cache-" + divID + "").html();
		var mid = $("#" + divID + "").html();
		$("#" + divID + "").html(now);
		$("#cache-" + divID + "").html(mid);
}
function addAnswerChoice(id){
	var radio = $("#" + id + " #answerGroup").html();
	$("#answerGroup" + id + "").append($("#answerGroup").html());
	var select = $("#answerGroup" + id + " .input-group input[type=radio]").last();
	var lengt = $("#answerGroup" + id + " .input-group").length;
	if(!$("#answerGroup" + id + " .input-group").length){
		var lengt = 1;
	}
	if($("#answerGroup" + id + " .input-group input[type=radio][value=" + lengt + "]").lengt){
		lengt += 1;
	}
	select.attr("value", lengt);
	console.log(select);
	console.log(lengt);
	console.log($("#answerGroup" + id + " .input-group").length);
	console.log("done");
	
	
}
function removeAnswerChoice(id){
	if($("#answerGroup" + id + " .input-group").length != 1){
		$("#answerGroup" + id + " .input-group").last().remove();	
	}
}
function submitForm(loc, id){
	if(loc == "cache"){
		var form = $("#multipleChoice" + id + " form").serialize();
	}else if(loc == "reg"){
		var form = $("#longAnswer" + id + " form").serialize();
	}
	console.log(form);
	$.post("./addQuestion.php?aid=<?php print $id; ?>&qid=" + id + "", form)
		.done(function( data ) {
			$("#" + id + "-debug").html(data);
		});
}
</script>
<?php if($timeAllowed == "-1"){ ?><script>$(document).ready(function(){ changeTest(); });</script><?php } ?>
<!--<script src="js/canvas-to-blob.js" type="text/javascript"></script>-->
<script src="js/fileinput.js"></script>
<script>
	$("#input-la").fileinput();
</script>
</body>
<?php
include "core/modal.php";
?>
</html>