<h4>People <?php if ( $group AND $group->loaded() ): ?>- <?php echo $group->name; ?><?php endif; ?></h4>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li><a href="/people">People</a></li>
	<?php if ( $group AND $group->loaded() ): ?><li class="current"><a href="#"><?php echo $group->name; ?></a></li><?php endif; ?>
</ul>

<div class="dashboard-actions-wrapper">
	<div class="new-message">
		<a href="/people/edit" class="button  expand">New Person [+]</a>
	</div>
</div>

<div class="dashboard-display-table-wrapper">
	<div class="dashboard-display-table">
		<table class="display dataTable" id="activity">
			<thead>
				<tr>
					<th>Name</th>
					<th width="150">Status</th>
					<th width="50">Pings</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>
