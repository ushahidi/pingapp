<h4>Pings</h4>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li><a href="/settings">Settings</a></li>
	<li class="current"><a href="#">Pings</a></li>
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
		<legend>Maximum Number of Pings To A Person Per 24 Hours</legend>
		<div class="row">
			<div class="large-12 columns">
				<?php echo Form::select('settings[pings_per_24]', array('1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' =>8, '9' => 9, '10' => 10), (isset($post['settings']['pings_per_24'])) ? $post['settings']['pings_per_24'] : '', array("class" => "medium")) ;?>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Minute Delay Between Re-Pings</legend>
		<div class="row">
			<div class="large-12 columns">
				<?php echo Form::select('settings[pings_repings_delay]', array('5' => 5, '10' => 10, '15' => 15, '30' => 30, '60' => 60), (isset($post['settings']['pings_repings_delay'])) ? $post['settings']['pings_repings_delay'] : '', array("class" => "medium")) ;?>
			</div>
		</div>
	</fieldset>

	<div class="add-new-person-submit">
		<button class="button  expand">Submit</button>
	</div>
<?php echo Form::close(); ?>
