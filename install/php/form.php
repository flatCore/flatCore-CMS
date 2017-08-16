<?php
if(!defined('INSTALLER')) {
	header("location:../login.php");
	die("PERMISSION DENIED!");
}

$prefs_cms_domain = "http://$_SERVER[HTTP_HOST]";
$prefs_cms_ssl_domain = '';
$prefs_cms_base = dirname(dirname(htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES, "utf-8")));

?>

<div class="alert alert-info">
<?php echo $lang['msg_form']; ?>
</div><hr>

<form action="index.php" method="POST" class="form-horizontal">

	<div class="form-group">
		<label class="col-sm-2 control-label"><?php echo $lang['username']; ?></label>
		<div class="col-sm-10">
			<input type="text" class="form-control" name="username" value="">
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label"><?php echo $lang['email']; ?></label>
		<div class="col-sm-10">
			<input type="text" class="form-control" name="mail" value="">
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-2 control-label"><?php echo $lang['password']; ?></label>
		<div class="col-sm-10">
			<input type="password" class="form-control" name="psw" value="">
		</div>
	</div>
	
	<hr>
	
	<div class="form-group">
		<label class="col-sm-2 control-label"><?php echo $lang['prefs_cms_domain']; ?></label>
		<div class="col-sm-10">
			<input type="text" class="form-control" name="prefs_cms_domain" value="<?php echo"$prefs_cms_domain"; ?>">
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-2 control-label"><?php echo $lang['prefs_cms_ssl_domain']; ?></label>
		<div class="col-sm-10">
			<input type="text" class="form-control" name="prefs_cms_ssl_domain" value="<?php echo"$prefs_cms_ssl_domain"; ?>">
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-2 control-label"><?php echo $lang['prefs_cms_base']; ?></label>
		<div class="col-sm-10">
			<input type="text" class="form-control" name="prefs_cms_base" value="<?php echo"$prefs_cms_base"; ?>">
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-2 control-label"></label>
		<div class="col-sm-10">
			<input type="submit" class="btn btn-success btn-block" name="step3" value="<?php echo $lang['start_install']; ?>">
		</div>
	</div>


</form>