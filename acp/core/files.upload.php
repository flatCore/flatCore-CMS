<?php

//prohibit unauthorized access
require("core/access.php");

?>

<div class="row">
<div class="col-md-6">
<fieldset>
	<legend><?php echo $lang['upload_img_legend']; ?></legend>
	<form action="core/files.upload-script.php" id="myDropzone" class="dropzone">
		<input type="hidden" name="w" value="<?php echo $fc_preferences['prefs_maximagewidth']; ?>" />
		<input type="hidden" name="h" value="<?php echo $fc_preferences['prefs_maximageheight']; ?>" />
		<input type="hidden" name="fz" value="<?php echo $fc_preferences['prefs_maxfilesize']; ?>" />
		<input type="hidden" name="d" value="images" />
		<input type="hidden" name="upload_type" value="images" />
		<div class="fallback">
			<input name="file" type="file" multiple />
		</div>
	</form>
	<hr>
	<?php echo '<small>' . $fc_preferences['prefs_imagesuffix'] . '</small>'; ?>
</fieldset>
</div>
<div class="col-md-6">
<fieldset>
	<legend><?php echo $lang['upload_files_legend']; ?></legend>
		<form action="core/files.upload-script.php" id="my-dropzone2" class="dropzone">
			<input type="hidden" name="d" value="files" />
			<input type="hidden" name="upload_type" value="files" />
			<div class="fallback">
				<input name="file" type="file" multiple />
			</div>
		</form>
		<hr>
		<?php echo '<small>' . $fc_preferences['prefs_filesuffix'] . '</small>'; ?>
</fieldset>
	</div>
</div>