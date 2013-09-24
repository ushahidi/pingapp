$(document).ready(function() {
	$("#ping-add-contact").click(function() {
		var clone = $('.ping-contact').clone(true);

		var c = 1;
		var newID = ++c;
		clone.insertBefore($("#ping-add-contact"));
	});

	$("#ping-del-contact").click(function(){
		$(this).parent().parent().parent().remove();
	});	
});