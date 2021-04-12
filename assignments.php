<?php

session_start();
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
if(!$_GET['id']){

	header("location: 404.html");

	exit;

}

$id = $_GET['id'];

$startVal = $_GET['s'];

if($startVal == ""){

	$startVal = "0";

}

include "core/sqlconnect.php";

include "core/class.user.php";

$user = new user();


$user->inClass($id);

?>

<!doctype html>

<html>



<head>

<title>Student Portal</title>
<base href="http://<?php print $subdomain; ?>.aristocratlms.com/">
<link rel="stylesheet" type="text/css" href="style/bootstrap.min.css" />

<link rel="stylesheet" type="text/css" href="style/global.css" />

<link rel="stylesheet" type="text/css" href="style/style.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/s/bs/jq-2.1.4,dt-1.10.10,cr-1.3.0,r-2.0.0,rr-1.1.0/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="style/fileinput.css" type="text/css" />



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

					<a href="http://<?php print $subdomain ?>.aristocratlms.com/course/<?php print $id; ?>"><?php print $user->getClassInfo($id,"name"); ?></a>

				</li>

				<li class="active">Assignments</li>

			</ol>

		</div>


		<div class="row">

			<div class="col-md-12">

				<table id="assing" class="table table-striped">

					<thead>

						<tr>

							<?php

								if(!$user->isTeacher()){

							?>

							<th class="col-md-6" style="cursor:pointer;">Assignnment&nbsp;</th>

							<th class="col-md-3" style="cursor:pointer;">Time Assigned&nbsp;</th>

							<th class="col-md-3" style="cursor:pointer;">Due Date&nbsp;</th>

							<?php

								}

								else{

							?>

							<th class="col-md-5" style="cursor:pointer;">Assignment&nbsp;</th>

							<th class="col-md-3" style="cursor:pointer;">Time Assigned&nbsp;</th>

							<th class="col-md-3" style="cursor:pointer;">Due Date&nbsp;</th>

							<th class="col-md-1" class="no-sort"></th>

							<?php

									

								}

							?>

						</tr>

					</thead>

					<?php

						$queryTotal = mysql_query("SELECT * FROM `assignments` WHERE `class`='" . mysql_real_escape_string($id) . "' AND `delete`='0' ORDER BY `timeAssigned` DESC");

						$query = mysql_query("SELECT * FROM `assignments` WHERE `class`='" . mysql_real_escape_string($id) . "' AND `delete`='0' ORDER BY `timeAssigned` DESC LIMIT " . $startVal . ",10");

						while($fetch = mysql_fetch_array($query)){

							echo("<tr>");

								echo("<td data-sort-value=\"" .strtolower($fetch['name']) . "\" >");

									echo("<a href=\"assignment/" . $fetch['id'] . "\">" . $fetch['name'] . "</a>");

								echo("</td>");

								echo("<td data-sort-value=\"" . $fetch['timeAssigned'] . "\">");

									echo(date("F j Y, g:i a",$fetch['timeAssigned']));

								echo("</td>");

								echo("<td data-sort-value=\"" . $fetch['timeDue'] . "\">");

									echo(date("F j Y, g:i a",$fetch['timeDue']));

								echo("</td>");

								if($user->isTeacher()){

									echo("<td>");

										echo("<a href=\"editAssignment/" . $fetch['id'] . "&delete=true\"><span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span></a>&nbsp;<a href=\"editAssignment/" . $fetch['id'] . "\"><span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span></a>");

									echo("</td>");

								}

							echo("</tr>");

						}

					?>

				</table>

			</div>

		</div>

	</div>

</div>

<?php

include "core/footer.php";

?>

<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>-->
<script type="text/javascript" src="https://cdn.datatables.net/s/bs/dt-1.10.10,r-2.0.0/datatables.min.js"></script>
<script>
$(document).ready(function() {
	$("#assing").DataTable( {
		"order": [],
	    "columnDefs": [ {
	      "targets"  : 'no-sort',
	      "orderable": false,
	    }],
	    "dom": '<"panel-body"<"col-md-12"<"col-md-9"<"toolbar">><"col-md-3"<"well"fl>>>><"row"<"col-md-12"<t>>><"panel-footer"<"pull-left"i>p>'
	} );
	$("div.toolbar").html('<h3><?php print $user->getClassInfo($id,"name"); ?>: Assignments</h3>');
	$("div.wrapme").attr("style","border-bottom: #333;");

	$("div.dataTables_filter input").attr("Placeholder","Search");
});
</script>

</body>

<?php

include "core/modal.php";

?>

</html>