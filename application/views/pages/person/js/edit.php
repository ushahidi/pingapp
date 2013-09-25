$(document).ready(function() {
	var c = 0;
	$('#ping-add-contact').click(function() {
		var clone = $('#contact\\[0\\]').clone(true);
		var newID = ++c;
		
		clone.attr('id', clone.attr('id').replace(/\d+/, newID) );
		clone.find(':input').each(function(){
			$(this).attr('id', $(this).attr('id').replace(/\d+/, newID) );
			$(this).attr('name', $(this).attr('name').replace(/\d+/, newID) );
		});

		clone.insertBefore($('#ping-add-contact'));
	});

	$('.ping-del-contact').click(function(){
		var parent = $(this).parent().parent().parent();
		if (parent.attr('id') != 'contact[0]') {
			parent.remove();
		};
	});
});