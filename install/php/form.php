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

	<fieldset>
		<legend>USER</legend>
			<div class="form-group row">
				<label class="col-sm-3 control-label text-right"><?php echo $lang['username']; ?></label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="username" value="">
				</div>
			</div>
		
			<div class="form-group row">
				<label class="col-sm-3 control-label text-right"><?php echo $lang['email']; ?></label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="mail" value="">
				</div>
			</div>
			
			<div class="form-group row">
				<label class="col-sm-3 control-label text-right"><?php echo $lang['password']; ?></label>
				<div class="col-sm-9">
					<input type="password" class="form-control" name="psw" value="">
				</div>
			</div>
	</fieldset>
	
	<fieldset>
		<legend>Domain</legend>
	
			<div class="form-group row">
				<label class="col-sm-3 control-label text-right"><?php echo $lang['prefs_cms_domain']; ?></label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="prefs_cms_domain" value="<?php echo"$prefs_cms_domain"; ?>">
				</div>
			</div>
			
			<div class="form-group row">
				<label class="col-sm-3 control-label text-right"><?php echo $lang['prefs_cms_ssl_domain']; ?></label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="prefs_cms_ssl_domain" value="<?php echo"$prefs_cms_ssl_domain"; ?>">
				</div>
			</div>
			
			<div class="form-group row">
				<label class="col-sm-3 control-label text-right"><?php echo $lang['prefs_cms_base']; ?></label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="prefs_cms_base" value="<?php echo"$prefs_cms_base"; ?>">
				</div>
			</div>
	
	</fieldset>
	
	<fieldset>
		<legend>Database</legend>
		
		
		

			
		<ul class="nav nav-pills mb-3" id="myTab" role="tablist">
		    <li class="nav-item" role="presentation">
		    	<a class="nav-link active" data-toggle="pill" id="sqlite-tab" href="#sqlite">SQLite</a>
		    </li>
		    <li class="nav-item" role="presentation">
		    	<a class="nav-link" data-toggle="pill" id="mysql-tab" href="#mysql">MySQL</a>
		    </li>
		</ul>
			
			<div class="tab-content" id="pills-tabContent">
				<div class="tab-pane fade show active" id="sqlite" role="tabpanel">
					<p class="alert alert-info">No more settings needed</p>
				</div>
				<div class="tab-pane fade" id="mysql" role="tabpanel">
		
					<div class="form-group row">
						<label class="col-sm-3 control-label text-right">Host</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="prefs_database_host" placeholder="localhost" value="<?php echo"$prefs_database_host"; ?>">
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 control-label text-right">Port</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="prefs_database_port" placeholder="localhost" value="<?php echo"$prefs_database_port"; ?>">
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 control-label text-right">Name</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="prefs_database_name" value="<?php echo"$prefs_database_name"; ?>">
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 control-label text-right">Username</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="prefs_database_username" value="<?php echo"$prefs_database_username"; ?>">
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 control-label text-right">Password</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="prefs_database_psw" value="<?php echo"$prefs_database_psw"; ?>">
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-sm-3 control-label text-right">Table Prefix</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="prefs_database_prefix" placeholder="fcdb_" value="<?php echo"$prefs_database_prefix"; ?>">
						</div>
					</div>
				</div>
				
			</div>
	</fieldset>
	
	<div class="form-group row">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-9">
			<input type="submit" class="btn btn-success btn-block" name="step3" value="<?php echo $lang['start_install']; ?>">
		</div>
	</div>


</form>