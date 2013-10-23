(function($) {
	$.fn.extend( {
		limiter: function(limit, elem) {
			$(this).on("keyup focus", function() {
				setCount(this, elem);
			});
			function setCount(src, elem) {
				var chars = src.value.length;
				if (chars > limit) {
					src.value = src.value.substr(0, limit);
					chars = limit;
				}
				elem.html( limit - chars );
			}
			setCount($(this)[0], elem);
		}
	});
})(jQuery);

$(document).ready(function() {
	// Prevent Form Enter Submit
	$('input,select').keypress(function(event) { return event.keyCode != 13; });

	// Limit Message
	var elem = $("#chars");
	$("#message").limiter(120, elem);

	// Email + Title
	$('#type\\[email\\]').change(function() {
		if($(this).is(":checked")) {
			$('#rowTitle').show();
		} else {
			$('#rowTitle').hide();
		}
	});
	if($('#type\\[email\\]').is(":checked")) {
		$('#rowTitle').show();
	}

	// Next Button
	$('#btnNext').click(function() {
		$("a[href='#panel2']").click();
		calculate();
		return false;
	});
	$("a[href='#panel2']").click(function() {
		calculate();
	});
});

function calculate(){
	$.post("<?php echo URL::site().'messages/ajax_calculate'; ?>",
		$("form").serialize(),
		function(data) {
			$('#rowCalculating').hide();
			$('#rowConfirm').show();
			$('#txtMessage').html($('form #message').val());
			$('#txtSMS').html(data.sms);
			$('#txtEmail').html(data.email);
			$('#txtCost').html(data.cost);
		}, "json"
	);
}