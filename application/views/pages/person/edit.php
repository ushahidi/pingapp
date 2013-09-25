<?php echo Form::open(NULL, array('class' => 'custom')); ?>	
	<?php echo Form::hidden('token', Security::token()); ?>
	<fieldset>
		<legend>Name</legend>
		<div class="new-name-row">
			<div class="new-name-first">
				<label>First Name</label>
				<input type="text" name="first_name" value="" id="first_name">
			</div>
			<div class="new-name-last">
				<label>Last Name</label>
				<input type="text" name="last_name" value="" id="last_name">
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Contact Information</legend>
		<div class="panel">
			<div class="contact-info-row">
					<div class="contact-info-type">
						<label>Type</label>
						<select id="customDropdown1" class="medium">
							<option>-- Select One --</option>
							<option>Cell Phone</option>
							<option>Twitter</option>
							<option>Email</option>
							<option>WhatsApp</option>
						</select>				
					</div>
					<div class="contact-info-account">
						<label>Account</label>
						<input type="text" name="last_name" value="" id="last_name">				
					</div>
			</div>
		</div>

		<div class="panel">
			<div class="contact-info-row">
					<div class="contact-info-type">
						<label>Type</label>
						<select id="customDropdown1" class="medium">
							<option>-- Select One --</option>
							<option>Cell Phone</option>
							<option>Twitter</option>
							<option>Email</option>
							<option>WhatsApp</option>
						</select>				
					</div>
					<div class="contact-info-account">
						<label>Account</label>
						<input type="text" name="last_name" value="" id="last_name">				
					</div>
			</div>
		</div>
	</fieldset>

	<div class="add-new-person-submit">
		<button class="button  expand">Submit</button>
	</div>

<?php echo Form::close(); ?>
