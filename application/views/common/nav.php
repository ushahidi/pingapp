<nav class="top-bar" style="">
	<ul class="title-area">
		<!-- Title Area -->
		<li class="name"></li>
		<li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
	</ul>
	<section class="top-bar-section">
		<ul class="right">
			<li class="divider hide-for-small"></li>
			<li class=""><a href="/dashboard">Dashboard</a></li>
			<li class=""><a href="#">Messages</a></li>
			<li class=""><a href="#">Settings</a></li>
			<li class="logged-in has-dropdown">
				<a href="#">
					<div class="avatar">
						<?php echo $user->first_name.' '.$user->last_name; ?>
					</div>
				</a>
				<ul class="dropdown">
					<li><a href="#">My Profile</a></li>
					<li class="signout"><a href="/logout">Sign Out</a></li>
				</ul>
			</li>
		</ul>
	</section>
</nav>