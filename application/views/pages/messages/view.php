<h4>View Message</h4>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li><a href="/messages">Messages</a></li>
	<li class="current"><a href="#">#<?php echo $message->id; ?></a></li>
</ul>

<div class="panel">
	<?php echo $message->message; ?>
</div>

<h4>Recipients</h4>
<div class="dashboard-display-table-wrapper">
	<div class="dashboard-display-table">
		<table class="display  dataTable" id="activity">
			<thead>
				<tr>
					<th>Name</th>
					<th>Contact</th>
					<th>Type</th>
					<th>Action</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>
