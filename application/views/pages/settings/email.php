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
		<legend>Email Provider</legend>
		<div class="row">
			<div class="large-6 columns">
				<label>Outgoing Email?</label>
				<div class="switch round">
					<input id="z" name="settings[email_outgoing]" value="off" type="radio" <?php echo (( isset($post['settings']['email_outgoing']) AND $post['settings']['email_outgoing'] == 'off')  OR ! isset($post['settings']['email_outgoing']) ) ? 'checked' : '' ?>>
					<label for="z" onclick="">Off</label>

					<input id="z1" name="settings[email_outgoing]" value="on" type="radio" <?php echo ( isset($post['settings']['email_outgoing']) AND $post['settings']['email_outgoing'] == 'on') ? 'checked' : '' ?>>
					<label for="z1" onclick="">On</label>
					<span></span>
				</div>				
			</div>

			<div class="large-6 columns">
				<label>Incoming Email?</label>
				<div class="switch round">
					<input id="z" name="settings[email_incoming]" value="off" type="radio" <?php echo (( isset($post['settings']['email_incoming']) AND $post['settings']['email_incoming'] == 'off')  OR ! isset($post['settings']['email_incoming']) ) ? 'checked' : '' ?>>
					<label for="z" onclick="">Off</label>

					<input id="z1" name="settings[email_incoming]" value="on" type="radio" <?php echo ( isset($post['settings']['email_incoming']) AND $post['settings']['email_incoming'] == 'on') ? 'checked' : '' ?>>
					<label for="z1" onclick="">On</label>
					<span></span>
				</div>				
			</div>
		</div>

		<div class="row">
			<div class="large-6 columns">
				<label>Email Address</label>
				<?php echo Form::input("settings[email_from]", (isset($post['settings']['email_from'])) ? $post['settings']['email_from'] : ''); ?>
			</div>
			<div class="large-6 columns">
				<label>Email From Name</label>
				<?php echo Form::input("settings[email_from_name]", (isset($post['settings']['email_from_name'])) ? $post['settings']['email_from_name'] : ''); ?>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Outgoing Email Settings</legend>
		<div class="row">
			<div class="large-4 columns">
				<label>Type</label>
				<?php echo Form::select('settings[email_outgoing_type]', array('sendmail'=>'Sendmail', 'smtp' => 'SMTP'), (isset($post['settings']['email_outgoing_type'])) ? $post['settings']['email_outgoing_type'] : '', array("class" => "medium")) ;?>
			</div>
			<div class="large-4 columns">
				<label>Host</label>
				<?php echo Form::input("settings[email_outgoing_host]", (isset($post['settings']['email_outgoing_host'])) ? $post['settings']['email_outgoing_host'] : ''); ?>
			</div>
			<div class="large-4 columns">
				<label>Port (25, 465, 587)</label>
				<?php echo Form::input("settings[email_outgoing_port]", (isset($post['settings']['email_outgoing_port'])) ? $post['settings']['email_outgoing_port'] : ''); ?>
			</div>
		</div>
		<div class="row">
			<div class="large-3 columns">
				<label>SMTP Auth?</label>
				<?php echo Form::select('settings[email_smtp_auth]', array('no'=>'NO', 'yes' => 'YES'), (isset($post['settings']['email_smtp_auth'])) ? $post['settings']['email_smtp_auth'] : '', array("class" => "medium")) ;?>
			</div>
			<div class="large-3 columns">
				<label>Security</label>
				<?php echo Form::select('settings[email_outgoing_security]', array('none'=>'None', 'tls' => 'TLS', 'ssl' => 'SSL'), (isset($post['settings']['email_outgoing_security'])) ? $post['settings']['email_outgoing_security'] : '', array("class" => "medium")) ;?>
			</div>
			<div class="large-3 columns">
				<label>User Name</label>
				<?php echo Form::input("settings[email_outgoing_username]", (isset($post['settings']['email_outgoing_username'])) ? $post['settings']['email_outgoing_username'] : ''); ?>
			</div>
			<div class="large-3 columns">
				<label>Password</label>
				<?php echo Form::password("settings[email_outgoing_password]", (isset($post['settings']['email_outgoing_password'])) ? $post['settings']['email_outgoing_password'] : ''); ?>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Incoming Email Settings</legend>
		<div class="row">
			<div class="large-4 columns">
				<label>Type</label>
				<?php echo Form::select('settings[email_incoming_type]', array('imap' => 'IMAP', 'pop3'=>'POP3'), (isset($post['settings']['email_incoming_type'])) ? $post['settings']['email_incoming_type'] : '', array("class" => "medium")) ;?>
			</div>
			<div class="large-4 columns">
				<label>Server</label>
				<?php echo Form::input("settings[email_incoming_server]", (isset($post['settings']['email_incoming_server'])) ? $post['settings']['email_incoming_server'] : ''); ?>
			</div>
			<div class="large-4 columns">
				<label>Port (110/143/993)</label>
				<?php echo Form::input("settings[email_incoming_port]", (isset($post['settings']['email_incoming_port'])) ? $post['settings']['email_incoming_port'] : ''); ?>
			</div>
		</div>
		<div class="row">
			<div class="large-4 columns">
				<label>Security</label>
				<?php echo Form::select('settings[email_incoming_security]', array('none'=>'None', 'tls' => 'TLS', 'ssl' => 'SSL'), (isset($post['settings']['email_incoming_security'])) ? $post['settings']['email_incoming_security'] : '', array("class" => "medium")) ;?>
			</div>
			<div class="large-4 columns">
				<label>User Name</label>
				<?php echo Form::input("settings[email_incoming_username]", (isset($post['settings']['email_incoming_username'])) ? $post['settings']['email_incoming_username'] : ''); ?>
			</div>
			<div class="large-4 columns">
				<label>Password</label>
				<?php echo Form::password("settings[email_incoming_password]", (isset($post['settings']['email_incoming_password'])) ? $post['settings']['email_incoming_password'] : ''); ?>
			</div>
		</div>
	</fieldset>

	<div class="add-new-person-submit">
		<button class="button  expand">Submit</button>
	</div>
<?php echo Form::close(); ?>
