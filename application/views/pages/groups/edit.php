<?php if ($group->loaded()):?>
<h4>Edit: <?php echo $group->name; ?></h4>
<?php else: ?>
<h4>Add A Group</h4>
<?php endif; ?>
<ul class="breadcrumbs">
	<li><a href="/">Home</a></li>
	<li><a href="/groups">Groups</a></li>
	<li class="current"><a href="#">Edit <?php echo $group->name; ?></a></li>
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

<?php if ($group->loaded()): ?>
<a href="/people?group_id=<?php echo $group->id; ?>" class="small button" id="ping-add-contact">View Users</a>
<a href="/groups/delete/<?php echo $group->id; ?>" class="small button alert" id="ping-add-contact" onclick="return confirm('Delete This Group?');">Delete</a>
<?php endif; ?>

<?php echo Form::open(NULL, array('class' => 'custom')); ?>
	<?php echo Form::hidden('token', Security::token()); ?>
	<fieldset>
		<legend>Name</legend>
		<div class="new-name-row">
			<div class="new-name-first">
				<?php echo Form::input("name", $post['name'], array("id" =>"name", "placeholder" => "Group Name", "required" => "required")); ?>
			</div>
		</div>
	</fieldset>

	<div class="add-new-person-submit">
		<button class="button  expand">Submit</button>
	</div>
<?php echo Form::close(); ?>
