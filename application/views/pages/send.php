<?php if (isset($errors)): ?>
<div data-alert class="alert-box alert">
	<?php foreach ($errors as $error): ?>
	&middot; <?php echo $error; ?><br />
	<?php endforeach; ?>
	<a href="#" class="close">&times;</a>
</div>
<?php endif; ?>

<?php if ($done): ?>
<div data-alert class="alert-box success">
	<?php echo __('Sent Successfully'); ?>
	<a href="#" class="close">&times;</a>
</div>
<?php endif; ?>

<?php echo Form::open(NULL, array('class' => 'custom', 'method'=> 'post')); ?>
	<?php echo Form::hidden('token', Security::token()); ?>
	<fieldset>
		<legend>New Message</legend>
		<div class="row">
			<div class="large-8 columns">
				<label>To</label>
				<?php echo Form::select("recipients[]", PingApp_Form::people($user), (isset($post['recipients'])) ? $post['recipients'] : '', array("id" => "recipients[]", "minlength" => "3", "class" => "medium", "multiple" => "multiple")); ?>
			</div>
		</div>
		<div class="row">
			<div class="large-8 columns">
				<label>Message</label>
				<?php echo Form::textarea('message', $post['message']); ?>
			</div>
		</div>
	</fieldset>
	<div class="row">
		<div class="large-12 columns">
			<button class="button">SEND</button>
		</div>
	</div>
<?php echo Form::close(); ?>