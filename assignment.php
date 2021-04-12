<?php
session_start();
if(!$_GET['id']){
	header("location: 404.html");
	exit;
}
$id = $_GET['id'];
include "core/sqlconnect.php";
include "core/class.user.php";
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
$user = new user();
$class2 = $user->fetchAssinClass($id, "class");
$user->inClass($class2);
if($user->fetchAssinClass($id, "delete") == "1"){
	header("Location: 404.html");
	exit;
}
$myseql = mysql_query("SELECT * FROM `classes` WHERE `id`='" . mysql_real_escape_string($class2) . "'");
$fetck = mysql_fetch_array($myseql);
?>
<!doctype html>
<html>

<head>
<title>Student Portal</title>
<base href="http://<?php print $subdomain ?>.aristocratlms.com/">
<link rel="stylesheet" type="text/css" href="style/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="style/global.css" />
<link rel="stylesheet" type="text/css" href="style/style.css" />
<link rel="stylesheet" href="style/quill.snow.css" />
<link rel="stylesheet" type="text/css" href="style/fileinput.css" type="text/css" />
<script src="js/jquery.min.js"></script>
<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/circle-progress.js"></script>
<script src="js/calendar.js"></script>
<script src="js/notifications.js"></script>
<script src="js/canvas-to-blob.js"></script>
<script src="js/filepicker.js"></script>
<script src="js/quill.min.js"></script>
<script src="js/fileinput.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/s/bs/dt-1.10.10,r-2.0.0/datatables.min.js"></script>
<script>
$.widget.bridge('uibutton', $.ui.button);
$.widget.bridge('uitooltip', $.ui.tooltip);
</script>
<style>
.glyphicon.spinning {
    animation: spin 1s infinite linear;
    -webkit-animation: spin2 1s infinite linear;
}

@keyframes spin {
    from { transform: scale(1) rotate(0deg); }
    to { transform: scale(1) rotate(360deg); }
}

@-webkit-keyframes spin2 {
    from { -webkit-transform: rotate(0deg); }
    to { -webkit-transform: rotate(360deg); }
}
</style>

<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<!--
Website created, designed, and coded by: Leighton Tidwell;
-->
</head>
<body>
<?php
include "core/navigation.php";
?>
<div class="container">
	<div class="row">
		<div class="col-md-3">
        <?php
			if($user->fetchAssinClass($id, "picture")){
		?>
        <div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Assignment Picture</h3>
			  	</div>
				<div class="panel-body">
                    <img class="thumbnail img-responsive center-block" src="<?php print $user->fetchAssinClass($id, "picture"); ?> " />
				</div>
			</div>
            <?php
			}
			?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Assignment Information</h3>
			  	</div>
				<div class="panel-body">
                    <table class="table table-striped customTable table-bordered">
                    	<tr>
                    		<td>Created By:</td>
                    		<?php 
                    			$teacher = json_decode($fetck['teacher'],true);
                    			foreach($teacher as $tc){
									echo("<td>" . $user->fetchProfileInfo($tc,"firstName") . " " .$user->fetchProfileInfo($tc,"lastName") . " </td>");
								}
							?>
                    	</tr>
                    	<?php
                    		$mining = mysql_query("SELECT * FROM `project` WHERE `aids` LIKE '%" . $id . "%'");
                    		$fetchMin = mysql_fetch_array($mining);
                    		if(mysql_num_rows($mining) == 1){
	                    		echo("<tr>");
	                    			echo("<td>Project</td>");
	                    			echo("<td>" . $fetchMin['name'] . "</td>");
	                    		echo("</tr>");
	                    	}else if(mysql_num_rows($mining) == 0){
	                    		
	                    	}
                    	?>
                        <tr>
                            <td>Time Assigned:</td>
                            <td><?php print date("F j", $user->fetchAssinClass($id,"timeAssigned")); ?> </td>
                        </tr>
                        <tr>
                            <td>Time Due:</td>
                            <td><?php print date("F j", $user->fetchAssinClass($id,"timeDue")); ?> </td>
                        </tr>
                        <tr>
                            <td>Type:</td>
                            <td>
                            	<?php
                            		if($user->fetchAssinClass($id, "type") == 1){
                            			print "Assignment";
                            		}else{
                            			print "Test";
                            		}
                            	?>
                            </td>
                        </tr>
                        <tr>
                        	<td>Turn in:</td>
                        	<td>Digital</td>
                        </tr>
						<?php
							if(!$user->IsTeacher()){
								$findGradeStudent = mysql_query("SELECT * FROM `grades` WHERE `uid`='" .mysql_real_escape_string($user->getUserInfo("id"))."' AND `aid`='" . mysql_real_escape_string($id) . "' AND `cid`='" . mysql_real_escape_string($class2) . "'");
								if(mysql_num_rows($findGradeStudent) != 0){
									$fetchTheGrade = mysql_fetch_array($findGradeStudent);
									echo("<tr>");
										echo("<td>Grade:</td>");
										echo("<td>" . $fetchTheGrade['grade'] . "</td>");
									echo("</tr>");
								}
								$lateQuery = mysql_query("SELECT * FROM `turnin` WHERE `class`='" . mysql_real_escape_string($class2) . "' AND `aid`='" . mysql_real_escape_string($id) . "' AND `uid`='" . mysql_real_escape_string($user->getUserInfo("id"))."' ORDER BY `time` DESC LIMIT 1") or die(mysql_error());
								$fetchLateQuery = mysql_fetch_array($lateQuery);
								if($fetchLateQuery['time'] > $user->fetchAssinClass($id,"timeDue")){
									echo("<tr class=\"warning\">");
										echo("<td>Late:</td>");
										echo("<td>Yes</td>");
									echo("</tr>");
								}
		
							}
						?>
                    </table>
				</div>
			</div>
		</div>
		<div class="col-md-9">
			<div class="panel panel-default">
				<div class="panel-heading">
				<?php
					if($user->isTeacher()){
				?>
					<a href="editAssignment/<?php print $id; ?>"><span class="glyphicon glyphicon-pencil pull-right"></span></a>
				<?php
				}
				?>
                <ol class="breadcrumb panel-title" style="margin:0px; padding:0px;">
                	<li>
						<a href="http://<?php print $subdomain ?>.aristocratlms.com/course/<?php print $user->fetchAssinClass($id, "class"); ?>"><?php print $user->getClassInfo($class2,"name"); ?></a>
                    </li>
                    <li class="active"><?php print $user->fetchAssinClass($id, "name"); ?></li>
                </ol>
			  	</div>
				<div class="panel-body">
					<p><h2><?php print $user->fetchAssinClass($id, "name"); ?></h2>	
                    	<?php print $user->fetchAssinClass($id, "description"); ?></h2></p>
                        <hr>
                    		<?php
                    $jsonString = $user->fetchAssinClass($id, "resources");
					$jsonArray = json_decode($jsonString, true);
					$count = 0;
					if($jsonString != "null" && $user->fetchAssinClass($id, "resources") != ""){
						$object = count($jsonArray);
						$url = array();
						$resource = array();
						for($i = 0;  $i < $object; $i++){
							if($i % 2 == 0){
								$newCount = count($url);
								$newNum = $newCount + 1;
								$url[$newNum] = $jsonArray[$i];
							}else{
								$newCount = count($resource);
								$newNum = $newCount + 1;
								$resource[$newNum] = $jsonArray[$i];
							}
						}
						$countMe = 1;
						if(count($url) == 1 && $url[1] == "" && $resource[1] == ""){
						}
						else{
						echo("<h3>Resources</h3>");
						echo("<div class=\"well\">");
						foreach($url as $link){
							if($url != "" && $link != ""){
								echo("<span class=\"glyphicon glyphicon-save-file\" aria-hidden=\"true\"></span>&nbsp;<a href=\"" . $resource[$countMe] . "\">" . $link . "</a><br />");
							}
								$countMe++;
						}
						echo("</div>");
					}
					}
                    		?>
						<?php
							$find = mysql_query("SELECT * FROM `turnin` WHERE `aid`='" . mysql_real_escape_string($id) . "' AND `uid`='" . $user->getUserInfo("id") . "' ORDER BY `time` DESC");
							if($user->fetchAssinClass($id, "type") == "1"){
								if(!$user->isTeacher()){
									if(mysql_num_rows($find) != $user->fetchAssinClass($id, "submissions") || !mysql_num_rows($find) || mysql_num_rows($find) == 0){
							?>
							<div class="panel with-nav-tabs">
								<div class="panel-heading">
										<ul class="nav nav-tabs">
											<li class="active"><a href="#tab1default" data-toggle="tab">Upload File</a></li>
				                            <li><a href="#tab2default" data-toggle="tab">Create</a></li>
				                            <li><a href="#tab3default" data-toggle="tab">Connect</a></li>
										</ul>
								</div>
								<div class="panel-body">
									<div class="tab-content">
											<div class="tab-pane fade in active" id="tab1default">
												<label class="control-label">Submit Assignment</label> 
												<input id="input-709" type="file" name="assignments[]" multiple class="file-loading">
											</div>
											<div class="tab-pane fade" id="tab2default">
												
														<?php
															$findCreation = mysql_query("SELECT * FROM `turnin` WHERE `aid`='" . mysql_real_escape_string($id) . "' AND `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `name`='creation' ORDER BY `time` DESC");
															if(mysql_num_rows($findCreation) > 0){
																?>
												<span class="pull-right">
													<select onchange="loadText()" id="textEntries" class="form-control">
														<option>Select Entry</option>
														<?php
																$i = 1;
																while($fetcheroonie = mysql_fetch_array($findCreation)){
																	echo("<option value=\"" . $fetcheroonie['id'] . "\">Text Entry " . $i . "</option>");
																	$i++;
																}
																?>
														</select>
												</span>
																<?php
															}
														?>
													
												<div class="panel panel-info">
													<div class="quill-wrapper">
														<div class="panel-heading panel-info">
															<div id="full-toolbar" class="toolbar">
																<span class="ql-format-group">
																	<select title="Font" class="ql-font">
																		<option value="sans-serif" selected="">Sans Serif</option>
																		<option value="serif">Serif</option>
																		<option value="monospace">Monospace</option>
																	</select>
																	<select title="Size" class="ql-size">
																		<option value="10px">Small</option>
																		<option value="13px" selected="">Normal</option>
																		<option value="18px">Large</option>
																		<option value="32px">Huge</option>
																	</select>
																</span>
																<span class="ql-format-group">
																	<span title="Bold" class="ql-format-button ql-bold"></span>
																	<span class="ql-format-separator"></span>
																	<span title="Italic" class="ql-format-button ql-italic"></span>
																	<span class="ql-format-separator"></span>
																	<span title="Underline" class="ql-format-button ql-underline"></span>
																	<span class="ql-format-separator"></span>
																	<span title="Strikethrough" class="ql-format-button ql-strike"></span>
																</span>
																<span class="ql-format-group">
																	<select title="Text Color" class="ql-color">
																		<option value="rgb(0, 0, 0)" label="rgb(0, 0, 0)" selected=""></option>
																		<option value="rgb(230, 0, 0)" label="rgb(230, 0, 0)"></option>
																		<option value="rgb(255, 153, 0)" label="rgb(255, 153, 0)"></option>
																		<option value="rgb(255, 255, 0)" label="rgb(255, 255, 0)"></option>
																		<option value="rgb(0, 138, 0)" label="rgb(0, 138, 0)"></option>
																		<option value="rgb(0, 102, 204)" label="rgb(0, 102, 204)"></option>
																		<option value="rgb(153, 51, 255)" label="rgb(153, 51, 255)"></option>
																		<option value="rgb(255, 255, 255)" label="rgb(255, 255, 255)"></option>
																		<option value="rgb(250, 204, 204)" label="rgb(250, 204, 204)"></option>
																		<option value="rgb(255, 235, 204)" label="rgb(255, 235, 204)"></option>
																		<option value="rgb(255, 255, 204)" label="rgb(255, 255, 204)"></option>
																		<option value="rgb(204, 232, 204)" label="rgb(204, 232, 204)"></option>
																		<option value="rgb(204, 224, 245)" label="rgb(204, 224, 245)"></option>
																		<option value="rgb(235, 214, 255)" label="rgb(235, 214, 255)"></option>
																		<option value="rgb(187, 187, 187)" label="rgb(187, 187, 187)"></option>
																		<option value="rgb(240, 102, 102)" label="rgb(240, 102, 102)"></option>
																		<option value="rgb(255, 194, 102)" label="rgb(255, 194, 102)"></option>
																		<option value="rgb(255, 255, 102)" label="rgb(255, 255, 102)"></option>
																		<option value="rgb(102, 185, 102)" label="rgb(102, 185, 102)"></option>
																		<option value="rgb(102, 163, 224)" label="rgb(102, 163, 224)"></option>
																		<option value="rgb(194, 133, 255)" label="rgb(194, 133, 255)"></option>
																		<option value="rgb(136, 136, 136)" label="rgb(136, 136, 136)"></option>
																		<option value="rgb(161, 0, 0)" label="rgb(161, 0, 0)"></option>
																		<option value="rgb(178, 107, 0)" label="rgb(178, 107, 0)"></option>
																		<option value="rgb(178, 178, 0)" label="rgb(178, 178, 0)"></option>
																		<option value="rgb(0, 97, 0)" label="rgb(0, 97, 0)"></option>
																		<option value="rgb(0, 71, 178)" label="rgb(0, 71, 178)"></option>
																		<option value="rgb(107, 36, 178)" label="rgb(107, 36, 178)"></option>
																		<option value="rgb(68, 68, 68)" label="rgb(68, 68, 68)"></option>
																		<option value="rgb(92, 0, 0)" label="rgb(92, 0, 0)"></option>
																		<option value="rgb(102, 61, 0)" label="rgb(102, 61, 0)"></option>
																		<option value="rgb(102, 102, 0)" label="rgb(102, 102, 0)"></option>
																		<option value="rgb(0, 55, 0)" label="rgb(0, 55, 0)"></option>
																		<option value="rgb(0, 41, 102)" label="rgb(0, 41, 102)"></option>
																		<option value="rgb(61, 20, 102)" label="rgb(61, 20, 102)"></option>
																	</select>
																	<span class="ql-format-separator"></span>
																	<select title="Background Color" class="ql-background">
																		<option value="rgb(0, 0, 0)" label="rgb(0, 0, 0)"></option>
																		<option value="rgb(230, 0, 0)" label="rgb(230, 0, 0)"></option>
																		<option value="rgb(255, 153, 0)" label="rgb(255, 153, 0)"></option>
																		<option value="rgb(255, 255, 0)" label="rgb(255, 255, 0)"></option>
																		<option value="rgb(0, 138, 0)" label="rgb(0, 138, 0)"></option>
																		<option value="rgb(0, 102, 204)" label="rgb(0, 102, 204)"></option>
																		<option value="rgb(153, 51, 255)" label="rgb(153, 51, 255)"></option>
																		<option value="rgb(255, 255, 255)" label="rgb(255, 255, 255)" selected=""></option>
																		<option value="rgb(250, 204, 204)" label="rgb(250, 204, 204)"></option>
																		<option value="rgb(255, 235, 204)" label="rgb(255, 235, 204)"></option>
																		<option value="rgb(255, 255, 204)" label="rgb(255, 255, 204)"></option>
																		<option value="rgb(204, 232, 204)" label="rgb(204, 232, 204)"></option>
																		<option value="rgb(204, 224, 245)" label="rgb(204, 224, 245)"></option>
																		<option value="rgb(235, 214, 255)" label="rgb(235, 214, 255)"></option>
																		<option value="rgb(187, 187, 187)" label="rgb(187, 187, 187)"></option>
																		<option value="rgb(240, 102, 102)" label="rgb(240, 102, 102)"></option>
																		<option value="rgb(255, 194, 102)" label="rgb(255, 194, 102)"></option>
																		<option value="rgb(255, 255, 102)" label="rgb(255, 255, 102)"></option>
																		<option value="rgb(102, 185, 102)" label="rgb(102, 185, 102)"></option>
																		<option value="rgb(102, 163, 224)" label="rgb(102, 163, 224)"></option>
																		<option value="rgb(194, 133, 255)" label="rgb(194, 133, 255)"></option>
																		<option value="rgb(136, 136, 136)" label="rgb(136, 136, 136)"></option>
																		<option value="rgb(161, 0, 0)" label="rgb(161, 0, 0)"></option>
																		<option value="rgb(178, 107, 0)" label="rgb(178, 107, 0)"></option>
																		<option value="rgb(178, 178, 0)" label="rgb(178, 178, 0)"></option>
																		<option value="rgb(0, 97, 0)" label="rgb(0, 97, 0)"></option>
																		<option value="rgb(0, 71, 178)" label="rgb(0, 71, 178)"></option>
																		<option value="rgb(107, 36, 178)" label="rgb(107, 36, 178)"></option>
																		<option value="rgb(68, 68, 68)" label="rgb(68, 68, 68)"></option>
																		<option value="rgb(92, 0, 0)" label="rgb(92, 0, 0)"></option>
																		<option value="rgb(102, 61, 0)" label="rgb(102, 61, 0)"></option>
																		<option value="rgb(102, 102, 0)" label="rgb(102, 102, 0)"></option>
																		<option value="rgb(0, 55, 0)" label="rgb(0, 55, 0)"></option>
																		<option value="rgb(0, 41, 102)" label="rgb(0, 41, 102)"></option>
																		<option value="rgb(61, 20, 102)" label="rgb(61, 20, 102)"></option>
																	</select>
																</span>
																<span class="ql-format-group">
																	<span title="List" class="ql-format-button ql-list"></span>
																	<span class="ql-format-separator"></span>
																	<span title="Bullet" class="ql-format-button ql-bullet"></span>
																	<span class="ql-format-separator"></span>
																	<select title="Text Alignment" class="ql-align">
																		<option value="left" label="Left" selected=""></option>
																		<option value="center" label="Center"></option>
																		<option value="right" label="Right"></option>
																		<option value="justify" label="Justify"></option>
																	</select>
																</span>
																<span class="ql-format-group">
																	<span title="Link" class="ql-format-button ql-link"></span>
																	<span class="ql-format-separator"></span>
																	<span title="Image" class="ql-format-button ql-image"></span>
																</span>
																
															</div>
														</div>
														<div id="full-editor" class="editor">
															
														</div>
													</div>
												</div>
												<button id="submitt" type="button" onclick="submitText();" class="btn btn-success">Submit</button>
											</div>
                        					<div class="tab-pane fade" id="tab3default">
												<div class="well">
													<button type="button" id="googlePic" class="btn btn-lg btn-success"><img src="./datafiles/ico/product16.png" />&nbsp;Upload with Google Drive</button>
												</div>
												<div id="driveErrors"></div>
											</div>
									</div>
								</div>
							</div>
								<div id="kv-success-2" class="alert alert-success fade in" style="margin-top:10px;display:none;"><ul></ul></div>
								<div id="errordiv" class="alert alert-danger fade in" style="margin-top:10px;display:none;"></div>
							<?php
									}
									if(mysql_num_rows($find) > 0){
										echo("<hr>");
										if($user->fetchAssinClass($id,"submissions") == 0){
											$noSubmit = true;
										}
										echo("<h3>Submissions (" . mysql_num_rows($find) . "");
											if(!$noSubmit){
												echo("/" . $user->fetchAssinClass($id, "submissions") . ")</h3>");
											}else{
												echo(")</h3>");
											}
										echo("<div class=\"well\">");
										$seeGrades = mysql_query("SELECT * FROM `grades` WHERE `uid`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `aid`='" . mysql_real_escape_string($id) . "'");
										if($fetchLateQuery['time'] > $user->fetchAssinClass($id,"timeDue")){
											echo("<span class=\"pull-right\"><button type=\"button\" class=\"btn btn-warning\">Late</button></span>");
										}
										if(mysql_num_rows($seeGrades) == 0){
											echo("<span class=\"pull-right\" style=\"margin-right:5px\"><button type=\"button\" class=\"btn btn-info\">Ungraded</button></span>");
										}
										else{
											echo("<span class=\"pull-right\" style=\"margin-right:5px\"><a href=\"fileGrader.php?id=" . $id . "&cid=" . $class2 . "\"><button type=\"button\" class=\"btn btn-success\">Graded</button></a></span>");
										}
										while($item = mysql_fetch_array($find)){
											if($item['name'] == "creation"){
												echo("<span class=\"glyphicon glyphicon-save-file\" aria-hidden=\"true\"></span>&nbsp;Text Entry on " . date("F j Y, g:i a", $item['time']) . "<br />");
											}
											else{
												echo("<span class=\"glyphicon glyphicon-save-file\" aria-hidden=\"true\"></span>&nbsp;<a href=\"" . $item['href'] . "\">" . $item['name'] . "</a> on " . date("F j Y, g:i a", $item['time']) . "<br />");
											}
										}
										echo("</div>");
									}
								}
								else{
									$users = array();
									$assinArray = mysql_query("SELECT * FROM `turnin` WHERE `class`='" . mysql_real_escape_string($class2) . "' AND `aid`='" . mysql_real_escape_string($id) . "'") or die(mysql_error());
									echo("<div class=\"panel panel-default\">");
										echo("<div class=\"panel-heading\">Submissions</div>");
										echo("<div class=\"panel-body\">");
										while($testme = mysql_fetch_array($assinArray)){
												if(!in_array($testme['uid'],$users)){
													array_push($users, $testme['uid']);
												}
										}
										foreach($users as $usering){
											$assinArrayModerate = mysql_query("SELECT * FROM `turnin` WHERE `class`='" . mysql_real_escape_string($class2) . "' AND `aid`='" . mysql_real_escape_string($id) . "' AND `uid`='" . mysql_real_escape_string($usering)."'") or die(mysql_error());
											$query = mysql_query("SELECT * FROM `users` WHERE `id`='" . mysql_real_escape_string($usering). "' ORDER BY `lastName` DESC");
											$fetch = mysql_fetch_array($query);
												echo("<div class=\"media\">");
													echo("<div class=\"media-left media-middle\">");
														echo("<a href=\"./profile/".$fetch['username']."\" class=\"hidden-xs\">");
															echo("<img class=\"media-object\" width=\"64px\" src=\"" .$fetch['profilePicture']. "\" alt=\"Profile Picture\" />");
														echo("</a>");
													echo("</div>");
													echo("<div class=\"media-body\">");
														echo("<h4 class=\"media-heading\"></h4>");
															echo("<div class=\"well clearfix\">");
															$lateErNot = mysql_query("SELECT * FROM `turnin` WHERE `class`='" . mysql_real_escape_string($class2) . "' AND `aid`='" . mysql_real_escape_string($id) . "' AND `uid`='" . mysql_real_escape_string($usering)."' ORDER BY `time` DESC LIMIT 1") or die(mysql_error());
															$fetchLate = mysql_fetch_array($lateErNot);
															if($fetchLate['time'] > $user->fetchAssinClass($id,"timeDue")){
																$alert = "<button type=\"button\" class=\"btn btn-warning\">Late</button>";
															}
															$findGrade = mysql_query("SELECT * FROM `grades` WHERE `uid`='" .mysql_real_escape_string($usering)."' AND `aid`='" . mysql_real_escape_string($id) . "' AND `cid`='" . mysql_real_escape_string($class2) . "'");
															if(mysql_num_rows($findGrade) != 0){
																$alert = "<button type=\"button\" class=\"btn btn-info\">Graded</button>";
															}
															echo("<h4>" . $fetch['firstName']. " " . $fetch['lastName'] . "'s submission(s)<span class=\"pull-right\">" .$alert. "&nbsp;<a href=\"fileGrader.php?id=".$id."&uid=".$usering."&cid=".$class2."\" class=\"btn btn-default\"  >Grade</a></span></h4><hr>");
												while($assin = mysql_fetch_array($assinArrayModerate)){
													if(strlen($assin['name']) > 20){
														$stringCut = substr($assin['name'], 0, 20);
														$string = $stringCut . "...";
													}
													else{
														$string = $assin['name'];
													}
																	if($assin['name'] == "creation"){
																		echo("<span class=\"glyphicon glyphicon-save-file\" aria-hidden=\"true\"></span>&nbsp;Text Entry<span class=\"hidden-xs\"> on " . date("F j Y, g:i a", $assin['time']) . "</span><br />");
																	}
																	else{
																		echo("<span class=\"glyphicon glyphicon-save-file\" aria-hidden=\"true\"></span>&nbsp;<a href=\"" . $assin['href'] . "\">" . $string. "</a><span class=\"hidden-xs\"> on " . date("F j Y, g:i a", $assin['time']) . "</span><br />");
												}					}
															echo("</div>");
													echo("</div>");
												echo("</div>");
												echo("<hr>");
												/*
												<span class=\"label label-warning\">Late</span><span class=\"label label-info\">Not Graded</span><span class=\"label label-success\">Graded</span>
												*/
											
									
								}
								echo("<h4>Not Submitted</h4>");
									if($users){
										$dude = true;
									}
									$schoolID = $user->getUserInfo("school");
									echo("<ul class=\"list-group\">");
										foreach($user->getUsersFrom($schoolID) as $person){
											if($dude){
												if(!in_array($person, $users) && $user->fetchProfileInfo($person, "permissions") != 2){
													$findGrades = mysql_query("SELECT * FROM `grades` WHERE `uid`='" .mysql_real_escape_string($person)."' AND `aid`='" . mysql_real_escape_string($id) . "' AND `cid`='" . mysql_real_escape_string($class2) . "'");
													if(mysql_num_rows($findGrades) != 0){
														$alert2 = "<a class=\"badge\" style=\"background-color: #5bc0de;\">Graded</a>";
													}
													else{
														$alert2 = "";
													}
													echo("<li class=\"list-group-item\">");
														echo("<a class=\"badge\" href=\"fileGrader.php?id=".$id."&uid=".$person."&cid=".$class2."\">");
															echo("Grade");
														echo("</a>");
														if($alert2){ echo($alert2); }
														echo("".$user->fetchProfileInfo($person,"firstName")." ".$user->fetchProfileInfo($person,"lastName")."");
													echo("</li>");
												}
											}
											else{
												$findGrades = mysql_query("SELECT * FROM `grades` WHERE `uid`='" .mysql_real_escape_string($person)."' AND `aid`='" . mysql_real_escape_string($id) . "' AND `cid`='" . mysql_real_escape_string($class2) . "'");
												if(mysql_num_rows($findGrades) != "0"){
													$alert2 = "<a class=\"badge\" style=\"background-color: #5bc0de;\">Graded</a>";
												}
												else{
													$alert2 = "";
												}
												echo("<li class=\"list-group-item\">");
													echo("<a class=\"badge\" href=\"fileGrader.php?id=".$id."&uid=".$person."&cid=".$class2."\">");
														echo("Grade");
													echo("</a>");
													if($alert2){ echo($alert2); }
													echo("".$user->fetchProfileInfo($person,"firstName")." ".$user->fetchProfileInfo($person,"lastName")."");
												echo("</li>");
											}
										}
									echo("</ul>");
								echo("</div>");
							echo("</div>");
								}
							}
							elseif($user->fetchAssinClass($id, "type") == "2"){
								$queryTest = mysql_query("SELECT * FROM `tests` WHERE `aid`='" . mysql_real_escape_string($id) . "'");
								$fetchTest = mysql_fetch_array($queryTest);
								echo("<h2>Test</h2>");
								echo("<div class=\"well\">");
								if($fetchTest['timeAllowed'] == "-1"){
									echo("<p>This is an untimed test. You will have " . $fetchTest['attempts'] . " attempts for this test.</p>");

								}
								else{
									echo("<p>This is a timed test. You will be allotted " . gmdate("H:i:s",$fetchTest['timeAllowed']) . " to complete the test. You will have " . $fetchTest['attempts'] . " attempts for this test.</p>");
								}
									echo("<a href=\"takeTest.php?id=" . $id . "\" class=\"btn btn-default btn-info\">Take The Test</a>");
								echo("</div>");
								echo("<hr>");
								$query = mysql_query("SELECT * FROM `testinfo` WHERE `aid`='" . mysql_real_escape_string($id) . "' AND `cid`='" . mysql_real_escape_string($class2) . "'");
								$users = array();
								if($user->isTeacher()){
									echo("<div class=\"panel panel-default\">");
											echo("<div class=\"panel-heading\">Submissions</div>");
											echo("<div class=\"panel-body\">");
												while($fetch = mysql_fetch_array($query)){
													if(!in_array($fetch['uid'],$users)){
														array_push($users, $fetch['uid']);
													}
												}
												foreach($users as $usering){
													$query2 = mysql_query("SELECT * FROM `testinfo` WHERE `aid`='" . mysql_real_escape_string($id) . "' AND `cid`='" . mysql_real_escape_string($class2) . "' AND `uid`='" . mysql_real_escape_string($usering) . "'");
													$query3 = mysql_query("SELECT * FROM `users` WHERE `id`='" . mysql_real_escape_string($usering). "' ORDER BY `lastName` DESC");
													$fetch3 = mysql_fetch_array($query3);
													echo("<div class=\"media\">");
														echo("<div class=\"media-left media-middle\">");
															echo("<a href=\"./profile/".$fetch3['username']."\" class=\"hidden-xs\">");
																echo("<img class=\"media-object\" width=\"64px\" src=\"" .$fetch3['profilePicture']. "\" alt=\"Profile Picture\" />");
															echo("</a>");
														echo("</div>");
														echo("<div class=\"media-body\">");
															echo("<h4 class=\"media-heading\"></h4>");
																echo("<div class=\"well clearfix\">");
																$lastAttempt = mysql_num_rows($query2);
																$query4 = mysql_query("SELECT * FROM `testinfo` WHERE `aid`='" . mysql_real_escape_string($id) . "' AND `cid`='" . mysql_real_escape_string($class2) . "' AND `uid`='" . mysql_real_escape_string($usering) . "' and `attempt`='" . $lastAttempt . "'");
																$fetch4 = mysql_fetch_array($query4);
																
																if($fetch4['timeStarted'] > $user->fetchAssinClass($id,"timeDue")){
																	$alert = "<button type=\"button\" class=\"btn btn-warning\">Late</button>";
																}
																$findGrade = mysql_query("SELECT * FROM `grades` WHERE `uid`='" .mysql_real_escape_string($usering)."' AND `aid`='" . mysql_real_escape_string($id) . "' AND `cid`='" . mysql_real_escape_string($class2) . "'");
																if(mysql_num_rows($findGrade) != 0){
																	$alert = "<button type=\"button\" class=\"btn btn-info\">Graded</button>";
																}
																 
																echo("<h4>" . $fetch3['firstName']. " " . $fetch3['lastName'] . "'s submission(s)<span class=\"pull-right\">" .$alert. "&nbsp;<a href=\"fileGrader.php?id=".$id."&uid=".$usering."&cid=".$class2."\" class=\"btn btn-default\"  >Grade</a></span></h4><hr>");
																while($fetch2 = mysql_fetch_array($query2)){
																	$string = "Attempt " . $fetch2['attempt'] . "";
																	echo("<span class=\"glyphicon glyphicon-save-file\" aria-hidden=\"true\"></span>&nbsp;<a href=\"./fileGrader.php?id=" . $id . "&uid=".$usering."&cid=".$class2."\">" . $string. "</a><span class=\"hidden-xs\"> on " . date("F j Y, g:i a", $fetch2['timeStarted']) . "</span><br />");
																}
																echo("</div>");
														echo("</div>");
													echo("</div>");
													echo("<hr>");
												}
											echo("<h4>Not Submitted</h4>");
											if($users){
												$dude = true;
											}
											$schoolID = $user->getUserInfo("school");
											echo("<ul class=\"list-group\">");
												foreach($user->getUsersFrom($schoolID) as $person){
													if($dude){
														if(!in_array($person, $users) && $user->fetchProfileInfo($person, "permissions") != 2){
															$findGrades = mysql_query("SELECT * FROM `grades` WHERE `uid`='" .mysql_real_escape_string($person)."' AND `aid`='" . mysql_real_escape_string($id) . "' AND `cid`='" . mysql_real_escape_string($class2) . "'");
															if(mysql_num_rows($findGrades) != 0){
																$alert2 = "<a class=\"badge\" style=\"background-color: #5bc0de;\">Graded</a>";
															}
															else{
																$alert2 = "";
															}
															echo("<li class=\"list-group-item\">");
																echo("<a class=\"badge\" href=\"fileGrader.php?id=".$id."&uid=".$person."&cid=".$class2."\">");
																	echo("Grade");
																echo("</a>");
																if($alert2){ echo($alert2); }
																echo("".$user->fetchProfileInfo($person,"firstName")." ".$user->fetchProfileInfo($person,"lastName")."");
															echo("</li>");
														}
													}
													else{
														$findGrades = mysql_query("SELECT * FROM `grades` WHERE `uid`='" .mysql_real_escape_string($person)."' AND `aid`='" . mysql_real_escape_string($id) . "' AND `cid`='" . mysql_real_escape_string($class2) . "'");
														if(mysql_num_rows($findGrades) != "0"){
															$alert2 = "<a class=\"badge\" style=\"background-color: #5bc0de;\">Graded</a>";
														}
														else{
															$alert2 = "";
														}
														echo("<li class=\"list-group-item\">");
															echo("<a class=\"badge\" href=\"fileGrader.php?id=".$id."&uid=".$person."&cid=".$class2."\">");
																echo("Grade");
															echo("</a>");
															if($alert2){ echo($alert2); }
															echo("".$user->fetchProfileInfo($person,"firstName")." ".$user->fetchProfileInfo($person,"lastName")."");
														echo("</li>");
													}
												}
											echo("</ul>");
										echo("</div>");
									echo("</div>");
								}
							}
							elseif($user->fetchAssinClass($id, "type") == "3"){
								echo("<h2>Timed Test</h2>");
									echo("<div class=\"well\">");
										echo("");
									echo("</div>");
							}
							elseif($user->fetchAssinClass($id, "type") == "4"){
								echo("<h2>Free Response</h2>");
									echo("<div class=\"well\">");
										echo("");
									echo("</div>");
							}
						?>
				</div>
			</div>
            <div class="panel panel-default">
				<div class="panel-heading">
                	Comments
			  	</div>
				<div class="panel-body">
					<div class="col-md-12">
						<div class="form-group">
							<textarea id="commentsr" class="form-control"></textarea><br />
							<button type="button" onclick="postComment();" class="btn btn-success">Submit Comment</button>
						</div>
					</div>
					<hr>
					<div class="col-md-12" id="commentBody">
						<?php
							$findComm = mysql_query("SELECT * FROM `comments` WHERE `aid`='" . mysql_real_escape_string($id) . "'");
							while($fetchCom = mysql_fetch_array($findComm)){
								echo("<div class=\"alert alert-info\">");
									echo("<img class=\"img-responsive pull-left\" width=\"30px\" src=\"" . $user->fetchProfileInfo($fetchCom['uid'],"profilePicture") . "\" />&nbsp;");
									echo("<b>" . $user->fetchProfileInfo($fetchCom['uid'],"firstName") . " " . $user->fetchProfileInfo($fetchCom['uid'],"lastName") . "</b>:&nbsp;");
									echo($fetchCom['comment']);
									echo("<span class=\"pull-right\">" . date("F j Y, g:i a",$fetchCom['timestamp']) . "</span>");
								echo("</div>");
							}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
include "core/footer.php";
?>
<!--
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/calendar.js"></script>
<script src="js/canvas-to-blob.js" type="text/javascript"></script>
<script src="js/filepicker.js"></script> -->
<script>
	function initPicker() {
		var picker = new FilePicker({
			apiKey: 'AIzaSyAx7MDzGK4nor3q95taan5a0fZA6TApNh8',
			clientId: '815564136862-mk4kreerrupmutknfkqnnq42isf50kud',
			buttonEl: document.getElementById('googlePic'),
			onSelect: function(file) {
				saveFile(file);
				$("#googlePic").html("<span class='glyphicon glyphicon-refresh spinning'></span>&nbsp; Uploading file, Please wait..");
			}
		});	
	}
	function saveFile(file){
		var accessToken = gapi.auth.getToken().access_token;
			var classe = <?php print $id; ?>;
			$.ajax({
				url : "turnInGoogleDrive.php?aid=" + classe + "",
				type: "POST",
				data : {accessToken:accessToken, fileTitle:file.title, downloadUrl:file.downloadUrl, fileExtension:file.fileExtension, fileSize:file.fileSize, mimeType:file.mimeType, exportLinks:file.exportLinks},
				success: function(data)
				{
					$("#googlePic").html("<img src='./datafiles/ico/product16.png' />&nbsp;Upload with Google Drive");
					$("#driveErrors").html(data);
				}
		   });
	}
</script>

<script src="https://www.google.com/jsapi?key=AIzaSyAx7MDzGK4nor3q95taan5a0fZA6TApNh8"></script>
<script src="https://apis.google.com/js/client.js?onload=initPicker"></script> 

<?php
if($user->fetchAssinClass($id, "type") == "1"){
?>
<!--<link rel="stylesheet" href="style/quill.snow.css" />
<script src="js/quill.min.js"></script>-->
<!-- Initialize Quill editor -->
<?php
	if(!$user->isTeacher()){
?>
<script>
// Initialize editor with custom theme and modules
var fullEditor = new Quill('#full-editor', {
  modules: {
    'toolbar': { container: '#full-toolbar' },
    'link-tooltip': true,
    "image-tooltip" : true
  },
  theme: 'snow'
});

function submitText(){
	var htmlString = fullEditor.getHTML();
	$.post("turnin.php?id=<?php print $id ?>", { 'creation': htmlString }).done(function(data){
		$("#submitt").html("<span class='glyphicon glyphicon-refresh spinning'></span>&nbsp; Saving, Please wait..");
		location.reload();
	});

}

function loadText(){
	var strings = $("#textEntries").val();
	if(strings != -1){
		$.get("textRetrieve.php?id=" + strings + "",function(data){
			fullEditor.setHTML(data);
			fullEditor.editor.enable(false);
		});
	}else{
		fullEditor.setHTML("");
		fullEditor.editor.enable(true);
	}

}
</script>
<?php
}
?>
<script>
function postComment(){
	var strings = $("#commentsr").val();
	$.post("postComment.php", { 'comment': strings,'id':'<?php print $id ?>','aid':'true' }, function( data ){
		console.log(data);
	});
	$("#commentBody").append("<div class='alert alert-info'>" + strings + "</div>");
	$("#commentsr").val("");

}
</script>
<!--
<script src="js/fileinput.js"></script>
-->
<script>
	$("#input-709").fileinput({
		uploadUrl: "http://<?php print $subdomain; ?>.aristocratlms.com/turnin.php?id=<?php print $id ?>",
		showPreview: false,
		uploadAsync: false,
		maxFileCount: 5,
		allowedFileExtensions: ['jpg', 'png', 'zip','pdf','txt'],
		elErrorContainer: '#errordiv'
	}).on('filebatchuploadsuccess', function(event,data) {
		var out = '';
		$.each(data.files, function(key, file) {
			var fname = file.name;
			out = out + '<li>' + 'Uploaded file # ' + (key + 1) + ' - '  +  fname + ' successfully.' + '</li>';
		});
		$('#kv-success-2 ul').append(out);
		$('#kv-success-2').fadeIn('slow');
	});
</script>
<?php
}
?>
</body>
<?php
include "core/modal.php";
?>
</html>