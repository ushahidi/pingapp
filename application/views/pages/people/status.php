<h4><?php echo $person->name; ?></h4>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li><a href="/people">People</a></li>
	<li><a href="/people/view/<?php echo $person->id; ?>"><?php echo $person->name; ?></a></li>
	<li class="current"><a href="#">Status</a></li>
</ul>

<?php if (isset($errors)): ?>
<div data-alert class="alert-box alert">
	<?php foreach ($errors as $error): ?>
	<?php echo $error; ?><br />
	<?php endforeach; ?>
	<a href="#" class="close">&times;</a>
</div>
<?php endif; ?>

<div class="panel">
	Status: <a href="/people/status/<?php echo $person->id; ?>"><strong><?php echo strtoupper($person->status); ?></strong></a> <small><?php echo date('Y-m-d', strtotime($person->updated)); ?></small>
</div>

<?php echo Form::open(NULL, array('class' => 'custom', 'id' => 'statusForm')); ?>
	<?php echo Form::hidden('token', Security::token()); ?>
	<fieldset>
		<legend>Update Status</legend>
		<div class="row">
			<div class="small-12 large-4 columns">
				<div class="panel">
					<label for="ok"><?php echo Form::radio('status', 'ok', ($post['status'] == 'ok') ? TRUE : FALSE, array('id' => 'ok', 'style' => 'display:none;')); ?><span class="custom radio checked"></span> OKAY</label>
				</div>
			</div>
			<div class="small-12 large-4 columns">
				<div class="panel">
					<label for="notok"><?php echo Form::radio('status', 'notok', ($post['status'] == 'notok') ? TRUE : FALSE, array('id' => 'notok', 'style' => 'display:none;')); ?><span class="custom radio checked"></span> NOT OKAY</label>
				</div>
			</div>
			<div class="small-12 large-4 columns">
				<div class="panel">
					<label for="unknown"><?php echo Form::radio('status', 'unknown', ($post['status'] == 'unknown') ? TRUE : FALSE, array('id' => 'unknown', 'style' => 'display:none;')); ?><span class="custom radio checked"></span> UNKNOWN</label>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="small-12 large-12 columns">
				<?php echo Form::textarea('note', $post['note'], array("id" => "note", 'placeholder' => 'add a note')); ?>
			</div>
		</div>
	</fieldset>
	<div class="add-new-person-submit">
		<button class="button  expand">Update</button>
	</div>
<?php echo Form::close(); ?>