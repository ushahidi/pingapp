<?php echo Form::open('messages', array('class' => 'custom', 'method'=> 'post')); ?>	
	<?php echo Form::hidden('token', Security::token()); ?>
	<fieldset>
		<legend>New Message</legend>
		<div class="row">
			<div class="large-4 columns">
				<label>To (Use "," to separate recipients)</label>
				<?php echo Form::input('recipients'); ?>
			</div>
		</div>
		<div class="row">
			<div class="large-4 columns">
				<label>Message</label>
				<?php echo Form::textarea('message_text', NULL); ?>
			</div>
		</div>
	</fieldset>
	<div class="row">
		<div class="large-12 columns">
			<button class="button">SEND</button>
		</div>
	</div>
<?php echo Form::close(); ?>
