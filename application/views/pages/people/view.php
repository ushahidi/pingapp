<h4><?php echo $person->name; ?></h4>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li><a href="/people">People</a></li>
	<?php if ($person->parent_id != 0):?>
	<li><a href="/people/view/<?php echo $person->parent->id; ?>"><?php echo $person->parent->name; ?></a></li>
	<?php endif; ?>
	<li class="current"><a href="#"><?php echo $person->name; ?></a></li>
</ul>
<a href="/people/edit/<?php echo $person->id; ?>" class="small button" id="ping-add-contact">Edit</a>
<?php
// Make sure this isn't a secondary contact
if ( $person->parent_id == 0 ): ?>
<a href="/people/edit/?parent_id=<?php echo $person->id; ?>" class="small button" id="ping-add-contact">Add Secondary Contact</a>
<?php endif;?>
<a href="/people/delete/<?php echo $person->id; ?>" class="small button alert" id="ping-add-contact" onclick="return confirm('Delete This Person?');">Delete</a>

<?php if ( $person->parent_id == 0 ): ?>
<div class="panel">
	Status: <a href="/people/status/<?php echo $person->id; ?>"><strong><?php echo strtoupper($person->status); ?></strong></a> <small><?php echo date('Y-m-d g:i a', strtotime($person->updated)); ?> [<a href="/people/status/<?php echo $person->id; ?>">change</a>]</small>
	<?php if ($status->loaded() AND ! $my_status): ?>
	<br /><br />
		<?php if ($status->user_id):?>
		<div data-alert class="alert-box alert radius">
			* status of this person was updated by another user
		</div>
		User <a href="#">#<?php echo $status->user_id; ?></a> added a note: <strong><?php echo $status->note; ?></strong>
		<?php else: ?>
		<div data-alert class="alert-box success radius">
			* status was updated when this person responded to another user
		</div>
		<?php endif; ?>
	<?php endif; ?>
</div>

<div class="panel">
	Groups: 
	<?php foreach ($groups as $group):?>
	<a href="/groups/view/<?php echo $group->id; ?>" class="radius secondary label"><?php echo $group->name; ?></a></a>&nbsp;
	<?php endforeach; ?>
</div>
<?php endif; ?>

<div class="section-container auto" data-section>
	<section class="active">
		<p class="title" data-section-title><a href="#panel1">Pings</a></p>
		<div class="content" data-section-content>
			<table class="display dataTable" id="pings">
				<thead>
					<tr>
						<th>Contact</th>
						<th>Message</th>
						<th>Re-Pings</th>
						<th>Date</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>		
		</div>
	</section>
	<section>
		<p class="title" data-section-title><a href="#panel2">Pongs</a></p>
		<div class="content" data-section-content>
			<table class="display dataTable" id="pongs">
				<thead>
					<tr>
						<th>Contact</th>
						<th>Message</th>
						<th>Date</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</section>
	<?php if ( $person->parent_id == 0 ): ?>
	<section>
		<p class="title" data-section-title><a href="#panel2">Secondary Contacts</a></p>
		<div class="content" data-section-content>
			<div class="dashboard-display-table-wrapper">
				<div class="dashboard-display-table">
					<table class="display dataTable" id="secondary">
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
		</div>
	</section>
	<?php endif; ?>
</div>