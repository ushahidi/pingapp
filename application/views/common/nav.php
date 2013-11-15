<nav class="top-bar" style="">
	<ul class="title-area">
		<!-- Title Area -->
		<li class="name"><h1><a href="/dashboard">Home</a></h1></li>
		<li class="toggle-topbar  menu-icon"><a href="#"><span>Menu</span></a></li>
	</ul>
	<section class="top-bar-section">
		<ul class="right">
			<li class="divider hide-for-small"></li>
			<li><a href="/people">People</a></li>
			<li><a href="/groups">Groups</a></li>
			<li><a href="/messages">Messages</a></li>
			<?php if ($feedback_email):?><li><a href="/feedback" id="help">Help</a></li><?php endif; ?>
			<li class="logged-in  has-dropdown">
				<a href="#">
					<div class="avatar">
						<?php if (! $user->first_name AND !$user->last_name): ?>
							<?php echo $user->email; ?>
						<?php else: ?>
							<?php echo $user->first_name.' '.$user->last_name; ?>
						<?php endif; ?>
					</div>
				</a>
				<ul class="dropdown">
					<li><a href="#">My Profile</a></li>
					<?php if ($role == 'admin'):?>
					<li><a href="/users">Manage Users</a></li>
					<li><a href="/settings">Manage Settings</a></li>
					<?php endif; ?>
					<li class="signout"><a href="/logout">Sign Out</a></li>
				</ul>
			</li>
		</ul>
	</section>
</nav>
