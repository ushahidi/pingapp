<?php if ( $parent AND $parent->loaded() ): ?>
<h4>Secondary Contact For: <?php echo $parent->first_name.' '.$parent->last_name; ?></h4>
<?php endif; ?>
<?php echo Form::open(NULL, array('class' => 'custom')); ?>
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
		<?php echo __('Saved Successfully'); ?>
		<a href="#" class="close">&times;</a>
	</div>
	<?php endif; ?>

	<?php echo Form::hidden('token', Security::token()); ?>
	<fieldset>
		<legend>Name</legend>
		<div class="new-name-row">
			<div class="new-name-first">
				<?php echo Form::input("first_name", $post['first_name'], array("id" =>"first_name", "placeholder" => "First Name", "required" => "required")); ?>
			</div>
			<div class="new-name-last">
				<?php echo Form::input("last_name", $post['last_name'], array("id" =>"last_name", "placeholder" => "Last Name", "required" => "required")); ?>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Contact Information</legend>
		<div id="contact[0]">
			<div class="contact-info-row">
					<div class="contact-info-type">
						<?php echo Form::select("contact[0][type]", PingApp_Form::contact_types(), (isset($post['contact'][0]['type'])) ? $post['contact'][0]['type'] : '', array("id" => "contact[0][type]", "minlength" => "3", "class" => "medium")); ?>
					</div>

					<div class="contact-info-account">
						<?php echo Form::input("contact[0][contact]", (isset($post['contact'][0]['contact'])) ? $post['contact'][0]['contact'] : '', array("id" => "contact[0][contact]", "placeholder" => "Account", "minlength" => "3")); ?>
					</div>
					<div class="large-4 columns">
						<a class="small button secondary ping-del-contact">Remove</a>
					</div>
			</div>
		</div>

		<?php
		if (isset($post) AND isset($post['contact']) AND count($post['contact'])):
		unset($post['contact'][0]);
		foreach ($post['contact'] as $key => $value):
		?>
		<div class="panel" id="contact[<?php echo $key; ?>]">
			<div class="contact-info-row">
					<div class="contact-info-type">
						<?php echo Form::select("contact[".$key."][type]", PingApp_Form::contact_types(), (isset($post['contact'][$key]['type'])) ? $post['contact'][$key]['type'] : '', array("id" => "contact[".$key."][type]", "minlength" => "3", "class" => "medium")); ?>
					</div>
					<div class="contact-info-account">
						<?php echo Form::input("contact[".$key."][contact]", (isset($post['contact'][$key]['contact'])) ? $post['contact'][$key]['contact'] : '', array("id" => "contact[".$key."][contact]", "placeholder" => "Account", "minlength" => "3")); ?>			
					</div>
					<div class="remove-contact">
						<a class="small button secondary ping-del-contact">Remove</a>
					</div>
			</div>
		</div>
		<?php 
		endforeach;
		endif;
		?>
		<a class="add-contact small button secondary" id="ping-add-contact">Add Another Contact [+]</a>
	</fieldset>

	<div class="add-new-person-submit">
		<button class="button  expand">Submit</button>
	</div>

<?php echo Form::close(); ?>
