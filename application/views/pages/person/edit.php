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
		<div class="panel ping-contact">
			<div class="row">
					<div class="large-4 columns">
						<label>Type</label>
						<?php echo Form::select("contact[0][type]", Pingapp_Form::contact_types(), (isset($post['contact'][0]['type'])) ? $post['contact'][0]['type'] : '', array("id" => "contact[0][type]", "required" => "required", "minlength" => "3", "class" => "medium")); ?>				
					</div>
					<div class="large-4 columns">
						<label>Account</label>
						<?php echo Form::input("contact[0][account]", (isset($post['contact'][0]['account'])) ? $post['contact'][0]['account'] : '', array("required" => "required", "minlength" => "3")); ?>			
					</div>
					<div class="large-4 columns">
						<label>&nbsp;</label>
						<a class="small button secondary" id="ping-del-contact">Remove</a>
					</div>
			</div>
		</div>
		<a class="small button secondary" id="ping-add-contact">Add Another Contact [+]</a>
	</fieldset>
	<div class="row">
		<div class="large-12 columns">
			<button class="button">Submit</button>
		</div>
	</div>
<?php echo Form::close(); ?>