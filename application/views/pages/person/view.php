<h3><?php echo $person->first_name.' '.$person->last_name; ?></h3>

<div class="panel">
	<strong><?php echo strtoupper($person->status); ?></strong> (<?php echo date('Y-m-d', strtotime($person->updated)); ?>)
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
		<?php foreach ($person->pings as $ping): ?>
		<td><?php echo strtoupper($ping->type); ?></td>
		<td><?php echo date('Y-m-d', strtotime($ping->created)); ?></td>
		<?php endforeach; ?>
	</tbody>
</table>