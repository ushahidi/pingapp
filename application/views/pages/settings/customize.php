<h4>Customize Messages</h4>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li><a href="/settings">Settings</a></li>
	<li class="current"><a href="#">Customize Messages</a></li>
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
		<legend>SMS Tagline</legend>
		<div class="row">
			<div class="large-12 columns">
				<?php echo Form::input('settings[message_sms]', (isset($post['settings']['message_sms'])) ? $post['settings']['message_sms'] : '', array("maxlength" => "30")); ?>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Email Template</legend>
		<div class="row">
			<div class="large-12 columns highlight">	
				<small><strong>{{name}}</strong> = <span>Recipient Name</span></small><br />
				<small><strong>{{message}}</strong> = <span>Message Content</span></small>
			</div>
		</div>
		<div class="row">
			<div class="large-12 columns">
				<?php echo Form::textarea('settings[message_email]', (isset($post['settings']['message_email'])) ? $post['settings']['message_email'] : ''); ?>
			</div>
		</div>
	</fieldset>

	<div class="add-new-person-submit">
		<button class="button  expand">Submit</button>
	</div>
<?php echo Form::close(); ?>
