<h3><?php echo $person->first_name.' '.$person->last_name; ?></h3>
<a href="/person/edit/<?php echo $person->id; ?>" class="small button" id="ping-add-contact">Edit</a>
<?php
// Make sure this isn't a secondary contact
if ( $person->parent_id == 0 ): ?>
<a href="/person/edit/?parent_id=<?php echo $person->id; ?>" class="small button" id="ping-add-contact">Add Secondary Contact</a>
<?php endif;?>
<div class="panel">
	Status: <strong><?php echo strtoupper($person->status); ?></strong> (<?php echo date('Y-m-d', strtotime($person->updated)); ?>)
</div>

<h4>Pings</h4>
<table class="display dataTable" id="activity">
	<thead>
		<tr>
			<th>Date</th>
			<th width="150">Type</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($pings as $ping): ?>
		<tr>
			<td><?php echo strtoupper($ping->contact); ?></td>
			<td><?php echo date('Y-m-d g:i a', strtotime($ping->created)); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<h4>Pongs</h4>
<table class="display dataTable" id="activity">
	<thead>
		<tr>
			<th>Date</th>
			<th width="150">Type</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($pongs as $pong): ?>
		<tr></tr>
		<?php endforeach; ?>
	</tbody>
</table>