<?php

//prohibit unauthorized access
require 'core/access.php';
require_once 'core/pclzip.lib.php';

$danger_zone_lifetime = 300;



if($_POST['confirm_danger_zone'] == $_SESSION['user_nick']) {
	$_SESSION['confirmed_danger_zone'] = 'confirmed';
	$_SESSION['confirmed_danger_zone_time'] = time();
}

if((time() - $_SESSION['confirmed_danger_zone_time']) > $danger_zone_lifetime) {
	$_SESSION['confirmed_danger_zone'] = '';
}

if($_SESSION['confirmed_danger_zone'] !== 'confirmed') {

	/* confirm that you know it is dangerous here */
	echo '<form action="?tn=moduls&sub=u" method="POST">';
	echo '<div class="alert alert-danger mb-4">';
	echo '<p>'.$lang['section_is_beta'].'</p>';
	echo '<p>'.$lang['section_is_danger_zone'].'</p>';
	echo '<button type="submit" class="btn btn-danger" name="confirm_danger_zone" value="'.$_SESSION['user_nick'].'">OKAY, I GET IT</button>';
	echo $hidden_csrf_token;
	echo '</div>';
	echo '</form><hr>';

} else {

	echo '<fieldset class="mt-4">';
	echo '<legend>Upload</legend>';
	echo '<div class="row">';
	echo '<div class="col-md-4">';
	echo '<div class="well well-sm">';
	echo '<form action="core/files.upload-script.php" id="dropAddons" class="dropzone dropzone-plugin dropzone-sm">';
	echo '<input type="hidden" name="upload_type" value="plugin">';
	echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
	echo '<div class="fallback"><input name="file" type="file"></div>';
	echo '</form>';
	echo '</div>';
	echo '</div>';
	echo '<div class="col-md-4">';
	echo '<div class="well well-sm">';
	echo '<form action="core/files.upload-script.php" id="dropAddons" class="dropzone dropzone-module dropzone-sm">';
	echo '<input type="hidden" name="upload_type" value="module">';
	echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
	echo '<div class="fallback"><input name="file" type="file"></div>';
	echo '</form>';
	echo '</div>';
	echo '</div>';
	echo '<div class="col-md-4">';
	echo '<div class="well well-sm">';
	echo '<form action="core/files.upload-script.php" id="dropAddons" class="dropzone dropzone-theme dropzone-sm">';
	echo '<input type="hidden" name="upload_type" value="theme">';
	echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
	echo '<div class="fallback"><input name="file" type="file"></div>';
	echo '</form>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</fieldset>';

}


/* delete uploaded zip files */

if(isset($_POST['delete_uploaded_zip'])) {
	$file = basename($_POST['delete_uploaded_zip']);
	
	if($_POST['dir'] == 't') {
		$dir = 'themes';
	} else if($_POST['dir'] == 'm') {
		$dir = 'modules';
	} else {
		$dir = 'plugins';
	}

	
	if(is_file("../upload/$dir/$file")) {
		unlink("../upload/$dir/$file");
	}
	
}




/* check if we can write in /styles/, /modules/ and /content/plugins/ /*/

if(!is_writable('../styles/')) {
	echo '<p class="alert alert-danger">'.sprintf($lang['dir_must_be_writable'],'/styles/').'</p>';
}

if(!is_writable('../modules/')) {
	echo '<p class="alert alert-danger">'.sprintf($lang['dir_must_be_writable'],'/modules/').'</p>';
}

if(!is_writable('../content/plugins/')) {
	echo '<p class="alert alert-danger">'.sprintf($lang['dir_must_be_writable'],'/content/plugins/').'</p>';
}


/**
 * plugins, themes and modules must be contained in a zip archive
 * the zip archive for themes must contain a file 'contents.php'
 * in this file you can define which files have to be copied
 * the root folder of your sources must be specified (relative path)
 *
 * example:
 * $instRootDir = 'myThemefolder';
 * $instFiles = 'all';
 *
 */



/**
 * install plugins
 * 1. extract zip file
 * 2. get files from extracted directory
 * copy the files from ../upload/plugins/ to content/plugins/)
 *
 */
 
if(isset($_POST['install_uploaded_plg'])) {
	
	if(!is_dir("../upload/plugins/extract")) {
		mkdir("../upload/plugins/extract", 0777);
	}
	unset($all_files);
	$plugin = basename($_POST['install_uploaded_plg']);
	$archive = new PclZip("../upload/plugins/$plugin");
	$list = $archive->extract(
			PCLZIP_OPT_PATH, '../upload/plugins/extract',
			PCLZIP_OPT_STOP_ON_ERROR,
			PCLZIP_OPT_SET_CHMOD, 0777
			);
	if($list == 0) {
		echo "ERROR : ".$archive->errorInfo(true);
	}
	$extracted = basename("$plugin",".zip");
	
	if(is_dir("../upload/plugins/extract/$extracted"))	{
		
		$all_files = fc_scandir_rec("../upload/plugins/extract/$extracted");
		
	} else {
		echo '<div class="alert alert-danger">No Source found: '. $extracted .'</div>';
	}

	echo '<div class="scroll-container">';
	if(is_array($all_files)) {
		foreach($all_files as $f) {
			
			$i++;
			$target = "../content/plugins/" . substr($f, strlen("../upload/plugins/extract/$extracted/"));
			$status = copy_recursive("$f","$target");
		
			if($status == 'success') {
				$show_status = "<span class='badge badge-success'>ok</span>";
			} else {
				$show_status = '<span class="badge bg-danger">'.$status.'</span>';
				$cnt_errors++;
			}
			
			echo '<dl class="row">';
			echo '<dt class="col-sm-1 text-right">'.$i.'</dt>';
			echo '<dd class="col-sm-11">from: <code>extract/(..)'. basename($f) .'</code> to: <code>'. $target .' '. $show_status .'</code></dd>';
			echo '</dl>';
		
		}
		
		if($cnt_errors < 1) {
			rmdir_recursive("../upload/plugins/extract");
			echo '<div class="alert alert-success">Plugin installed</div>';
		}
		
	}
	echo '</div>';
}	


/**
 * install modules
 * 1. extract zip file
 * 2. find xyz.mod directory
 * 3. copy xyz.mod and it's contents to /modules/
 */
 
if(isset($_POST['install_uploaded_mod'])) {
	
	if(!is_dir("../upload/modules/extract")) {
		mkdir("../upload/modules/extract", 0777);
	}
	
	$mod = basename($_POST['install_uploaded_mod']);
	$archive = new PclZip("../upload/modules/$mod");
	$list = $archive->extract(
			PCLZIP_OPT_PATH, '../upload/modules/extract',
			PCLZIP_OPT_STOP_ON_ERROR,
			PCLZIP_OPT_SET_CHMOD, 0777
			);
	if($list == 0) {
		echo "ERROR : ".$archive->errorInfo(true);
	}
	
	$extracted = basename("$mod",".zip");
	if(is_dir("../upload/modules/extract/$extracted"))	{
		$all_files = fc_scandir_rec("../upload/modules/extract/$extracted");
	} else {
		echo '<div class="alert alert-danger">No Source found: '. $extracted .'</div>';
	}
	
	echo '<div class="scroll-container">';
	if(is_array($all_files)) {
		foreach($all_files as $f) {
			
			$i++;
			$target = "../modules/" . substr($f, strlen("../upload/modules/extract/$extracted/"));
			$status = copy_recursive("$f","$target");
		
			if($status == 'success') {
				$show_status = "<span class='badge badge-success'>ok</span>";
			} else {
				$show_status = '<span class="badge bg-danger">'.$status.'</span>';
				$cnt_errors++;
			}
			
			echo '<dl class="row">';
			echo '<dt class="col-sm-1 text-right">'.$i.'</dt>';
			echo '<dd class="col-sm-11">from: <code>extract/(..)'. basename($f) .'</code> to: <code>'. $target .' '. $show_status .'</code></dd>';
			echo '</dl>';
		
		}
		if($cnt_errors < 1) {
			rmdir_recursive("../upload/modules/extract");
			echo '<div class="alert alert-success">Module installed</div>';
		}
	}
	echo '</div>';
	
}


/**
 * install themes
 * 1. extract zip file
 * 2. find theme folder from contents.php
 * 3. copy theme folder and it's contents to /styles/
 */
 
if(isset($_POST['install_uploaded_tpl'])) {
	
	if(!is_dir("../upload/themes/extract")) {
		mkdir("../upload/themes/extract", 0777);
	}
	unset($all_files);
	$theme = basename($_POST['install_uploaded_tpl']);
	$archive = new PclZip("../upload/themes/$theme");
	$list = $archive->extract(
			PCLZIP_OPT_PATH, '../upload/themes/extract',
			PCLZIP_OPT_STOP_ON_ERROR,
			PCLZIP_OPT_SET_CHMOD, 0777
			);
	if($list == 0) {
		echo "ERROR : ".$archive->errorInfo(true);
	}
	
	$extracted = basename("$theme",".zip");
	if(is_dir("../upload/themes/extract/$extracted"))	{
		
		if(is_file("../upload/themes/extract/$extracted/contents.php")) {
			include '../upload/themes/extract/'.$extracted.'/contents.php';
			// themes root folder ($instRootDir) must be defined in contents.php 
			$all_files = fc_scandir_rec("../upload/themes/extract/$extracted/$instRootDir");
		} else {
			echo '<div class="alert alert-danger">This is not a compatible Theme</div>';
		}
		
	} else {
		echo '<div class="alert alert-danger">No Source found: '. $extracted .'</div>';
	}
	
	echo '<div class="scroll-container">';
	if(is_array($all_files)) {
		foreach($all_files as $f) {
			
			$i++;
			$target = "../styles/" . substr($f, strlen("../upload/themes/extract/$extracted/"));
			$status = copy_recursive("$f","$target");
		
			if($status == 'success') {
				$show_status = "<span class='badge badge-success'>ok</span>";
			} else {
				$show_status = '<span class="badge bg-danger">'.$status.'</span>';
				$cnt_errors++;
			}
			
			echo '<dl class="row">';
			echo '<dt class="col-sm-1 text-right">'.$i.'</dt>';
			echo '<dd class="col-sm-11">from: <code>extract/(..)'. basename($f) .'</code> to: <code>'. $target .' '. $show_status .'</code></dd>';
			echo '</dl>';
		
		}
		
		if($cnt_errors < 1) {
			rmdir_recursive("../upload/themes/extract");
			echo '<div class="alert alert-success">Theme installed</div>';
		}
		
	}
	echo '</div>';
}



/* list uploaded files */
if($_SESSION['confirmed_danger_zone'] == 'confirmed') {
	if(is_dir('../upload')) {
		$all_uploads = fc_scandir_rec('../upload');
		
		echo '<fieldset class="mt-3">';
		echo '<legend>'.$lang['label_ready_to_install'].'</legend>';
		
		if(count($all_uploads) < 1) {
			echo '<p class="text-muted">'.$lang['msg_nothing_to_install'].' <a href="?tn=moduls&sub=u" class="btn btn-link">'.$icon['sync_alt'].' reload</a></p>';
		} else {
		
			echo '<table class="table table-condensed">';
			foreach($all_uploads as $upload) {
				
				$this_pathinfo = pathinfo($upload);
				$filemtime = date("Y-m-d H:i:s",filemtime($upload));
				$pathinfo = print_r($this_pathinfo,true);
				
				if($this_pathinfo['dirname'] == '../upload/modules') {
					
					echo '<tr>';
					echo '<td>Module:</td><td><strong>'.$this_pathinfo['basename'].'</strong> <small>Upload time: '.$filemtime.'</small></td>';
					echo '<td>';
					echo '<div class="btn-group float-end">';
					
					echo '<form action="?tn=moduls&sub=u" method="POST">';
					echo '<button class="btn btn-fc text-success" type="submit" name="install_uploaded_mod" value="'.$this_pathinfo['basename'].'">Install</button>';
					echo '<button class="btn btn-fc text-danger" type="submit" name="delete_uploaded_zip" value="'.$this_pathinfo['basename'].'">Remove</button>';
					echo '<input type="hidden" name="dir" value="m">';
					echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
					echo '</form>';
					
					echo '</div>';
					echo '</td>';
					echo '</tr>';
					
				} else if($this_pathinfo['dirname'] == '../upload/plugins') {
					
					echo '<tr>';
					echo '<td>Plugin:</td><td><strong>'.$this_pathinfo['basename'].'</strong> <small>Upload time: '.$filemtime.'</small></td>';
					echo '<td>';
					echo '<div class="btn-group float-end">';
					
					echo '<form action="?tn=moduls&sub=u" method="POST">';
					echo '<button class="btn btn-fc text-success" type="submit" name="install_uploaded_plg" value="'.$this_pathinfo['basename'].'">Install</button>';
					echo '<button class="btn btn-fc text-danger" type="submit" name="delete_uploaded_zip" value="'.$this_pathinfo['basename'].'">Remove</button>';
					echo '<input type="hidden" name="dir" value="p">';
					echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
					echo '</form>';
					
					echo '</div>';
					echo '</td>';
					echo '</tr>';
					
				} else if($this_pathinfo['dirname'] == '../upload/themes') {
		
					echo '<tr>';
					echo '<td>Theme:</td><td><strong>'.$this_pathinfo['basename'].'</strong> <small>Upload time: '.$filemtime.'</small></td>';
					echo '<td>';
					echo '<div class="btn-group float-end">';
					
					echo '<form action="?tn=moduls&sub=u" method="POST">';
					echo '<button class="btn btn-fc text-success" type="submit" name="install_uploaded_tpl" value="'.$this_pathinfo['basename'].'">Install</button>';
					echo '<button class="btn btn-fc text-danger" type="submit" name="delete_uploaded_zip" value="'.$this_pathinfo['basename'].'">Remove</button>';
					echo '<input type="hidden" name="dir" value="t">';
					echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
					echo '</form>';
					
					echo '</div>';
					echo '</td>';
					echo '</tr>';	
				}
			}
			echo '</table>';	
		}
		echo '<fieldset>';
	}
}





/**
 * copy/move directory with its including contents
 */
 
function copy_recursive($source, $target) {
	
	$path_parts = pathinfo($target);
	if(!is_dir($path_parts['dirname'])) {
		mkdir($path_parts['dirname'], 0777, true);
	}
	
	if(is_dir($source)) {
	
		if(!is_dir("$target")) {
			mkdir("$target", 0777, true);
		}
		
		$dir = dir($source);
		while(FALSE !== ($entry = $dir->read())) {
			
			if($entry == '.' || $entry == '..') { continue; }
			
			$sub = $source . '/' . $entry;
			
			if(is_dir($sub)) {
				@chmod("$sub", 0777);
				copy_recursive($sub, $target . '/' . $entry);
				continue;
			}
			copy($sub, $target . '/' . $entry);
		}
 
		$dir->close();
	} else {
		@chmod("$target", 0777);
		@unlink("$target");
		if(copy($source, $target)) {
			return 'success';
		} else {
			$errors = error_get_last();
			return $errors['message'];
		}
	}
}



/**
 * delete directory (recursive)
 */

 function rmdir_recursive($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if(filetype($dir."/".$object) == "dir") {
         	rmdir_recursive($dir."/".$object);
         	} else {
         		unlink($dir."/".$object);
         	}
       }
     }
     reset($objects);
     rmdir($dir);
   }
 }


?>