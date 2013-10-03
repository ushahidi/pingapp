$(document).ready(function() {
	$('#activity').dataTable( {
		"bJQueryUI": true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "/people/ajax_list?group_id=<?php echo $group_id; ?>",
		"aaSorting": [[ 0, "asc" ]],
	});
});