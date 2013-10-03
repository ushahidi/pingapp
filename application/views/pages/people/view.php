<h4><?php echo $person->name; ?></h4>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li><a href="/people">People</a></li>
	<li class="current"><a href="#"><?php echo $person->name; ?></a></li>
</ul>
<a href="/people/edit/<?php echo $person->id; ?>" class="small button" id="ping-add-contact">Edit</a>
<?php
// Make sure this isn't a secondary contact
if ( $person->parent_id == 0 ): ?>
<a href="/people/edit/?parent_id=<?php echo $person->id; ?>" class="small button" id="ping-add-contact">Add Secondary Contact</a>
<?php endif;?>
<a href="/people/delete/<?php echo $person->id; ?>" class="small button alert" id="ping-add-contact" onclick="return confirm('Delete This Person?');">Delete</a>
<div class="panel">
	Status: <strong><?php echo strtoupper($person->status); ?></strong> (<?php echo date('Y-m-d', strtotime($person->updated)); ?>)
</div>

<div class="panel">
	Groups: 
	<?php foreach ($groups as $group):?>
	<span class="radius secondary label"><?php echo $group->name; ?></span></a>&nbsp;
	<?php endforeach; ?>
</div>

<h4>Pings</h4>
<table class="display dataTable" id="activity">
	<thead>
		<tr>
			<th width="100">&nbsp;</th>
			<th>Message</th>
			<th width="200">Date</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($pings as $ping): ?>
		<tr>
			<td><?php echo strtoupper($ping->contact); ?></td>
			<td><?php echo $ping->message->message; ?></td>
			<td><?php echo date('Y-m-d g:i a', strtotime($ping->created)); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<h4>Pongs</h4>
<table class="display dataTable" id="activity">
	<thead>
		<tr>
			<th width="100">&nbsp;</th>
			<th>Message</th>
			<th width="200">Date</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($pongs as $pong): ?>
		<tr>
			<td><?php echo strtoupper($pong->contact); ?></td>
			<td><?php echo $pong->content; ?></td>
			<td><?php echo date('Y-m-d g:i a', strtotime($pong->created)); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<h4>Secondary Contacts</h4>
<table class="display dataTable" id="activity">
	<thead>
		<tr>
			<th>Name</th>
			<th width="50">Pings</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($children as $child): ?>
		<tr>
			<td><a href="/people/edit/<?php echo $child->id; ?>"><strong><?php echo strtoupper($child->name); ?></strong></a></td>
			<td>0</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
