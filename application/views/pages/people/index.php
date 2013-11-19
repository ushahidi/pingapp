<h4>People <?php if ( $group AND $group->loaded() ): ?> <span class="header-label"><?php echo $group->name; ?><?php endif; ?></span></h4>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<?php if ( $group == FALSE): ?><li class="current"><a href="/people">People</a></li><?php endif; ?>
	<?php if ( $group AND $group->loaded() ): ?><li><a href="/people">People</a></li><?php endif; ?>
	<?php if ( $group AND $group->loaded() ): ?><li class="current"><a href="#"><?php echo $group->name; ?></a></li><?php endif; ?>
</ul>

<div class="people-actions-wrapper">
	<div class="new-person">
		<a href="/people/edit" class="new-person-button">New Person [+]</a>
	</div>
</div>

<div class="dashboard-display-table-wrapper">
	<div class="dashboard-display-table">
		<table class="display  dataTable" id="activity">
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
