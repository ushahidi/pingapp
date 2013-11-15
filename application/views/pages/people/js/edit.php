$(document).ready(function() {
	$('.add-contact-button').click(function() {
		var clone = $('.contact-info-row:last').clone(true);
		clone.find(':input').each(function(){
			if ($(this).attr('type') == 'hidden') {
				$(this).remove();
				return true;
			};

			var id = parseInt($(this).attr('name').replace(/\D/g, ''));
			$(this).attr('name', $(this).attr('name').replace(/\d+/, id + 1) );
			$(this).val('');
		});
		clone.insertBefore($('.add-contact'));
		clone.find('.contact-type').trigger('change', true);
		
		restoreInput(clone.find('.contact-account'), clone);
	});

	// Delete a Contact
	$('.ping-del-contact').click(function(){
		var parent = $(this).parent().parent();
		parent.remove();

		var hidden = parent.find('input[type="hidden"]').val().replace(/\D/g,'');
		if (hidden) {
			$('<input>').attr({
				type: 'hidden',
				name: 'delete[]',
				value: hidden
			}).appendTo('#personForm');
		};
	});

	// Initialize International Numbers OnLoad
	$('.contact-info-row').each(function(){
		if ($(this).find('.contact-type').val() == 'phone'){
			intTel($(this).find('.contact-account'));
		}
	});

	// Update input on dropdown change
	$('.contact-type').change(function(){
		var parent = $(this).parent().parent();
		var field = parent.find('.contact-account');
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
	if ($(field).val().charAt(0) == '+') {
		$(field).val($(field).val().substring(1, $(field).val().length));
	}
	parent.find('.contact-info-account').remove();

	var newDiv = $('<div>').attr({
		class: 'contact-info-account',
	});
	field.appendTo(newDiv);
	newDiv.insertBefore(parent.find('.remove-contact'));
}
