<div class="modal fade" id="composeMessage" tabindex="-1" role="dialog" aria-labelledby="messagecompLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="messagecompLabel">Send a Message</h4>
			</div>
			<div class="modal-body">
				<h3>To:</h3>
				<select multiple="false" name="to" id="chooseUser" style="width:100%;" class="form-control select2">				
					<?php
						$school = $user->getUserInfo("school");
						$query = mysql_query("SELECT * FROM `users` WHERE `school`='".mysql_real_escape_string($school)."'");
						while(($fetch = mysql_fetch_array($query)) != NULL){
							if($fetch['id'] != $user->getUserInfo("id")){
								echo("<option value=\"" . $fetch['id'] . "\">" . $fetch['firstName']. " " . $fetch['lastName'] . "</option>");
							}
						}
					?>
				</select>
				<br />
				<h3>Message</h3>
				<textarea class="form-control" id="msgComp"></textarea>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-success" onclick="sendMsg();">Send Message</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="addCourse" tabindex="-1" role="dialog" aria-labelledby="addcourseLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="messagecompLabel">Add A Course</h4>
			</div>
			<div class="modal-body">
				<h3>Course Code</h3>
				<small>Input your course code to enroll:</small>
				<input type="text" class="form-control" id="classCode" />
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-success" onclick="addCourse();">Add Course</button>
			</div>
		</div>
	</div>
</div>
<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet" />
<script>
$(function(){
	$('#chooseUser').select2({
		maximumSelectionLength: 1
	});
});
</script>