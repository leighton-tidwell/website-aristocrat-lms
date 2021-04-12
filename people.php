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
<base href="http://<?php print $subdomain ?>.aristocratlms.com/">
<link rel="stylesheet" type="text/css" href="style/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="style/global.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/s/bs/jq-2.1.4,dt-1.10.10,cr-1.3.0,r-2.0.0,rr-1.1.0/datatables.min.css"/>
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
			People
		</div>

		<div class="row">
			<div class="col-md-12">
				<table class="table table-striped" id="people">
				<thead>
					<tr>
						<th class="no-sort"></th>
						<th>Name</th>
						<th>Role</th>
					</tr>
				</thead>
					<?php
						foreach($user->getUsers() as $userID){
							$query2 = mysql_query("SELECT * FROM `users` WHERE `id`='".mysql_real_escape_string($userID)."'");
							$fetch2 = mysql_fetch_array($query2);
						if($fetch2['firstName'] != ""){
					?>
					
					<tr>
						<td width="50px"><img width="50px" class="img-responive center-block" src="<?php print $fetch2['profilePicture']; ?>" /></td>
						<td><a href="http://<?php print $subdomain ?>.aristocratlms.com/profile/<?php print $fetch2['username'] ?>"><?php print $fetch2['firstName'] . " " . $fetch2['lastName']; ?></a></td>
						<td>Student</td>
					</tr>
					<?php
					} }
					?>				
				</table>
		</div>
	</div>
</div>
<?php
include "core/footer.php";
?>
</div>
	<!--<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/circle-progress.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/s/bs/dt-1.10.10,r-2.0.0/datatables.min.js"></script>-->
	<script>
	$(document).ready(function() {
		$("#people").DataTable( {
			"order": [],
		    "columnDefs": [ {
		      "targets"  : 'no-sort',
		      "orderable": false,
		    }],
		    "dom": '<"panel-body"<"col-md-12"<"col-md-9"<"toolbar">><"col-md-3"<"well"fl>>>><"row"<"col-md-12"<t>>><"panel-footer"<"pull-left"i>p>'
		} );
		<?php
			$school = $user->getUserInfo("school");
			$qrty = mysqL_query("SELECT * FROM `school` WHERE `id`='" . mysql_real_escape_string($school) . "'");
			$fetchty = mysql_fetch_array($qrty);
		?>
		$("div.toolbar").html('<h3>People of <?php print $fetchty['name']; ?></h3>');
		$("div.wrapme").attr("style","border-bottom: #333;");

		$("div.dataTables_filter input").attr("Placeholder","Search");
	});
	</script>
</body>
<?php
include "core/modal.php";
?>
</html>