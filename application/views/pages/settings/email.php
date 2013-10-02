<h4>Email Settings</h4>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li><a href="/settings">Settings</a></li>
	<li class="current"><a href="#">Email</a></li>
</ul>
<?php if (isset($errors)): ?>
<div data-alert class="alert-box alert">
	<?php foreach ($errors as $error): ?>
	<?php echo $error; ?><br />
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

<?php echo Form::open(NULL, array('class' => 'custom')); ?>
	<?php echo Form::hidden('token', Security::token()); ?>
	<fieldset>
		<legend>Email</legend>
		<div class="row">
			<div class="large-6 columns">
				<div class="switch round">
					<input id="z" name="settings[email]" value="off" type="radio" <?php echo (( isset($post['settings']['email']) AND $post['settings']['email'] == 'off')  OR ! isset($post['settings']['email']) ) ? 'checked' : '' ?>>
					<label for="z" onclick="">Off</label>

					<input id="z1" name="settings[email]" value="on" type="radio" <?php echo ( isset($post['settings']['email']) AND $post['settings']['email'] == 'on') ? 'checked' : '' ?>>
					<label for="z1" onclick="">On</label>
					<span></span>
				</div>				
			</div>
			<div class="large-6 columns">
				<?php echo Form::select('settings[email_type]', array('sendmail'=>'Sendmail', 'smtp' => 'SMTP'), (isset($post['settings']['email_type'])) ? $post['settings']['email_type'] : '', array("class" => "medium")) ;?>
			</div>
		</div>
		<div class="row">
			<div class="large-4 columns">
				<label>Host</label>
				<?php echo Form::input("settings[email_host]", (isset($post['settings']['email_host'])) ? $post['settings']['email_host'] : ''); ?>
			</div>
			<div class="large-4 columns">
				<label>Port</label>
				<?php echo Form::input("settings[email_port]", (isset($post['settings']['email_port'])) ? $post['settings']['email_port'] : ''); ?>
			</div>
			<div class="large-4 columns">
				<label>SMTP Auth?</label>
				<?php echo Form::select('settings[email_smtp_auth]', array('no'=>'NO', 'yes' => 'YES'), (isset($post['settings']['email_smtp_auth'])) ? $post['settings']['email_smtp_auth'] : '', array("class" => "medium")) ;?>
			</div>
		</div>
		<div class="row">
			<div class="large-6 columns">
				<label>User Name</label>
				<?php echo Form::input("settings[email_username]", (isset($post['settings']['email_username'])) ? $post['settings']['email_username'] : ''); ?>
			</div>
			<div class="large-6 columns">
				<label>Password</label>
				<?php echo Form::input("settings[email_password]", (isset($post['settings']['email_password'])) ? $post['settings']['email_password'] : ''); ?>
			</div>
		</div>
	</fieldset>

	<div class="add-new-person-submit">
		<button class="button  expand">Submit</button>
	</div>
<?php echo Form::close(); ?>
