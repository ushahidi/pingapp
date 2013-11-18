$(document).ready(function() {
	$('#sms').dataTable( {
		"bJQueryUI": true,
		"bProcessing": true,
		"bServerSide": true,
		"aaSorting": [[ 2, "desc" ]],
		"sAjaxSource": "/messages/ajax_list?type=sms",
	});

	$('#email').dataTable( {
		"bJQueryUI": true,
		"bProcessing": true,
		"bServerSide": true,
		"aaSorting": [[ 2, "desc" ]],
		"sAjaxSource": "/messages/ajax_list?type=email",
	});
});
