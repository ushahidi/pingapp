<h4>Extras</h4>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li><a href="/settings">Settings</a></li>
	<li class="current"><a href="#">Extras</a></li>
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
		<legend>Site URL</legend>
		<div class="row">
			<div class="large-12 columns">
				<?php echo Form::input('settings[site_url]', (isset($post['settings']['site_url'])) ? $post['settings']['site_url'] : '', array('placeholder' => 'http://www.example.com')); ?>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<legend>Feedback Email Address</legend>
		<div class="row">
			<div class="large-12 columns">
				<?php echo Form::input('settings[feedback_email]', (isset($post['settings']['feedback_email'])) ? $post['settings']['feedback_email'] : '', array('placeholder' => 'Email Address')); ?>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<legend>Google Analytics</legend>
		<div class="row">
			<div class="large-12 columns">
				<?php echo Form::input('settings[google_analytics]', (isset($post['settings']['google_analytics'])) ? $post['settings']['google_analytics'] : '', array('placeholder' => 'Tracking ID: Example UA-12345678-90')); ?>
			</div>
		</div>
	</fieldset>
	<div class="add-new-person-submit">
		<button class="button  expand">Submit</button>
	</div>
<?php echo Form::close(); ?>
