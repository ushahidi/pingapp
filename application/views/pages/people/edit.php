<?php if ($person->loaded()):?>
<h4>Edit: <?php echo $person->name; ?> <?php if ( $parent AND $parent->loaded() ): ?><small>Secondary Contact For <?php echo $parent->name; ?></small><?php endif; ?></h4>
<?php else: ?>
<h4>Add A Person</h4>
<?php endif; ?>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li><a href="/people">People</a></li>
	<li class="current"><a href="#">Edit <?php echo $person->name; ?></a></li>
</ul>

<?php if (isset($errors)): ?>
<div data-alert class="warning-alert-box">
	<?php foreach ($errors as $error): ?>
	<?php echo $error; ?><br />
	<?php endforeach; ?>
	<a href="#" class="close">&times;</a>
</div>
<?php endif; ?>

<?php if ($done): ?>
<div data-alert class="success-alert-box">
	<?php echo __('Saved Successfully'); ?>
	<a href="#" class="close">&times;</a>
</div>
<a href="/people/edit" class="new-person-button" id="ping-add-contact">New Person [+]</a>
<?php endif; ?>

<?php echo Form::open(NULL, array('class' => 'custom', 'id' => 'personForm')); ?>
	<?php echo Form::hidden('token', Security::token()); ?>
	<?php echo Form::hidden('group[]', ''); ?>
	<?php echo Form::hidden('delete[]', ''); ?>
	<fieldset>
		<legend>Name</legend>
		<div class="new-name-row">
			<div class="new-name-first">
				<?php echo Form::input("name", $post['name'], array("id" =>"name", "placeholder" => "Full Name", "required" => "required")); ?>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Groups</legend>
		<?php $i = 0; foreach ($groups as $group): ?>
		<?php if ($i == 0): ?><div class="edit-groups-row"><?php endif; ?>
			<div class="edit-groups">
				<label for="group-<?php echo $group->id; ?>">
				<?php echo Form::checkbox('group[]', $group->id,  ( isset($post['group']) AND in_array($group->id, $post['group'])) ? TRUE : FALSE, array("id" => "group-".$group->id, "style" => "display: none;")) ;?>
				<span class="custom  checkbox"></span> <?php echo $group->name; ?></label>
			</div>
		<?php $i++; if ($i == 4): ?></div><?php $i = 0; endif; ?>
		<?php endforeach; ?>
	</fieldset>


	<fieldset>
		<legend>Contact Information</legend>
		<div id="contact[0]" class="contact-info-row">
			<?php echo Form::hidden('contact[0][id]', (isset($post['contact'][0]['id'])) ? $post['contact'][0]['id'] : '0'); ?>
			<div class="contact-info-type">
				<?php echo Form::select("contact[0][type]", PingApp_Form::contact_types(), (isset($post['contact'][0]['type'])) ? $post['contact'][0]['type'] : '', array("id" => "contact[0][type]", "minlength" => "3", "class" => "medium contact-type" )); ?>
			</div>

			<div class="contact-info-account">
				<?php echo Form::input("contact[0][contact]", (isset($post['contact'][0]['contact'])) ? $post['contact'][0]['contact'] : '', array("id" => "contact[0][contact]", "placeholder" => "Account", "minlength" => "3", "class" => "contact-account" )); ?>
			</div>
			<div class="remove-contact">
				<a class="remove-contact-button  ping-del-contact">Remove</a>
			</div>
		</div>

		<?php
		if (isset($post) AND isset($post['contact']) AND count($post['contact'])):
		unset($post['contact'][0]);
		foreach ($post['contact'] as $key => $value):
		?>
		<div id="contact[<?php echo $key; ?>]" class="contact-info-row">
			<?php echo Form::hidden('contact['.$key.'][id]', (isset($post['contact'][$key]['id'])) ? $post['contact'][$key]['id'] : '0'); ?>
			<div class="contact-info-type">
				<?php echo Form::select("contact[".$key."][type]", PingApp_Form::contact_types(), (isset($post['contact'][$key]['type'])) ? $post['contact'][$key]['type'] : '', array("id" => "contact[".$key."][type]", "minlength" => "3", "class" => "medium contact-type")); ?>
			</div>
			<div class="contact-info-account">
				<?php echo Form::input("contact[".$key."][contact]", (isset($post['contact'][$key]['contact'])) ? $post['contact'][$key]['contact'] : '', array("id" => "contact[".$key."][contact]", "placeholder" => "Account", "minlength" => "3", "class" => "contact-account" )); ?>			
			</div>
			<div class="remove-contact">
				<a class="remove-contact-button  ping-del-contact">Remove</a>
			</div>
		</div>
		<?php 
		endforeach;
		endif;
		?>
		<div class="add-contact">
			<a class="add-contact-button">Add Another Contact [+]</a>
		</div>
	</fieldset>

	<div class="add-new-person-submit">
		<button class="add-new-person-submit-button">Submit</button>
	</div>

<?php echo Form::close(); ?>
