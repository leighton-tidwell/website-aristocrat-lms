<?php
session_start();
$profile = $_GET['id'];
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
if(!$profile){
	header("Location: 404.html");
	exit;
}
include "core/sqlconnect.php";
include "core/class.user.php";
$user = new user();
if(!$user->fetchProfileInfo($profile,"username")){
	header("Location: 404.html");
	exit;
}
?>
<!doctype html>
<html>

<head>
<title>Student Portal</title>
<base href="http://<?php print $subdomain; ?>.aristocratlms.com/">
<link rel="stylesheet" type="text/css" href="http://<?php print $subdomain; ?>.aristocratlms.com/style/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="http://<?php print $subdomain; ?>.aristocratlms.com/style/global.css" />
<link rel="stylesheet" type="text/css" href="http://<?php print $subdomain; ?>.aristocratlms.com/style/fileinput.css" type="text/css" />
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
<script>

</script>
</head>
<body>
<?php
include "core/navigation.php";
?>
<div class="container">
	<div class="col-md-4 text-center" style="margin-bottom: 10px;">
		<button id="editProfilePicture" type="button" data-toggle="" data-target="#myModal"> <img id="profilePicture" src="<?php print $user->fetchProfileInfo($profile, "profilePicture"); ?>" alt="profile_picture" max-width="247px" max-height="247px" class="img-rounded img-responsive center-block"></button>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Upload Picture</h4>
      </div>
      <div class="modal-body">
      <h3>Enter IMG url here:</h3>
      <small>Preferably one that is 250px by 250px.</small>
      <textarea class="form-control" id="urlForm" name="URL"><?php print $user->fetchProfileInfo($profile, "profilePicture"); ?></textarea>
		<!--<label class="control-label">Submit Assignment</label> 
		<input id="input-43" type="file" name="assignments[]" multiple class="file-loading">
		<div id="kv-success-2" class="alert alert-success fade in" style="margin-top:10px;display:none;"><ul></ul></div>
		<div id="errordiv" class="alert alert-danger fade in" style="margin-top:10px;display:none;"></div>-->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Confirm</button>
      </div>
    </div>
  </div>
</div>
		<h3><?php print $user->fetchProfileInfo($profile, "firstName") . " " . $user->fetchProfileInfo($profile,"lastName"); if($_SESSION['username'] == $user->fetchProfileInfo($profile, "username")){?> <button type="button" onClick="changeClass()" id="edit" class="btn btn-primary"><i class="glyphicon glyphicon-pencil"></i></button><?php } ?></h3>
	
		<small id="editable14"><?php print $user->fetchProfileInfo($profile, "quote"); ?></small>
	</div>
	<div class="panel panel-default col-md-8">
		<div class="panel-body">
			<ul class="nav nav-tabs nav-justified">
				<li role="presentation" class="active"><a href="#about" data-toggle="tab">About</a></li>
				<li role="presentation"><a href="#classes" data-toggle="tab" >Classes</a></li>
				<li role="presentation"><a href="#contact" data-toggle="tab" >Contact Information</a></li>
			</ul>
			<hr>
			<form id="profileForm" method="post" action="http://<?php print $subdomain; ?>.aristocratlms.com/updateInformation.php">
			<div class="tab-content">
		        	<div id="about" class="tab-pane fade in active">
		        		<dl class="dl-horizontal">
		        		<?php 
		        		if(!$user->fetchProfileInfo($profile, "aboutMe") && !$user->fetchProfileInfo($profile, "goalsAfterHighschool") && !$user->fetchProfileInfo($profile, "movies") && !$user->fetchProfileInfo($profile, "movies") && !$user->fetchProfileInfo($profile, "tv") && !$user->fetchProfileInfo($profile, "music") && !$user->fetchProfileInfo($profile, "books") && !$user->fetchProfileInfo($profile, "interests") && !$user->fetchProfileInfo($profile, "funFact")){
		        			print "This user has not filled in any data for their profile.";
		        		}
		        		?>
		        		<input type="hidden" value="<?php print $profile; ?>" name="profile">
		        		<input type="hidden" value="" id="QUOTE" name="quote">
		        		<input type="hidden" value="" id="PROFILEPICTURE" name="profilePicture">
	 					<dt id="name">About Me</dt>
	 					<dd id="editable"><?php print $user->fetchProfileInfo($profile, "aboutMe"); ?></dd>
	 					<dt id="name2">Goals After Highschool</dt>
	 					<dd id="editable2"><?php print $user->fetchProfileInfo($profile, "goalsAfterHighschool"); ?></dd>
	 					<dt id="name3">Movies</dt>
	 					<dd id="editable3"><?php print $user->fetchProfileInfo($profile, "movies"); ?></dd>
	 					<dt id="name4">TV</dt>
	 					<dd id="editable4"><?php print $user->fetchProfileInfo($profile, "tv"); ?></dd>
	 					<dt id="name5">Music</dt>
	 					<dd id="editable5"><?php print $user->fetchProfileInfo($profile, "music"); ?></dd>
	 					<dt id="name6">Books</dt>
	 					<dd id="editable6"><?php print $user->fetchProfileInfo($profile, "books"); ?></dd>
	 					<dt id="name7">Interests</dt>
	 					<dd id="editable7"><?php print $user->fetchProfileInfo($profile, "interests"); ?></dd>
	 					<dt id="name8">Fun Fact</dt>
	 					<dd id="editable8"><?php print $user->fetchProfileInfo($profile, "funFact"); ?></dd>
					</dl>
		        	</div>
		        	<div id="classes" class="tab-pane fade">
		        		<table class="table table-striped">
		        		<thead>
			        		<tr>
							<th>Class</th>
							<th>Teacher</th>
						</tr>
					</thead>
		        			<?php
						$classes = $user->fetchProfileInfo($profile,"classes");
						$classes = json_decode($classes,true);
						if($classes != ""){
							ksort($classes);
							$semie = mysql_query("SELECT * FROM `semester` WHERE `startTime` < '" . time() . "' AND `endTime` > '" . time() . "' AND `school`='" . mysql_real_escape_string($user->getUserInfo("school")) . "'");
							$femie = mysql_fetch_array($semie);
							$semester = $femie['id'];

							//now lets get current subsemester
							$subsemie = mysql_query("SELECT * FROM `subsemester` WHERE `startTime` < '" . time() . "' AND `endTime` > '" . time() . "' AND `semid`='" . mysql_real_escape_string($semester) . "'");
							$subfemie = mysql_fetch_array($subsemie);
							$subsemester = $subfemie['id'];

							foreach($classes[$semester][$subsemester] as $period=>$class){
								if($class != ""){
								$query2 = mysql_query("SELECT * FROM `classes` WHERE `id`='".$class."'");
								$fetch2 = mysql_fetch_array($query2);
								$tchers = json_decode($fetch2['teacher'],true);
			        			?>
			        			<tr>
			        				<td><?php print $fetch2['name'] ?></td>
			        				<td>
				        				<?php 
				        					foreach($tchers as $tc){
				        						print $user->fetchProfileInfo($tc,"firstName") . " " . $user->fetchProfileInfo($tc,"lastName");  
				        					}
				        				?>
			        				</td>
			        			</tr>
			        			<?php
			        				} 
			        			}
			        		}else{
			        			echo("<td>This user is not enrolled in any classes!</td><td></td>");
			        		}
			        			?>
					</table>		        	
				</div>
		        	<div id="contact" class="tab-pane fade">
		        		<dl class="dl-horizontal">
	 					<dt id="name9">Email</dt>
	 					<dd id="editable9"><?php print $user->fetchProfileInfo($profile, "email"); ?></dd>
	 					<dt id="name10">Website</dt>
	 					<dd id="editable10"><?php print $user->fetchProfileInfo($profile, "website"); ?></dd>
	 					<dt id="name11">IM</dt>
	 					<dd id="editable11"><?php print $user->fetchProfileInfo($profile, "im"); ?></dd>
	 					<dt id="name12">Phone Number</dt>
	 					<dd id="editable12"><?php print $user->fetchProfileInfo($profile, "phoneNumber"); ?></dd>
	 					<dt id="name13">Another Website</dt>
	 					<dd id="editable13"><?php print $user->fetchProfileInfo($profile, "website2"); ?></dd>
					</dl>
		        	</div>
		        </div>
		        </form>
		</div>
	</div>
</div>
<hr >
<?php
include "core/footer.php";
?>

<script src="http://<?php print $subdomain; ?>.aristocratlms.com/js/profile.js"></script>
<script src="http://<?php print $subdomain; ?>.aristocratlms.com/js/fileinput.js"></script>
<script>
	 $("#input-43").fileinput({
		uploadUrl: "http://<?php print $subdomain; ?>.aristocratlms.com/uploadProfilePicture.php",
		showPreview: false,
		maxFileCount: 1,
        allowedFileExtensions: ['jpg', 'png'],
        elErrorContainer: "#errordiv"
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
</body>
<?php
include "core/modal.php";
?>
</html>