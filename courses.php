<?php
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
$user = new user();
?>
<doctype html>
<html>

<head>
<title>Student Portal</title>
<base href="http://<?php print $subdomain ?>.aristocratlms.com/">
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
<script>

</script>
</head>
<body>
<?php
include "core/navigation.php";
?>
<div class="container">
	<div class="row">
    	<div class="panel panel-default">
            <div class="panel-heading">
            <span class="pull-right" style="margin-top:-8px;">
	                <select onchange="changeSemester();" id="semChooser" class="form-control" id="changeSemester">
	                	<?php
	                		$semie = mysql_query("SELECT * FROM `semester` WHERE `startTime` < '" . time() . "' AND `endTime` > '" . time() . "' AND `school`='" . mysql_real_escape_string($user->getUserInfo("school")) . "'");
							$femie = mysql_fetch_array($semie);
							$semester = $femie['id'];

							echo("<option value=\"" . $semester . "\">" . $femie['name'] . "</option>");
	                	?>
	                </select>
	            </span>
                Courses
            </div>
        <div class="panel-body">
            <div class="col-md-4">	
                <img src="<?php print $user->getUserInfo("profilePicture"); ?>" alt="profile_picture" class="img-responsive img-rounded">
            </div>
            <div class="col-md-8">
                <h1><?php print $user->getUserInfo("firstName") . " " . $user->getUserInfo("lastName"); ?>'s Classes</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped" id="courseTable">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Teacher</th>
                        </tr>
                    </thead>
                    <?php
                    $classes = $user->getUserInfo("classes");
                    if($classes != ""){
                    $classes = json_decode($classes,true);
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
                        <td><a href="http://<?php print $subdomain ?>.aristocratlms.com/course/<?php print $fetch2['id'] ?>"><?php print $fetch2['name'] ?></a></td>
                        <td>
                            <?php 
                                foreach($tchers as $tc){
                                    print $user->fetchProfileInfo($tc,"firstName") . " " . $user->fetchProfileInfo($tc,"lastName");  
                                }
                            ?>
                        </td>
                    </tr>
                    <?php
                        } } }
                        else{
                            ?>
                                <td>You do not have any classes.</td>
                                <td></td>
                            <?php
                        }
                    ?>
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
</body>
<?php
include "core/modal.php";
?>
</html>