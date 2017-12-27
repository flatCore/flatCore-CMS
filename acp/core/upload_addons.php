<?php

//prohibit unauthorized access
require 'core/access.php';
require_once 'core/pclzip.lib.php';

echo '<div class="alert alert-info">';
echo '<p>'.$lang['section_is_beta'].'</p>';
echo '</div>';

echo '<fieldset>';
echo '<legend>Upload</legend>';
echo '<div class="row">';
echo '<div class="col-md-4">';
echo '<div class="well well-sm">';
echo '<form action="core/files.upload-script.php" id="dropAddons" class="dropzone dropzone-plugin dropzone-sm">';
echo '<input type="hidden" name="upload_type" value="plugin">';
echo '<div class="fallback"><input name="file" type="file"></div>';
echo '</form>';
echo '</div>';
echo '</div>';
echo '<div class="col-md-4">';
echo '<div class="well well-sm">';
echo '<form action="core/files.upload-script.php" id="dropAddons" class="dropzone dropzone-module dropzone-sm">';
echo '<input type="hidden" name="upload_type" value="module">';
echo '<div class="fallback"><input name="file" type="file"></div>';
echo '</form>';
echo '</div>';
echo '</div>';
echo '<div class="col-md-4">';
echo '<div class="well well-sm">';
echo '<form action="core/files.upload-script.php" id="dropAddons" class="dropzone dropzone-theme dropzone-sm">';
echo '<input type="hidden" name="upload_type" value="theme">';
echo '<div class="fallback"><input name="file" type="file"></div>';
echo '</form>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</fieldset>';

/* delete files */
if(!empty($_GET['del'])) {
	$file = basename($_GET['del']);
	$path = basename($_GET['dir']);
	if(is_file("../upload/$path/$file")) {
		unlink("../upload/$path/$file");
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
 
if(!empty($_GET['plg'])) {
	
	if(!is_dir("../upload/plugins/extract")) {
		mkdir("../upload/plugins/extract", 0777);
	}
	unset($all_files);
	$plugin = basename($_GET['plg']);
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

	if(is_array($all_files)) {
		foreach($all_files as $f) {
			
			$i++;
			$target = "../content/plugins/" . substr($f, strlen("../upload/plugins/extract/$extracted/"));
			$status = copy_recursive("$f","$target");
		
			if($status == 'success') {
				$show_status = "<span class='label label-success'>ok</span>";
			} else {
				$show_status = '<span class="label label-danger">'.$status.'</span>';
				$cnt_errors++;
			}
			
			echo '<dl class="dl-horizontal">';
			echo '<dt>'.$i.'</dt>';
			echo '<dd>from: extract/(..)'. basename($f) .'<br>to: '. $target .' '. $show_status .'</dd>';
			echo '</dl>';
		
		}
		
		if($cnt_errors < 1) {
			rmdir_recursive("../upload/plugins/extract");
			echo '<div class="alert alert-success">Plugin installed</div>';
		}
		
	}
}	


/**
 * install modules
 * 1. extract zip file
 * 2. find xyz.mod directory
 * 3. copy xyz.mod and it's contents to /modules/
 */
 
if(!empty($_GET['mod'])) {
	
	if(!is_dir("../upload/modules/extract")) {
		mkdir("../upload/modules/extract", 0777);
	}
	
	$mod = basename($_GET['mod']);
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
	
	if(is_array($all_files)) {
		foreach($all_files as $f) {
			
			$i++;
			$target = "../modules/" . substr($f, strlen("../upload/modules/extract/$extracted/"));
			$status = copy_recursive("$f","$target");
		
			if($status == 'success') {
				$show_status = "<span class='label label-success'>ok</span>";
			} else {
				$show_status = '<span class="label label-danger">'.$status.'</span>';
				$cnt_errors++;
			}
			
			echo '<dl class="dl-horizontal">';
			echo '<dt>'.$i.'</dt>';
			echo '<dd>from: extract/(..)'. basename($f) .'<br>to: '. $target .' '. $show_status .'</dd>';
			echo '</dl>';
		
		}
		if($cnt_errors < 1) {
			rmdir_recursive("../upload/modules/extract");
			echo '<div class="alert alert-success">Module installed</div>';
		}
	}
	
}

/**
 * install themes
 * 1. extract zip file
 * 2. find theme folder from contents.php
 * 3. copy theme folder and it's contents to /styles/
 */
if(!empty($_GET['theme'])) {
	
	if(!is_dir("../upload/themes/extract")) {
		mkdir("../upload/themes/extract", 0777);
	}
	unset($all_files);
	$theme = basename($_GET['theme']);
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
			/* themes root folder ($instRootDir) must be defined in contents.php */
			$all_files = fc_scandir_rec("../upload/themes/extract/$extracted/$instRootDir");
		} else {
			echo '<div class="alert alert-danger">This is not a compatible Theme</div>';
		}
		
	} else {
		echo '<div class="alert alert-danger">No Source found: '. $extracted .'</div>';
	}
	
	
	if(is_array($all_files)) {
		foreach($all_files as $f) {
			
			$i++;
			$target = "../styles/" . substr($f, strlen("../upload/themes/extract/$extracted/"));
			$status = copy_recursive("$f","$target");
		
			if($status == 'success') {
				$show_status = "<span class='label label-success'>ok</span>";
			} else {
				$show_status = '<span class="label label-danger">'.$status.'</span>';
				$cnt_errors++;
			}
			
			echo '<dl class="dl-horizontal">';
			echo '<dt>'.$i.'</dt>';
			echo '<dd>from: extract/(..)'. basename($f) .'<br>to: '. $target .' '. $show_status .'</dd>';
			echo '</dl>';
		
		}
		
		if($cnt_errors < 1) {
			rmdir_recursive("../upload/themes/extract");
			echo '<div class="alert alert-success">Theme installed</div>';
		}
		
	}
}



/* list uploaded files */

if(is_dir('../upload')) {
	$all_uploads = fc_scandir_rec('../upload');
	
	echo '<fieldset>';
	echo '<legend>'.$lang['label_ready_to_install'].'</legend>';
	
	if(count($all_uploads) < 1) {
		echo '<p class="text-muted">'.$lang['msg_nothing_to_install'].'</p>';
	} else {
	
		echo '<table class="table table-condensed">';
		foreach($all_uploads as $upload) {
			
			$this_pathinfo = pathinfo($upload);
			$filemtime = date("Y-m-d H:i:s",filemtime($upload));
			$pathinfo = print_r($this_pathinfo,true);
			
			if($this_pathinfo['dirname'] == '../upload/modules') {
				
				echo '<tr>';
				echo '<td>Module:</td><td><strong>'.$this_pathinfo['basename'].'</strong> <small>Upload time:'.$filemtime.'</small></td>';
				echo '<td>';
				echo '<div class="btn-group pull-right">';
				echo '<a href="?tn=moduls&sub=u&mod='.$this_pathinfo['basename'].'" class="btn btn-default">Install</a>';
				echo '<a href="?tn=moduls&sub=u&dir=modules&del='.$this_pathinfo['basename'].'" class="btn btn-danger">'.$lang['delete'].'</a>';
				echo '</div>';
				echo '</td>';
				echo '</tr>';
				
			} else if($this_pathinfo['dirname'] == '../upload/plugins') {
				
				echo '<tr>';
				echo '<td>Plugin:</td><td><strong>'.$this_pathinfo['basename'].'</strong> <small>Upload time:'.$filemtime.'</small></td>';
				echo '<td>';
				echo '<div class="btn-group pull-right">';
				echo '<a href="?tn=moduls&sub=u&plg='.$this_pathinfo['basename'].'" class="btn btn-default">Install</a>';
				echo '<a href="?tn=moduls&sub=u&dir=plugins&del='.$this_pathinfo['basename'].'" class="btn btn-danger">'.$lang['delete'].'</a>';
				echo '</div>';
				echo '</td>';
				echo '</tr>';
				
			} else if($this_pathinfo['dirname'] == '../upload/themes') {
	
				echo '<tr>';
				echo '<td>Theme:</td><td><strong>'.$this_pathinfo['basename'].'</strong> <small>Upload time:'.$filemtime.'</small></td>';
				echo '<td>';
				echo '<div class="btn-group pull-right">';
				echo '<a href="?tn=moduls&sub=u&theme='.$this_pathinfo['basename'].'" class="btn btn-default">Install</a>';
				echo '<a href="?tn=moduls&sub=u&dir=themes&del='.$this_pathinfo['basename'].'" class="btn btn-danger">'.$lang['delete'].'</a>';
				echo '</div>';
				echo '</td>';
				echo '</tr>';
				
			}
			
			
		}
		echo '</table>';
		
	}
	
	
	echo '<fieldset>';
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