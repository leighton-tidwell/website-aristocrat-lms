function markRead(varchi){
	// $("#not-" + id + "").slideUp();
	if(varchi == "notifications"){
		$.get("./markRead.php?notifications=true", function(data){ console.log(data); });
		$("#notbox").switchClass("panel-info","panel-default",2000);
		$("#notCount").text("");
	}
	else if(varchi == "messages"){
		$.get("./markRead.php?messages=true", function(data){ console.log(data); });
		$("#msgBoxr").switchClass("panel-info","panel-default",2000);
		$("#messBadge").text("");
	}
	/* var lenght = $('div[id^="not-"]:visible').length - 1;
	if(lenght == 0){
		var html = "<div class='alert alert-success'>There are no more notifications!</div>";
		$("#notificationsContainer").append(html);
	}
	$("#notCount").text($("#notCount").text()-1);
	if($("#notCount").text() == "0"){
		$("#notCount").text("");
	}
	*/
}
$("#notifications").click(function(event){
     event.stopPropagation();
});
$("#messages").click(function(event){
     event.stopPropagation();
});
function sendMsg(){
	var user = $("#chooseUser").val();
	var message = $("#msgComp").val();
	$.post( "sendMessage.php?id=" + user + "", { 'message': message }).done(function(data){
		$("#msgComp").val("");
		$("#chooseUser").val("");
		$("#composeMessage").modal("hide");
		window.location = "messages.php?id=" + user + "";		
	});
}

function addCourse(){
	var courseCode = $("#classCode").val();
	$.post( "addCourse.php", {'code': courseCode}).done(function(data){
		console.log(data);
		$("#classCode").val("");
		$("#addCourse").modal("hide");
		window.location = "index.php";
	});
}