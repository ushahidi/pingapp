<h4>People <?php if ( $group AND $group->loaded() ): ?><small><?php echo $group->name; ?></small><?php endif; ?></h4>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li class="current"><a href="#">People</a></li>
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
					<th class="sorting">Name</th>
					<th class="sorting" width="150">Status</th>
					<th class="sorting" width="50">Pings</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>
