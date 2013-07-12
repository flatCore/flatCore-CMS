
<p style="padding:15px 0;">
<?php echo"$lang[msg_form]"; ?>
</p>


<form action="index.php" method="POST" class="form-horizontal">

<div class="control-group">
	<label class="control-label"><?php echo"$lang[username]"; ?></label>
	<div class="controls">
		<input type="text" name="username" value="" class="input-block-level">
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php echo"$lang[email]"; ?></label>
	<div class="controls">
		<input type="text" name="mail" value="" class="input-block-level">
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php echo"$lang[password]"; ?></label>
	<div class="controls">
		<input type="password" name="psw" value="" class="input-block-level">
	</div>
</div>

<hr>

<div class="row-fluid">
	<div class="span4">
		<input type="submit" class="btn btn-block" name="step1" value="<?php echo"$lang[step]"; ?> 1">
	</div>
	<div class="span8">
		<input type="submit" class="btn btn-success btn-block" name="step3" value="<?php echo"$lang[start_install]"; ?>">
	</div>
</div>


</form>