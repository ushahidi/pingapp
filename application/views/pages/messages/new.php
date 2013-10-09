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
				<label><strong>To:</strong></label>
				<?php echo Form::select("recipients[]", PingApp_Form::people($user), (isset($post['recipients'])) ? $post['recipients'] : '', array("id" => "recipients[]", "minlength" => "3", "class" => "medium recipients-dropdown", "multiple" => "multiple")); ?>
			</div>
		</div>

		<div class="row">
			<div class="large-6 columns">
				<h4><label for="type[sms]">
				<?php echo Form::checkbox('type[]', 'sms',  ( ! isset($post['type']) OR (isset($post['type']) AND in_array('sms', $post['type'])) ) ? TRUE : FALSE, array("id" => "type[sms]", "style" => "display: none;")) ;?>
				<span class="custom checkbox"></span> Send SMS</label></h4>
			</div>
			<div class="large-6 columns">
				<h4><label for="type[email]">
				<?php echo Form::checkbox('type[]', 'email',  ( isset($post['type']) AND in_array('email', $post['type'])) ? TRUE : FALSE, array("id" => "type[email]", "style" => "display: none;")) ;?>
				<span class="custom checkbox"></span> Send Email</label></h4>
			</div>
		</div>

		<div class="row">
			<div class="large-12 columns">
				<label>Title: <small>* required for email pings</small></label>
				<?php echo Form::input('title', $post['title'], array("maxlength" => "120")); ?>
			</div>
		</div>

		<div class="message-row">
			<div class="message">
				<label><strong>Message:</strong></label>
				<?php echo Form::textarea('message', $post['message'], array("id" => "message")); ?>
				<div id="chars" class="chars">120</div>
			</div>
		</div>

	</fieldset>

	<div class="new-message-submit">
		<button class="success expand button">SEND</button>
	</div>

<?php echo Form::close(); ?>
