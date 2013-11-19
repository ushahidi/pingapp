<h4>Customize Messages</h4>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li><a href="/settings">Settings</a></li>
	<li class="current"><a href="#">Customize Messages</a></li>
</ul>
<?php if (isset($errors)): ?>
<div data-alert class="warning-alert-box">
	<?php foreach ($errors as $error): ?>
	<?php echo $error; ?><br />
	<?php endforeach; ?>
	<a href="#" class="close">&times;</a>
</div>
<?php endif; ?>

<?php if ($done): ?>
<div data-alert class="success-alert-box">
	<?php echo __('Saved Successfully'); ?>
	<a href="#" class="close">&times;</a>
</div>
<?php endif; ?>

<?php echo Form::open(NULL, array('class' => 'custom')); ?>
	<?php echo Form::hidden('token', Security::token()); ?>
	<fieldset>
		<legend>SMS Tagline</legend>
		<div class="sms-tagline-row">
			<div class="sms-tagline">
				<?php echo Form::input('settings[message_sms]', (isset($post['settings']['message_sms'])) ? $post['settings']['message_sms'] : '', array("maxlength" => "30")); ?>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Email Template</legend>
		<div class="email-template-row">
			<div class="codebox">	
				<small><strong>{{name}}</strong> = <span>Recipient Name</span></small><br />
				<small><strong>{{message}}</strong> = <span>Message Content</span></small>
			</div>
			<div class="email-template-textarea">
				<?php echo Form::textarea('settings[message_email]', (isset($post['settings']['message_email'])) ? $post['settings']['message_email'] : ''); ?>
			</div>
		</div>
	</fieldset>

	<div class="update-custom-message-settings">
		<button class="update-custom-message-settings-button">Submit</button>
	</div>
<?php echo Form::close(); ?>
