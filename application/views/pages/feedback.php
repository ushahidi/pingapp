<h4>Help &amp; Feedback</h4>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li class="current"><a href="#">Help &amp; Feedback</a></li>
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
	<?php echo __('Thank you for your feedback!'); ?>
	<a href="#" class="close">&times;</a>
</div>
<?php endif; ?>

<?php echo Form::open(NULL, array('class' => 'custom')); ?>
	<?php echo Form::hidden('token', Security::token()); ?>
	<fieldset>
		<legend>Contact Information</legend>
		<div class="row">
			<div class="large-12 columns">
				<?php echo Form::input('name', $post['name'], array('placeholder' => 'Name')); ?>
			</div>
		</div>
		<div class="row">
			<div class="large-12 columns">
				<?php echo Form::input('email', $post['email'], array('placeholder' => 'Email Address')); ?>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<legend>Message</legend>
		<div class="row">
			<div class="large-12 columns">
				<?php echo Form::textarea('message', $post['message'], array('placeholder' => 'Message or Question')); ?>
			</div>
		</div>
	</fieldset>
	<div class="add-new-person-submit">
		<button class="button  expand">Submit</button>
	</div>
<?php echo Form::close(); ?>