<?php
session_start();
$id = $_GET['id'];
include "core/sqlconnect.php";
include "core/class.user.php";
$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
$user = new user();

if($_GET['date']){
	$date = explode("-", $_GET['date']);
	$year = $date[0];
	$month = $date[1];
	$day = $date[2];
	
	if(strlen($month) == 1){
		$month = "0" . $month;
	}
	if(strlen($day) == 1){
		$day = "0" . $day;
	}
}
$class = $_GET['class'];
$user->inClass($class);
$query = mysql_query("SELECT * FROM `agendas` WHERE `day`='".mysql_real_escape_string($day)."' AND `month`='".mysql_real_escape_string($month)."' AND `year`='".mysql_real_escape_string($year)."' AND `class`='".mysql_real_escape_string($class)."'") or die(mysql_error());
$fetch = mysql_fetch_array($query);
$url = "year=".$year."&month=".$month."&day=".$day."&class=".$class."";

?>
<doctype html>
<html>

<head>
<title>Student Portal</title>
<base href="http://<?php print $subdomain ?>.aristocratlms.com/">
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
	<div class="row">
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Calendar</h3>
			  	</div>
				<div class="panel-body">
			   		<div class="jquery-calendar"></div>
				</div>
			</div>
		</div>
		<div class="col-md-9">
			<div class="panel panel-default">
				<div class="panel-heading">
					<ol class="breadcrumb panel-title" style="margin:0px; padding:0px;">
                        <li>
                            <a href="http://<?php print $subdomain ?>.aristocratlms.com/course.php?id=<?php print $user->getClassInfo($_GET['class'],"id"); ?>"><?php print $user->getClassInfo($_GET['class'], "name"); ?></a>
                        </li>
                        <li class="active">
                        	Agenda for <?php print $user->monthName($month)." ".$day.", ".$year; ?>
                        </li>
                	</ol>
				</div>
				<div class="panel-body">
				
					<?php
					if($user->isTeacher()){
				?>
					<h2>Agenda</h2>
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
			<?php
				print $fetch['text'];
			?>
		</div>
	</div>
</div>
<form method="POST" id="agendaForm" action="updateAgenda.php?<?php print $url;?>">
<input type="hidden" id="agendaField" name="agenda">
</form>
<button type="button" id="updateAgenda" class="btn btn-primary">Update Agenda</button>
					<?php
						}
						else{
					?>	
						<h2>Agenda</h2>
						<div class="well">
							<?php
								if($fetch['text']){
									print $fetch['text'];
								}
								else
								{
									print "There is no agenda for today.";
								}
								
							?>
						</div>
					<?php
						}
					?>
					
				</div>
			</div>
		</div>
</div>
<?php
include "core/footer.php";
?>
<!--	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/calendar.js"></script> -->
	<?php
		if($user->isTeacher()){
	?>
		<link rel="stylesheet" href="style/quill.snow.css" />
		<!--<script src="js/quill.min.js"></script>-->
		<!-- Initialize Quill editor -->
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
$( "#clickkker" ).click(function() {
  var htmlString = $( "#ql-editor-1" ).html();
  $( this ).text( htmlString );
});
$( "#updateAgenda" ).click(function() {
  var htmlString = fullEditor.getHTML();
  $( "#agendaField" ).val(htmlString);
  $( "#agendaForm" ).submit();
});
		</script>
	<?php
		
		}
	?>
</body>
</html>