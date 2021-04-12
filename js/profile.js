function changeClass(){
	if(document.getElementById("edit").className == "btn btn-success"){
		document.getElementById("QUOTE").value = document.getElementById("quoteForm").value;
		document.getElementById("PROFILEPICTURE").value = document.getElementById("urlForm").value;
		document.getElementById("edit").className = "btn btn-primary";
		$( "#profileForm" ).submit();
	}
	else{
		document.getElementById("edit").className = "btn btn-success";
		document.getElementById("profilePicture").src = "http://cicero-movie.edu.helsinki.fi/website/img/edit.png";
		$("#editProfilePicture").attr("data-toggle", "modal");

	}
}
$("#edit").click( function(){
			$("#editable").replaceWith(function() {
				var yes = $("#name").text();
				var str = yes.replace(/\s+/g, '');
				$("#name").replaceWith(function(){
					return $("<h4>").text(str);
				});
				return $("<textarea class='form-control' name='" + str + "'>").text(this.innerHTML);
			});
			$("#editable2").replaceWith(function() {
				var yes = $("#name2").text();
				var str = yes.replace(/\s+/g, '');
				$("#name2").replaceWith(function(){
					return $("<h4>").text(str);
				});
				return $("<textarea class='form-control' name='" + str + "'>").text(this.innerHTML);
			});	
			$("#editable3").replaceWith(function() {
				var yes = $("#name3").text();
				var str = yes.replace(/\s+/g, '');
				$("#name3").replaceWith(function(){
					return $("<h4>").text(str);
				});
				return $("<textarea class='form-control' name='" + str + "'>").text(this.innerHTML);
			});
			$("#editable4").replaceWith(function() {
				var yes = $("#name4").text();
				var str = yes.replace(/\s+/g, '');
				$("#name4").replaceWith(function(){
					return $("<h4>").text(str);
				});
				return $("<textarea class='form-control' name='" + str + "'>").text(this.innerHTML);
			});
			$("#editable5").replaceWith(function() {
				var yes = $("#name5").text();
				var str = yes.replace(/\s+/g, '');
				$("#name5").replaceWith(function(){
					return $("<h4>").text(str);
				});
				return $("<textarea class='form-control' name='" + str + "'>").text(this.innerHTML);
			});
			$("#editable6").replaceWith(function() {
				var yes = $("#name6").text();
				var str = yes.replace(/\s+/g, '');
				$("#name6").replaceWith(function(){
					return $("<h4>").text(str);
				});
				return $("<textarea class='form-control' name='" + str + "'>").text(this.innerHTML);
			});
			$("#editable7").replaceWith(function() {
				var yes = $("#name7").text();
				var str = yes.replace(/\s+/g, '');
				$("#name7").replaceWith(function(){
					return $("<h4>").text(str);
				});
				return $("<textarea class='form-control' name='" + str + "'>").text(this.innerHTML);
			});
			$("#editable8").replaceWith(function() {
				var yes = $("#name8").text();
				var str = yes.replace(/\s+/g, '');
				$("#name8").replaceWith(function(){
					return $("<h4>").text(str);
				});
				return $("<textarea class='form-control' name='" + str + "'>").text(this.innerHTML);
			});
			$("#editable9").replaceWith(function() {
				var yes = $("#name9").text();
				var str = yes.replace(/\s+/g, '');
				$("#name9").replaceWith(function(){
					return $("<h4>").text(str);
				});
				return $("<textarea class='form-control' name='" + str + "'>").text(this.innerHTML);
			});
			$("#editable10").replaceWith(function() {
				var yes = $("#name10").text();
				var str = yes.replace(/\s+/g, '');
				$("#name10").replaceWith(function(){
					return $("<h4>").text(str);
				});
				return $("<textarea class='form-control' name='" + str + "'>").text(this.innerHTML);
			});
			$("#editable11").replaceWith(function() {
				var yes = $("#name11").text();
				var str = yes.replace(/\s+/g, '');
				$("#name11").replaceWith(function(){
					return $("<h4>").text(str);
				});
				return $("<textarea class='form-control' name='" + str + "'>").text(this.innerHTML);
			});
			$("#editable12").replaceWith(function() {
				var yes = $("#name12").text();
				var str = yes.replace(/\s+/g, '');
				$("#name12").replaceWith(function(){
					return $("<h4>").text(str);
				});
				return $("<textarea class='form-control' name='" + str + "'>").text(this.innerHTML);
			});
			$("#editable13").replaceWith(function() {
				var yes = $("#name13").text();
				var str = yes.replace(/\s+/g, '');
				$("#name13").replaceWith(function(){
					return $("<h4>").text(str);
				});
				return $("<textarea class='form-control' name='" + str + "'>").text(this.innerHTML);
			});
			$("#editable14").replaceWith(function() {
				return $("<textarea class='form-control' id='quoteForm' name='quote'>").text(this.innerHTML);
			});
		});