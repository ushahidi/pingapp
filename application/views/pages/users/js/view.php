$(document).ready(function() {
	$('#sms').dataTable( {
		"bJQueryUI": true,
		"bProcessing": true,
		"bServerSide": true,
		"aaSorting": [[ 4, "desc" ]],
		"sAjaxSource": "/messages/ajax_list?type=sms&user_id=<?php echo $user->id; ?>",
	});

	$('#email').dataTable( {
		"bJQueryUI": true,
		"bProcessing": true,
		"bServerSide": true,
		"aaSorting": [[ 4, "desc" ]],
		"sAjaxSource": "/messages/ajax_list?type=email&user_id=<?php echo $user->id; ?>",
	});
});