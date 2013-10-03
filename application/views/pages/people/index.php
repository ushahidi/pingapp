<h4>People <?php if ( $group AND $group->loaded() ): ?><small><?php echo $group->name; ?></small><?php endif; ?></h4>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li class="current"><a href="#">People</a></li>
</ul>

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
