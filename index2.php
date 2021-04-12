<?php
session_start();
include "core/sqlconnect.php";
include "core/class.user.php";
$user = new user();
?>
<doctype html>
<html>

<head>
<title>Student Portal</title>
<link rel="stylesheet" type="text/css" href="style/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="style/global.css" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<!--
Website created, designed, and coded by: Leighton Tidwell;
-->
<script>

</script>
</head>
<body>
<?php
include "core/navigation.php";
$classes = $user->getUserInfo("classes");
$classes = explode(",",$classes);

foreach($classes as $class){
	if($class != ""){
	$query2 = mysql_query("SELECT * FROM `classes` WHERE `id`='".$class."'");
	$fetch2 = mysql_fetch_array($query2);
?>
<div class="panel panel-default" style="margin-left: 15px; margin-right: 15px">
	<div class="panel-heading">
		<h2 class="panel-title"><a href="#"><?php print $fetch2['name'];?></a> <small>First Period</small></h2>
		<span class="glyphicon glyphicon-chevron-down pull-right customChevron" data-toggle="collapse" data-target="#collapseClass<?php print $fetch2['id']; ?>" aria-expanded="false" aria-controls="collapseExample"></span>
	</div>
	<div class="panel-body collapse in" id="collapseClass<?php print $fetch2['id']; ?>">
		<div class="row">
			<div class="col-md-4">   
				<div id="<?php print $fetch2['id']; ?>" class="gradeCircle">
				        <strong></strong>
				</div>
			</div>
			<div class="col-md-8">
				<div class="alert alert-info" role="alert">
				<h4>Agenda</h4>
				<hr>
				<?php
					if($fetch2['agenda']){
						print $fetch2['agenda'];
					}
					else{
						print "There is no agenda for today.";
					}
				 ?>
				</div>
				<div class="alert alert-warning" role="alert">
					<h4>Upcoming Assignments</h4>
					<hr>
					<dl class="dl-horizontal">
	 					<dt>Assignment One</dt>
	 					<dd>There is no description for this assignment</dd>
	 					<dt>Assignment Two</dt>
	 					<dd>LORMELRJKEJ:FLKJS:L LKJF:SDLK JF:LJSD LFKJSD :LFKJSD: LKFJ:LSKDJ:FLKSJD</dd>
					</dl>
				</div>
				<div class="alert alert-danger alert-dismissable" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  					<span class="sr-only">Error:</span>
 					You have one assignment that needs to be turned in!
				</div>
			</div>
		</div>
	</div>
</div>
<hr>
<?} } ?>
<?php
include "core/footer.php";
?>
</div>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/circle-progress.js"></script>
<script>
$('.gradeCircle').circleProgress({
    value: .75,
    fill: { gradient: [['#0681c4', .5], ['#4ac5f8', .5]], gradientAngle: Math.PI / 4 }
}).on('circle-animation-progress', function(event, progress, stepValue) {
var d = String(stepValue.toFixed(2)).substr(1);
var s = d.replace(".", "");
var c = document.getElementById("test");

var context = c.getContext('2d');

context.font="80px font-family-base";
context.fillText(s + "%",60,150);
});
</script> 
</body>

</html>