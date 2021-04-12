<nav class="navbar navbar-default navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="http://<?php print $subdomain; ?>.aristocratlms.com/index">
            	<?php
					$user->getSchool($user->getUserInfo("school"));
				?>
            </a>
		</div>
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
			<?php
 				$page = basename($_SERVER['PHP_SELF']);
			if($page == "index.php"){
				$index = true;
			}
			if($page == "grades.php"){
				$grades = true;
			}
			if($page == "people.php"){
				$people = true;
			}
			?>
				<li <?php if($index){ ?>class="active"<?php } ?>><a href="http://<?php print $subdomain; ?>.aristocratlms.com/index">Home <?php if($index){ print "<span class=\"sr-only\">(current)</span>"; } ?></a></li>
				<li <?php if($grades){ ?>class="active"<?php } ?>><a href="http://<?php print $subdomain; ?>.aristocratlms.com/grades">Grades </a></li>
				<li <?php if($people){ ?>class="active"<?php } ?>><a href="http://<?php print $subdomain; ?>.aristocratlms.com/people">People <?php if($people){ print "<span class=\"sr-only\">(current)</span>"; } ?></a></li>
				<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Courses <span class="caret"></span></a>
				<ul class="dropdown-menu" role="menu" style="min-width:200px">
				<?php
				$classes = $user->getUserInfo("classes");
				// sort them
				$semie = mysql_query("SELECT * FROM `semester` WHERE `startTime` < '" . time() . "' AND `endTime` > '" . time() . "' AND `school`='" . mysql_real_escape_string($user->getUserInfo("school")) . "'");
				$femie = mysql_fetch_array($semie);
				$semester = $femie['id'];
				// get current primary semester

				//now lets get current subsemester
				$subsemie = mysql_query("SELECT * FROM `subsemester` WHERE `startTime` < '" . time() . "' AND `endTime` > '" . time() . "' AND `semid`='" . mysql_real_escape_string($semester) . "'");
				$subfemie = mysql_fetch_array($subsemie);
				$subsemester = $subfemie['id'];

				if($classes != ""){
				$classes = json_decode($classes,true);
				
				ksort($classes[$semester][$subsemester]);
				foreach($classes[$semester][$subsemester] as $period=>$class){
					if($class != ""){
					$query2 = mysql_query("SELECT * FROM `classes` WHERE `id`='".$class."'");
					$fetch2 = mysql_fetch_array($query2);
				?>
					<li><a href="http://<?php print $subdomain; ?>.aristocratlms.com/course/<?php print $fetch2['id']; ?>"><?php print $fetch2['name']; ?></a></li>
				<?php
				} } } 
				else{
					echo("<li>You are not enrolled in a class.</li>");
				}
				?>
                <li class="divider" role="separator"></li>
                <li></li>
				<li style="padding-right: 10px; padding-left:10px;"><span onclick="$('#addCourse').modal();" class="btn btn-primary btn-xs pull-right"><span class="glyphicon glyphicon-plus"></span>&nbsp;Add a Course</span><span class="pull-left"><a href="http://<?php print $subdomain; ?>.aristocratlms.com/courses"><button type="button" class="btn btn-success btn-xs">View All</button></a></span></li>
				</ul>
				</li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
			<?php $lastname = $user->getUserInfo("lastName"); ?>
				<span class="signedIn"><p class="navbar-text">Welcome, <a href="http://<?php print $subdomain; ?>.aristocratlms.com/profile/<?php print $_SESSION['username']; ?>" class="navbar-link"><?php print $user->getUserInfo("firstName") . " " . $lastname[0] ?>.</a></p></span>
				<li class="dropdown">
				<?php
						$qMes = mysql_query("SELECT * FROM `messages` WHERE `to`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' or `from`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' ORDER BY `timestamp` DESC");
						$qNum = mysql_num_rows($qMes);
						$qr = mysql_query("SELECT * FROM `messages` WHERE `to`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' AND `read`='0' ORDER BY `timestamp` DESC");
						$qrum = mysql_num_rows($qr);
						
						// find messages from user to me
						$messages = mysql_query("SELECT * FROM `messages` WHERE `to`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' OR `from`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' ORDER BY `timestamp` DESC"); 

						// find messages from me to user
						// $to = mysql_query("SELECT * FROM `messages` WHERE `to`='" . mysql_real_escape_string($id) . "' AND `from`='" . mysql_real_escape_string($user->getUserInfo("id")) . "' ORDER BY `timestamp` DESC");

						$convoIDs = array();
						while($fMessages = mysql_fetch_array($messages)){
							$convoIDs[$fMessages['id']] = $fMessages['timestamp'];
						}

					$findUnreadms = mysql_query("SELECT * FROM `messages` WHERE `to`='" . $user->getUserInfo("id") . "' AND `read`='0'");
					$unreadms = mysql_num_rows($findUnreadms);
					if($unreadms == "0"){
						$unreadms = "";
					}
						// while($two = mysql_fetch_array($to)){
							// $convoIDs[$two['id']] = $two['timestamp'];
						// }

						arsort($convoIDs, SORT_NUMERIC);
				?>
				<a href="#" class="dropdown-toggle" onclick="markRead('messages');" data-toggle="dropdown" role="button" aria-expanded="false"><span class="glyphicon glyphicon-envelope"></span><span id="messBadge" class="badge"><?php print $unreadms; ?></span></a>
				<ul class="dropdown-menu media-list" id="messages" style="min-width:400px;max-height:400px;overflow-y:auto;overflow-x:hidden;padding: 5px;" role="menu">
					<li><span class="pull-left"><a href="messages"><span class="glyphicon glyphicon-envelope"></span></a></span><span class="pull-right"><a style="cursor:pointer;" data-toggle="modal" data-target="#composeMessage" ><span class="glyphicon glyphicon-edit"></span></a></span></li>
					<li role="separator" class="divider"></li>
					<?php
						if($qNum == "0"){
							echo("<div class=\"alert alert-info\">No messages!</div>");
						}
						$lePoo = array();
						foreach(array_keys($convoIDs) as $cvr){
							$queryMes = mysql_query("SELECT * FROM `messages` WHERE `id`='" . mysql_real_escape_string($cvr) . "'");
							$FETCHHH = mysql_fetch_array($queryMes);

							if($FETCHHH['from'] == $user->getUserInfo("id")){
								$FETCHHH['from'] = $FETCHHH['to'];
							}
							
							if(!in_array($FETCHHH['from'],$lePoo)){
								if($FETCHHH['read'] == 0 && $FETCHHH['to'] == $user->getUserInfo("id")){
									$panel = "info";
								}
								else{
									$panel = "default";
								}
							?>
								<li>
									<div id="msgBoxr" class="panel panel-<?php print $panel; ?>">
									  <div class="panel-heading" style="padding:3px;"><?php print $user->fetchProfileInfo($FETCHHH['from'],"firstName") . " " . $user->fetchProfileInfo($FETCHHH['from'],"lastName"); ?></div>
										  <a href="messages/<?php print $FETCHHH['from']; ?>"><div class="panel-body">
											<?php print $FETCHHH['message'] ?>
										  </div></a>
										<div class="panel-footer" style="padding:2px;font-size:10px;">Sent at <?php print date("F j Y, g:i a",$FETCHHH['timestamp']); ?></div>
									</div>
								</li>
							<?php
							}
						array_push($lePoo,$FETCHHH['from']);
						}
					?>
					
					
					<li role="separator" class="divider"></li>
					<li><a href="#">See more...</a></li>
				</ul>
				</li>
				
				<li class="dropdown pull-left">
				<?php
					$findUnreadNots = mysql_query("SELECT * FROM `notifications` WHERE `uid`='" . $user->getUserInfo("id") . "' AND `read`='0'");
					$findNots = mysql_query("SELECT * FROM `notifications` WHERE `uid`='" . $user->getUserInfo("id") . "' ORDER BY `timestamp` DESC");
					$unread = mysql_num_rows($findUnreadNots);
					if($unread == "0"){
						$unread = "";
					}
				?>
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" onclick="markRead('notifications');" role="button" aria-expanded="false"><span class="glyphicon glyphicon-bell"></span><span id="notCount" class="badge"><?php print $unread; ?></span></a>
				<ul class="dropdown-menu" id="notifications" style="min-width:400px;max-height:400px;padding:5px;overflow-y:auto;overflow-x:hidden;border-bottom-right-radius:10px;border-bottom-left-radius:10px;" role="menu">
					<div id="notificationsContainer">
					<?php
						if(mysql_num_rows($findNots) == 0){
							echo("<li><div class=\"alert alert-info\">You have no notifications.</div></li>");
						}
						else{
						while($fetchNots = mysql_fetch_array($findNots)){
							echo("<li>");
								if($fetchNots['read'] == 0){
									echo("<div id=\"notbox\" class=\"panel panel-info\">");
										echo("<div class=\"panel-heading\">");
											echo(date("F j Y, g:i a",$fetchNots['timestamp']));
											echo("");
										echo("</div>");
										echo("<div class=\"panel-body\">");
											echo($fetchNots['string']);
										echo("</div>");
									echo("</div>");
								}	
								else{
									echo("<div id=\"not-" . $fetchNots['id'] . "\" class=\"panel panel-default\">");
										echo("<div class=\"panel-heading\">");
											echo(date("F j Y, g:i a",$fetchNots['timestamp']));
											echo("<span style=\"cursor:pointer;\" class=\"pull-right glyphicon glyphicon-remove\"></span>");
										echo("</div>");
										echo("<div class=\"panel-body\">");
											echo($fetchNots['string']);
										echo("</div>");
									echo("</div>");
								}
							echo("</li>");
						}
							}
						echo("</div>");
						echo("");
							// echo("<a href=\"#\" style=\"text-align:center\"><div class=\"panel panel-default\">View More</div></a>");
						echo("");
					?>

				</ul>
				</li>
				<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><span class="glyphicon glyphicon-cog settingsButton"></span></a>
				<ul class="dropdown-menu" role="menu">
					<?php
						if($user->isTeacher()){
					?>
					<li><a href="editSchool">School Configuration</a></li>
					<?php
						}
					?>
					<li><a href="http://<?php print $subdomain; ?>.aristocratlms.com/profile/<?php print $_SESSION['username']; ?>">My Profile</a></li>
					<li><a href="http://<?php print $subdomain; ?>.aristocratlms.com/settings">Settings</a></li>
					<li><a href="#">Help</a></li>
				</ul>
				</li>
				
				<li><button onClick="window.location.assign('http://<?php print $subdomain; ?>.aristocratlms.com/logout')" type="button" class="btn btn-default navbar-btn navbar-right logoutButton">Logout</button></li>
			</ul>
		</div>
	</div>
</nav>






