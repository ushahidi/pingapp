<h4><?php echo $user->first_name.' '.$user->last_name; ?></h4>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li><a href="/users">Users</a></li>
	<li class="current"><a href="#"><?php echo $user->first_name.' '.$user->last_name; ?></a></li>
</ul>


<h5>Messages</h5>
<div class="user-message-stats-tabs" data-section="tabs">
	<section class="active">
		<p class="title" data-section-title><a href="#panel1">SMS</a></p>
		<div class="content" data-section-content>
			<table class="display dataTable" id="sms">
				<thead>
					<tr>
						<th>Message</th>
						<th>Recipients</th>
						<th>Created</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>		
		</div>
	</section>
	<section>
		<p class="title" data-section-title><a href="#panel2">Email</a></p>
		<div class="content" data-section-content>
			<table class="display dataTable" id="email">
				<thead>
					<tr>
						<th>Message</th>
						<th>Recipients</th>
						<th>Created</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</section>
</div>
