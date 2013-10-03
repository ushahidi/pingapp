$(document).ready(function() {
	var c = 0;
	// Clone the Contact DIV
	$('#ping-add-contact').click(function() {
		var clone = $('#contact\\[0\\]').clone(true);
		var newID = ++c;
		
		clone.attr('id', clone.attr('id').replace(/\d+/, newID) );
		clone.find(':input').each(function(){
			if ($(this).attr('type') == 'hidden') {
				$(this).remove();
				return true;
			};
			$(this).attr('id', $(this).attr('id').replace(/\d+/, newID) );
			$(this).attr('name', $(this).attr('name').replace(/\d+/, newID) );
			$(this).val('');
		});

		clone.insertBefore($('#ping-add-contact'));
	});

	// Delete a Contact
	$('.ping-del-contact').click(function(){
		var parent = $(this).parent().parent().parent();
		if (parent.attr('id') != 'contact[0]') {
			parent.remove();
		};

		var hidden = parent.find('input[type="hidden"]').val().replace(/\D/g,'');
		if (hidden) {
			$('<input>').attr({
				type: 'hidden',
				name: 'delete[]',
				value: hidden
			}).appendTo('#personForm');
		};
	});

	var i = 0;
	// Initialize International Numbers OnLoad
	$('.contact-info-row').find(':input').each(function(){
		if ($(this).hasClass('contact-type') && $(this).val() == 'phone') {
			intTel($('#contact\\['+i+'\\]\\[contact\\]'));
			i++;
		};
	});

	// Update input on dropdown change
	$('.contact-type').change(function(){
		var parent = $(this).parent().parent();
		var field = parent.find('input')[0];
		if ($(this).val() == 'phone') {
			intTel(field);
		} else {
			restoreInput(field, parent);
		}
	});
});

// Add International Selector
function intTel(field){
	if ($(field).val().charAt(0) != '+') {
		if ($(field).val().length == 0) {
			$(field).val('+254'+ $(field).val());
		} else {
			$(field).val('+'+ $(field).val());
		}
	};
	$(field).intlTelInput({
		preferredCountries: ["KE", "US"],
		americaMode: false
	});
}

// Restore Original Input
function restoreInput(field, parent){
	parent.children().eq(1).html(field);
}