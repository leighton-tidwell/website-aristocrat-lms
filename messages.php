<?php

session_start();

include "core/sqlconnect.php";

include "core/class.user.php";
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
$user = new user();



$id = $_GET['id'];

?>

<!doctype html>

<html>



<head>

<title>Student Portal</title>
<base href="http://<?php print $subdomain; ?>.aristocratlms.com/">
<link rel="stylesheet" type="text/css" href="style/bootstrap.min.css" />

<link rel="stylesheet" type="text/css" href="style/global.css" />

<link rel="stylesheet" href="style/style.css" />





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
<script src="http://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
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

	<div class="panel panel-default" style="margin-left: 15px; margin-right: 15px">

		<div class="panel-heading">

			<ol class="breadcrumb panel-title" style="margin:0px; padding:0px;">

				<li>

					Inbox

				</li>

		</div>

		<div class="panel-body">

			<div class="row">

				<div class="col-md-4" style="border-right:1px solid #ddd;border-bottom:1px solid #ddd;border-bottom-right-radius:5px;">

					<a href="#">

						<div class="row text-center" style="font-size:24px;height:50px; border-bottom:1px solid #bce8f1;">

							<div class="col-md-12">

								<a style="cursor:pointer;" onclick="$('#composeMessage').modal();"><span class="glyphicon glyphicon-edit"></span> Compose a Message</a>

							</div>

						</div>

					</a>

					<br />

					<?php
						/*
						$messages = mysql_query("SELECT * FROM `messages` WHERE `to`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' ORDER BY `timestamp` DESC"); 
						$convos = array();
						while($fMessages = mysql_fetch_array($messages)){
							if(!in_array($fMessages['from'],$convos)){
								array_push($convos, $fMessages['from']);
							}
						}
						$messages2 = mysql_query("SELECT * FROM `messages` WHERE `from`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' ORDER BY `timestamp` DESC"); 
						while($fMessages2 = mysql_fetch_array($messages2)){
							if(!in_array($fMessages2['to'],$convos)){
								array_push($convos, $fMessages2['to']);
							}
						}
						foreach($convos as $convo){
							$message = mysql_query("SELECT * FROM `messages` WHERE (`to`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `from`='" . mysql_real_escape_string($convo) . "') OR (`from`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `to`='" . mysql_real_escape_string($convo) . "') ORDER BY `timestamp` DESC LIMIT 1"); 

							$recentMessage = mysql_fetch_array($message);

							echo("<a style=\"cursor:pointer;\" onclick=\"loadConvo(" . $convo . ");\">");

								echo("<div class=\"media\">");

									echo("<div class=\"media-left media-middle\">");

										echo("<img class=\"media-object\" height=\"50px\" src=\"" . $user->fetchProfileInfo($convo,"profilePicture") . "\">");

									echo("</div>");

									echo("<div class=\"media-body\">");

										echo("<h4 class=\"media-heading\">" . $user->fetchProfileInfo($convo,"firstName") . " " . $user->fetchProfileInfo($convo,"lastName") . "</h4>");

										echo($recentMessage['message']);

									echo("</div>");

								echo("</div>");

							echo("</a>");

							echo("<hr>");

						}
					*/
					// find messages from user to me
					$messages = mysql_query("SELECT * FROM `messages` WHERE `to`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' OR `from`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' ORDER BY `timestamp` DESC"); 
					if(mysql_num_rows($messages) == 0){
						echo("<div class=\"alert alert-info\">You have no messages.</div>");
					}
					else{
					// find messages from me to user
					// $to = mysql_query("SELECT * FROM `messages` WHERE `to`='" . mysql_real_escape_string($id) . "' AND `from`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' ORDER BY `timestamp` DESC");

					$convoIDs = array();
					while($fMessages = mysql_fetch_array($messages)){
						$convoIDs[$fMessages['id']] = $fMessages['timestamp'];
					}


					// while($two = mysql_fetch_array($to)){
						// $convoIDs[$two['id']] = $two['timestamp'];
					// }

					arsort($convoIDs, SORT_NUMERIC);
					$lePoo = array();
						foreach(array_keys($convoIDs) as $cvr){
							$queryMes = mysql_query("SELECT * FROM `messages` WHERE `id`='" . mysql_real_escape_string($cvr) . "'");
							$FETCHHH = mysql_fetch_array($queryMes);

							if($FETCHHH['from'] == $user->getUserInfo("id")){
								$FETCHHH['from'] = $FETCHHH['to'];
							}
							
							if(!in_array($FETCHHH['from'],$lePoo)){
								echo("<a style=\"cursor:pointer;\" onclick=\"loadConvo(" . $FETCHHH['from'] . ");\">");

								echo("<div class=\"media\">");

									echo("<div class=\"media-left media-middle\">");

										echo("<img class=\"media-object\" height=\"50px\" width=\"50px\" src=\"" . $user->fetchProfileInfo($FETCHHH['from'],"profilePicture") . "\">");

									echo("</div>");

									echo("<div class=\"media-body\">");

										echo("<h4 class=\"media-heading\">" . $user->fetchProfileInfo($FETCHHH['from'],"firstName") . " " . $user->fetchProfileInfo($FETCHHH['from'],"lastName") . "</h4>");

										echo($FETCHHH['message']);

									echo("</div>");

								echo("</div>");

							echo("</a>");

							echo("<hr>");
							}
							array_push($lePoo,$FETCHHH['from']);
						}
					}
					?>

				</div>

				<div class="col-md-8">

					<div id="messageContainer" style="max-height:400px;overflow-y:auto;overflow-x:hidden;">



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
-->
	<script>

		function loadConvo(cid){

			var check = false;

			if($("#messageBox-" + cid + "").val()){

				var stringers = $("#messageBox-" + cid + "").val();	

				check = true;

			}

			$("#messageContainer").load("conversation.php?id=" + cid + "", function(){

				var height = 0;

				$('#messageContainer div').each(function(i, value){

					height += parseInt($(this).height());

				});



				height += '';

				if(check == true){

					$("#messageBox-" + cid + "").val("" + stringers + "");

				}

				$('#messageContainer').animate({scrollTop: height}, 800);



			});

			console.log("updating..");

			setTimeout(function() { timeMe(cid); }, 60000);

		}

		function timeMe(cid){

			loadConvo(cid);

		}

		function sendMessage(to){

			var message = $("#messageBox-" + to + "").val();

			$.post( "sendMessage.php?id=" + to + "", { 'message': message }).done(function(data){

				$("#messageBox-" + to + "").val("");

				loadConvo(to);

			});

		}

	</script>

	<?php

		if($id){

			?>

			<script>loadConvo(<?php print $id; ?>);</script>

			<?php

		}

	?>

</body>

<?php

include "core/modal.php";

?>

</html>