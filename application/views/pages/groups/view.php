<h4><?php echo $group->name; ?></h4>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li><a href="/groups">Groups</a></li>
	<li class="current"><a href="#"><?php echo $group->name; ?></a></li>
</ul>
<a href="/groups/edit/<?php echo $group->id; ?>" class="small button" id="ping-add-contact">Edit</a>
<a href="/groups/delete/<?php echo $group->id; ?>" class="small button alert" id="ping-add-contact" onclick="return confirm('Delete This Group?');">Delete</a>

<div class="dashboard-display-table-wrapper">
	<div class="dashboard-display-table">
		<table class="display dataTable" id="activity">
			<thead>
				<tr>
					<th class="sorting">Name</th>
					<th class="sorting" width="150">Status</th>
					<th class="sorting" width="50">Pings</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>