<h4><?php echo $person->name; ?> <span class="header-label">Status</span></h4>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li><a href="/people">People</a></li>
	<li><a href="/people/view/<?php echo $person->id; ?>"><?php echo $person->name; ?></a></li>
	<li class="current"><a href="#">Status</a></li>
</ul>

<?php if (isset($errors)): ?>
<div data-alert class="warning-alert-box">
	<?php foreach ($errors as $error): ?>
	<?php echo $error; ?><br />
	<?php endforeach; ?>
	<a href="#" class="close">&times;</a>
</div>
<?php endif; ?>

<div class="status-panel">
	Status: <a href="/people/status/<?php echo $person->id; ?>"><strong><?php echo strtoupper($person->status); ?></strong></a> <small><?php echo date('Y-m-d', strtotime($person->updated)); ?></small>
</div>

<?php echo Form::open(NULL, array('class' => 'custom', 'id' => 'statusForm')); ?>
	<?php echo Form::hidden('token', Security::token()); ?>
	<fieldset>
		<legend>Update Status</legend>
		<div class="update-status-row">
			<div class="okay-status">
				<div class="okay-status-panel">
					<label for="ok"><?php echo Form::radio('status', 'ok', ($post['status'] == 'ok') ? TRUE : FALSE, array('id' => 'ok', 'style' => 'display:none;')); ?><span class="custom  radio  checked"></span> OKAY</label>
				</div>
			</div>
			<div class="notok-status">
				<div class="notok-status-panel">
					<label for="notok"><?php echo Form::radio('status', 'notok', ($post['status'] == 'notok') ? TRUE : FALSE, array('id' => 'notok', 'style' => 'display:none;')); ?><span class="custom  radio  checked"></span> NOT OKAY</label>
				</div>
			</div>
			<div class="unknown-status">
				<div class="unknown-status-panel">
					<label for="unknown"><?php echo Form::radio('status', 'unknown', ($post['status'] == 'unknown') ? TRUE : FALSE, array('id' => 'unknown', 'style' => 'display:none;')); ?><span class="custom  radio  checked"></span> UNKNOWN</label>
				</div>
			</div>
		</div>
		<div class="update-status-note-row">
			<div class="update-status-note">
				<?php echo Form::textarea('note', $post['note'], array("id" => "note", 'placeholder' => 'add a note')); ?>
			</div>
		</div>
	</fieldset>

	<div class="update-status-submit">
		<button class="update-status-submit-button">Update</button>
	</div>

<?php echo Form::close(); ?>
