<?php

//prohibit unauthorized access
require("core/access.php");


$prefs = get_preferences();
$form_img_action = "core/files.upload-script.php?w=$prefs[prefs_maximagewidth]&h=$prefs[prefs_maximageheight]&fz=$prefs[prefs_maxfilesize]&d=$img_path";
$files_d = FILES_FOLDER;
$form_files_action = "core/files.upload-script.php?d=$files_d";

?>

<fieldset>
		<legend><?php echo"$lang[upload_img_legend]";  ?></legend>
		
		<div id="hiddenImgMessage" class="alert alert-success" style="display:none;"><?php echo $lang['upload_complete']; ?></div>
		
	<form method="post" action="<?php echo $form_img_action ?>" enctype="multipart/form-data">


		<div class="formRow">
			<label for="url" class="floated">File: </label>
			<input type="file" id="imagesToUpload" name="imagesToUpload[]" multiple><br>
		</div>

		<div class="formfooter">
			<input type="submit" name="upload" value="<?php echo"$lang[upload_target_images]";  ?>" class="btn" />
		</div>

	</form>
</fieldset>

<script>

window.addEvent('domready', function(){

	var upload = new Form.Upload('imagesToUpload', {
		onComplete: function(){
		
			$('hiddenImgMessage').setStyles({
				display: 'block'
    	});
			
			
		}
	});


});

</script>

<fieldset>
		<legend><?php echo"$lang[upload_files_legend]";  ?></legend>
		<div id="hiddenFilesMessage" class="alert alert-success" style="display:none;"><?php echo $lang['upload_complete']; ?></div>
		<form method="post" action="<?php echo $form_files_action ?>" enctype="multipart/form-data">


		<div class="formRow">
			<label for="url" class="floated">File: </label>
			<input type="file" id="filesToUpload" name="filesToUpload[]" multiple><br>
		</div>

		<div class="formfooter">
			<input type="submit" name="upload" value="<?php echo"$lang[upload_target_files]";  ?>" class="btn" />
		</div>

	</form>
</fieldset>

<script>

window.addEvent('domready', function(){

	var upload = new Form.Upload('filesToUpload', {
		onComplete: function(){
			$('hiddenFilesMessage').setStyles({
				display: 'block'
    	});
		}
	});


});

</script>


