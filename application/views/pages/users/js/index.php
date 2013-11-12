$(document).ready(function() {
	$('#activity').dataTable( {
		"bJQueryUI": true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "/users/ajax_list",
		"aaSorting": [[ 4, "desc" ]],
		"iDisplayLength": 50
	});
});