<?php if ( ! empty($errors)): ?>
<div data-alert class="alert-box alert">
	<?php foreach ($errors as $error): ?>
	<?php echo $error; ?><br />
	<?php endforeach; ?>
	<a href="#" class="close">&times;</a>
</div>
<?php endif; ?>

<?php if ($done): ?>
<div data-alert class="alert-box success">
	<?php echo __('Your message has been queued for sending'); ?>
	<a href="#" class="close">&times;</a>
</div>
<?php endif; ?>

<?php echo Form::open(NULL, array('class' => 'custom', 'method'=> 'post')); ?>
	<?php echo Form::hidden('token', Security::token()); ?>
	<div class="section-container auto" data-section>
		<section class="active">
			<p class="title" data-section-title><a href="#panel1">Message</a></p>
			<div class="content" data-section-content>
				
				<div class="recipient-row">
					<div class="recipients">
						<label><strong>To:</strong></label>
						<?php echo Form::select("recipients[]", PingApp_Form::people($user), (isset($post['recipients'])) ? $post['recipients'] : '', array("id" => "recipients[]", "minlength" => "3", "class" => "medium recipients-dropdown", "multiple" => "multiple")); ?>
					</div>
				</div>

				<div class="row">
					<div class="large-6 columns">
						<div class="panel">
							<label for="type[sms]">
							<?php echo Form::checkbox('type[]', 'sms',  ( ! isset($post['type']) OR (isset($post['type']) AND in_array('sms', $post['type'])) ) ? TRUE : FALSE, array("id" => "type[sms]", "style" => "display: none;")) ;?>
							<span class="custom checkbox"></span> Send SMS</label>
						</div>
					</div>
					<div class="large-6 columns">
						<div class="panel">
							<label for="type[email]">
							<?php echo Form::checkbox('type[]', 'email',  ( isset($post['type']) AND in_array('email', $post['type'])) ? TRUE : FALSE, array("id" => "type[email]", "style" => "display: none;")) ;?>
							<span class="custom checkbox"></span> Send Email</label>
						</div>
					</div>
				</div>

				<div class="row" id="rowTitle" style="display:none;">
					<div class="large-12 columns">
						<label>Title: <small>* required for email pings</small></label>
						<?php echo Form::input('title', $post['title'], array("maxlength" => "120")); ?>
					</div>
				</div>

				<div class="message-row">
					<div class="message">
						<label><strong>Message:</strong></label>
						<?php echo Form::textarea('message', $post['message'], array("id" => "message")); ?>
						<div id="chars" class="chars">120</div>
					</div>
				</div>
				<br />
				<div class="new-message-submit">
					<button id="btnNext" class="success expand button">Next &raquo;</button>
				</div>
				<div style="clear:both;"></div>
			</div>
		</section>
		<section>
			<p class="title" data-section-title><a href="#panel2">Confirm</a></p>
			<div class="content" data-section-content>
				<div class="row" id="rowCalculating">
					<div class="panel callout radius">
						<h5>Calculating...</h5>
					</div>
				</div>
				<div style="display:none;" id="rowConfirm">
					<div class="row">
						<div class="panel">
							<h5>Message:</h5>
							<p id="txtMessage"></p>
						</div>
					</div>
					<div class="row">
						<div class="panel">
							<h5>SMS Recipients: <span id="txtSMS"></span></h5>
						</div>
					</div>
					<div class="row">
						<div class="panel">
							<h5>Email Recipients: <span id="txtEmail"></span></h5>
						</div>
					</div>
					<div class="row">
						<div class="panel">
							<h5>Cost: <span id="txtCost"></span></h5>
						</div>
					</div>
					<div class="new-message-submit">
						<button class="success expand button">SEND</button>
					</div>
					<div style="clear:both;"></div>
				</div>
			</div>
		</section>
	</div>
<?php echo Form::close(); ?>
