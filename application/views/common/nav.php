<nav class="top-bar" style="">
	<ul class="title-area">
		<!-- Title Area -->
		<li class="name"><h1><a href="/dashboard">Home</a></h1></li>
		<li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
	</ul>
	<section class="top-bar-section">
		<ul class="right">
			<li class="divider hide-for-small"></li>
			<li class=""><a href="/groups">Groups</a></li>
			<li class=""><a href="#">Messages</a></li>
			<li class=""><a href="#">Settings</a></li>
			<li class="logged-in has-dropdown">
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
					<li><a href="/settings">Admin Settings</a></li>
					<?php endif; ?>
					<li class="signout"><a href="/logout">Sign Out</a></li>
				</ul>
			</li>
		</ul>
	</section>
</nav>