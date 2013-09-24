<?php echo Form::open(NULL, array('class' => 'custom')); ?>	
	<?php echo Form::hidden('token', Security::token()); ?>
	<fieldset>
		<legend>Name</legend>
		<div class="row">
			<div class="large-4 columns">
				<label>First Name</label>
				<input type="text" name="first_name" value="" id="first_name">
			</div>
			<div class="large-4 columns">
				<label>Last Name</label>
				<input type="text" name="last_name" value="" id="last_name">
			</div>
			<div class="large-4 columns">&nbsp;</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Contact Information</legend>
		<div class="panel">
			<div class="row">
					<div class="large-4 columns">
						<label>Type</label>
						<select id="customDropdown1" class="medium">
							<option>-- Select One --</option>
							<option>Cell Phone</option>
							<option>Twitter</option>
							<option>Email</option>
							<option>WhatsApp</option>
						</select>				
					</div>
					<div class="large-4 columns">
						<label>Account</label>
						<input type="text" name="last_name" value="" id="last_name">				
					</div>
					<div class="large-4 columns">&nbsp;</div>
			</div>
		</div>

		<div class="panel">
			<div class="row">
					<div class="large-4 columns">
						<label>Type</label>
						<select id="customDropdown1" class="medium">
							<option>-- Select One --</option>
							<option>Cell Phone</option>
							<option>Twitter</option>
							<option>Email</option>
							<option>WhatsApp</option>
						</select>				
					</div>
					<div class="large-4 columns">
						<label>Account</label>
						<input type="text" name="last_name" value="" id="last_name">				
					</div>
					<div class="large-4 columns">&nbsp;</div>
			</div>
		</div>
	</fieldset>
	<div class="row">
		<div class="large-12 columns">
			<button class="button">Submit</button>
		</div>
	</div>
<?php echo Form::close(); ?>