$(document).ready(function() {
	$('#activity').dataTable( {
		"bJQueryUI": true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "/messages/ajax_list",
	});
});
