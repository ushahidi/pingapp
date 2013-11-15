<h4>Help &amp; Feedback</h4>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li class="current"><a href="#">Help &amp; Feedback</a></li>
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
	<?php echo __('Thank you for your feedback!'); ?>
	<a href="#" class="close">&times;</a>
</div>
<?php endif; ?>

<?php echo Form::open(NULL, array('class' => 'custom')); ?>
	<?php echo Form::hidden('token', Security::token()); ?>
	<fieldset>
		<legend>Contact Information</legend>
		<div class="contact-info-row">
			<div class="contact-info-name">
				<?php echo Form::input('name', $post['name'], array('placeholder' => 'Name')); ?>
			</div>
			<div class="contact-info-email">
				<?php echo Form::input('email', $post['email'], array('placeholder' => 'Email Address')); ?>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<legend>Message</legend>
		<div class="contact-info-message-row">
			<div class="contact-info-message">
				<?php echo Form::textarea('message', $post['message'], array('placeholder' => 'Message or Question')); ?>
			</div>
		</div>
	</fieldset>
	<div class="feedback-submit">
		<button class="feedback-submit-button">Submit</button>
	</div>
<?php echo Form::close(); ?>
