$(document).ready(function() {
	$('#pings').dataTable( {
		"bJQueryUI": true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "/pings/ajax_list?person_id=<?php echo $person->id; ?>",
		"aaSorting": [[ 2, "desc" ]],
	});
	$('#pongs').dataTable( {
		"bJQueryUI": true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "/pongs/ajax_list?person_id=<?php echo $person->id; ?>",
		"aaSorting": [[ 2, "desc" ]],
	});
	$('#secondary').dataTable( {
		"bJQueryUI": true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "/people/ajax_list?person_id=<?php echo $person->id; ?>",
		"aaSorting": [[ 0, "asc" ]],
	});
});