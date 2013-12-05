<div class="alert alert-info">
<?php echo"$lang[msg_form]"; ?>
</div>

<form action="index.php" method="POST" class="form-horizontal">

	<div class="form-group">
		<label class="col-sm-2 control-label"><?php echo"$lang[username]"; ?></label>
		<div class="col-sm-10">
			<input type="text" class="form-control" name="username" value="">
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label"><?php echo"$lang[email]"; ?></label>
		<div class="col-sm-10">
			<input type="text" class="form-control" name="mail" value="">
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-2 control-label"><?php echo"$lang[password]"; ?></label>
		<div class="col-sm-10">
			<input type="password" class="form-control" name="psw" value="">
		</div>
	</div>

<hr>

<div class="row">
	<div class="col-md-4">
		<input type="submit" class="btn btn-block" name="step1" value="<?php echo"$lang[step]"; ?> 1">
	</div>
	<div class="col-md-8">
		<input type="submit" class="btn btn-success btn-block" name="step3" value="<?php echo"$lang[start_install]"; ?>">
	</div>
</div>


</form>