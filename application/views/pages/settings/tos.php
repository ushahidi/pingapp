<h4>Terms Of Service Settings</h4>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li><a href="/settings">Settings</a></li>
	<li class="current"><a href="#">Terms Of Service</a></li>
</ul>
<?php if (isset($errors)): ?>
<div data-alert class="alert-box alert">
	<?php foreach ($errors as $error): ?>
	<?php echo $error; ?><br />
	<?php endforeach; ?>
	<a href="#" class="close">&times;</a>
</div>
<?php endif; ?>

<?php if ($done): ?>
<div data-alert class="alert-box success">
	<?php echo __('Saved Successfully'); ?>
	<a href="#" class="close">&times;</a>
</div>
<?php endif; ?>

<?php echo Form::open(NULL, array('class' => 'custom')); ?>
	<?php echo Form::hidden('token', Security::token()); ?>
	<fieldset>
		<legend>Terms Of Service</legend>
		<div class="row">
			<div class="large-12 columns">
				<?php echo Form::textarea('settings[tos]', (isset($post['settings']['tos'])) ? $post['settings']['tos'] : ''); ?>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Privacy Policy</legend>
		<div class="row">
			<div class="large-12 columns">
				<?php echo Form::textarea('settings[privacy]', (isset($post['settings']['privacy'])) ? $post['settings']['privacy'] : ''); ?>
			</div>
		</div>
	</fieldset>

	<div class="add-new-person-submit">
		<button class="button  expand">Submit</button>
	</div>
<?php echo Form::close(); ?>
