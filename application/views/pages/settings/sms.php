<h4>SMS Settings</h4>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li><a href="/settings">Settings</a></li>
	<li class="current"><a href="#">SMS</a></li>
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
<?php endif; ?>

<?php echo Form::open(NULL, array('class' => 'custom')); ?>
	<?php echo Form::hidden('token', Security::token()); ?>
	<fieldset>
		<legend>SMS Provider</legend>
		<div class="sms-provider-row">
			<div class="sms-provider-switch">
				<div class="switch  round">
					<input id="z" name="settings[sms]" value="off" type="radio" <?php echo (( isset($post['settings']['sms']) AND $post['settings']['sms'] == 'off') OR ! isset($post['settings']['sms']) ) ? 'checked' : '' ?>>
					<label for="z" onclick="">Off</label>

					<input id="z1" name="settings[sms]" value="on" type="radio" <?php echo ( isset($post['settings']['sms']) AND $post['settings']['sms'] == 'on') ? 'checked' : '' ?>>
					<label for="z1" onclick="">On</label>
					<span></span>
				</div>				
			</div>
			<div class="sms-provider-select">
				<?php echo Form::select('settings[sms_provider]', PingApp_Form::sms_providers(), (isset($post['settings']['sms_provider'])) ? $post['settings']['sms_provider'] : '', array("class" => "medium")) ;?>
			</div>
		</div>
	</fieldset>

	<?php foreach ($plugins as $key => $plugin): ?>
	<fieldset>
		<legend>
			<?php echo $plugin['name']; ?> 
			<?php if(isset($plugin['links']['signup'])): ?>
				&nbsp;&nbsp;<a href="<?php echo $plugin['links']['signup']; ?>" target="_blank" class="plugin-signup-button">Sign Up</a>
			<?php endif;?>
		</legend>
		
		<div class="sms-plugin-row">
			<?php foreach ($plugin['options'] as $option => $label): ?>
			<div class="sms-plugin-options">
				<label><?php echo $label; ?></label>
				<?php echo Form::input("settings[".$key."_".$option."]", (isset($post['settings'][$key."_".$option])) ? $post['settings'][$key."_".$option] : ''); ?>
			</div>
			<?php endforeach; ?>
		</div>
	</fieldset>
	<?php endforeach; ?>

	<div class="update-sms-settings">
		<button class="update-sms-settings-button">Submit</button>
	</div>
<?php echo Form::close(); ?>
