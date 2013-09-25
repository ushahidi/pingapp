<?php echo Form::open(NULL, array('class' => 'custom')); ?>	
	<?php echo Form::hidden('token', Security::token()); ?>
	<fieldset>
		<div class="row">
			<div class="large-4 columns">
				<textarea name="message" maxlength="120" value="" id="message">
			</div>
			<div>
				<label>Are You Ok?</label>
			</div>
			<div class="large-4 columns">&nbsp;</div>
		</div>
	</fieldset>
	<div class="row">
		<div class="large-12 columns">
			<button class="button">SEND</button>
		</div>
	</div>
<?php echo Form::close(); ?>
