<?php if ( ! empty($errors)): ?>
<div data-alert class="alert-box alert">
	<?php foreach ($errors as $error): ?>
	<?php echo $error; ?><br />
	<?php endforeach; ?>
	<a href="#" class="close">&times;</a>
</div>
<?php endif; ?>

<?php if ($done): ?>
<div data-alert class="alert-box success">
	<?php echo __('Your message has been queued for sending'); ?>
	<a href="#" class="close">&times;</a>
</div>
<?php endif; ?>

<?php echo Form::open(NULL, array('class' => 'custom', 'method'=> 'post')); ?>
	<?php echo Form::hidden('token', Security::token()); ?>
	<fieldset>
		<legend>New Message</legend>
		<div class="recipient-row">
			<div class="recipients">
				<label>To:</label>
				<?php echo Form::select("recipients[]", PingApp_Form::people($user), (isset($post['recipients'])) ? $post['recipients'] : '', array("id" => "recipients[]", "minlength" => "3", "class" => "medium recipients-dropdown", "multiple" => "multiple")); ?>
			</div>
		</div>

		<div class="message-row">
			<div class="message">
				<label>Message:</label>
				<?php echo Form::textarea('message', $post['message'], array("id" => "message")); ?>
				<div id="chars" class="chars">140</div>
			</div>
		</div>

	</fieldset>

	<div class="new-message-submit">
		<button class="success expand button">SEND</button>
	</div>

<?php echo Form::close(); ?>
