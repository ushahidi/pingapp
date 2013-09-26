$(document).ready(function() {
	$('#activity').dataTable( {
		"bJQueryUI": true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "/person/ajax_list",
		"aaSorting": [[ 0, "asc" ]],
	});
});
