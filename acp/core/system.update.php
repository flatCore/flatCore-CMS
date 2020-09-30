<?php
set_time_limit (0);

//prohibit unauthorized access
require 'core/access.php';
require_once 'core/pclzip.lib.php';
include 'updatelist.php';

define('INSTALLER', TRUE);
include '../install/php/functions.php';

$_SESSION['protocol'] = '';
$_SESSION['errors_cnt'] = 0;

/* build an array from all php files in folder ../install/contents */
$all_tables = glob("../install/contents/*.php");

$remote_versions_file = file_get_contents("https://flatCore.org/_updates/versions.txt");

if(isset($_GET['beta']) && $_GET['beta'] > 0) {
	$remote_versions_file = file_get_contents("https://flatCore.org/_updates/versions-beta.txt");
}

// example string: 2013-06-29<|>Release Candidate 3<|>39<|>fc_b39.zip
$remote_versions = explode("<|>",$remote_versions_file);

if(isset($_GET['alpha']) && $_GET['alpha'] > 0) {
	$remote_versions_file = file_get_contents("https://flatCore.org/_updates/versions-alpha.txt");
}

if(isset($_GET['github']) && $_GET['github'] == 'master') {
	$remote_versions[0] = '';
	$remote_versions[1] = 'GitHub';
	$remote_versions[2] = 'master';
	$remote_versions[3] = 'flatCore-CMS-master.zip';
}

if(isset($_GET['github']) && $_GET['github'] == 'develop') {
	$remote_versions[0] = '';
	$remote_versions[1] = 'GitHub';
	$remote_versions[2] = 'develop';
	$remote_versions[3] = 'flatCore-CMS-develop.zip';
}

echo '<fieldset>';
echo '<legend>'.$lang['system_update'].'</legend>';

compare_versions();

if(isset($_GET['a']) && $_GET['a'] == 'start') {
	start_update();
}

echo '</fieldset>';


if(isset($_GET['a']) && $_GET['a'] == 'start') {
	echo '<div style="height:350px;overflow:auto;margin:0;" class="well well-sm">';
	echo '<h3>ERRORS: '.$_SESSION['errors_cnt'].'</h3>';
	$protocol = explode('<|>', $_SESSION['protocol']);
	$protocol = array_filter($protocol);
	echo '<ul>';
	foreach($protocol as $v) {
		echo '<li>'.$v. '</li>';
	}
	echo '</ul>';
	echo '</div>';
}
	
/**
 * start the update
 * 1. load the zip file from flatcore.org...
 * 2. mkdir acp/update and acp/update/extract 
 * 		copy the zip file into /acp/update and extract the files
 * 3. copy the file maintance.html to the root (starts the update modus in frontend)
 * 4. copy the files to their destination
 * 5. run the updatescript and check up the database
 * 6. delete maintance.html from root - (ends the update modus in frontend)
 *
 */


function start_update() {

	global $remote_versions;
	global $fc_content_files;
	global $fc_db_content;
	
	$get_file = $remote_versions[3];
	
	if($remote_versions[3] == 'flatCore-CMS-master.zip') {
		$source_file = 'https://github.com/flatCore/flatCore-CMS/archive/master.zip';
	} else if($remote_versions[3] == 'flatCore-CMS-develop.zip') {
		$source_file = 'https://github.com/flatCore/flatCore-CMS/archive/develop.zip';
	} else {
		$source_file = 'https://flatcore.org/_updates/zip/'.$get_file;
	}
	
	mkdir("update", 0777);
	mkdir("update/extract", 0777);

	if(is_dir("update")) {
		copy("$source_file","./update/$get_file");
	}
		
	$archive = new PclZip("update/$get_file");
	
	$list = $archive->extract(
			PCLZIP_OPT_PATH, 'update/extract',
			PCLZIP_OPT_STOP_ON_ERROR,
			PCLZIP_OPT_SET_CHMOD, 0777
			);
			
	if($list == 0) {
		echo "ERROR : ".$archive->errorInfo(true);
	}
	
	copy('../install/maintance.html', '../maintance.html');
	
	move_new_files();

	if(!is_array($fc_content_files)) {
		/* update single file database */
		update_database($fc_db_content);
	} else {
		/* update multisite database */
		for($i=0;$i<count($fc_content_files);$i++) {
			$db = 'content/SQLite/'.$fc_content_files[$i]['file'];
			update_database($db);
		}
	}
	remove_old_files();
	
	/**
	 * remove the update and ../install directory
	 */
	
	rmdir_recursive("update");
	unlink("../maintance.html");
	
}



/**
 * get the files from the extracted zip file
 * and copy them to their destination
 */

function move_new_files() {

	global $remote_versions;
	$cnt_errors = 0;

	$get_file = basename("$remote_versions[3]",".zip");

	if(is_dir("update/extract/$get_file"))	{
		$new_files = scandir_recursive("update/extract/$get_file");
	} else {
		echo '<div class="alert alert-danger">No Source found: '. $get_file .'</div>';
	}
	
	
	/* at first, the install folder */
	copy_recursive("update/extract/$get_file/install","../install");
			
	/* now copy the other files and directories */
	foreach($new_files as $value) {
	
		$i++;
	
		if(preg_match("#\/install\/#i", "$value")) {
			continue;
		}
		
		if(preg_match("#\/content\/#i", "$value")) {
			continue;
		}
		
		if(preg_match("#\/modules\/#i", "$value")) {
			continue;
		}

		if(preg_match("#\/.github\/#i", "$value")) {
			continue;
		}
		
		if(substr(basename($value), 0,1) == ".") { continue;}
		if($value === '.' || $value === '..') {continue;}
		if(basename($value) == "README.md") { continue;}
		if(basename($value) == "robots.txt") { continue;}
		if(basename($value) == "_htaccess") { continue;}
		if(basename($value) == "CODE_OF_CONDUCT.md") { continue;}
		
			
		/**
		 * copy files from 'update/extract/*'
		 */
		$target = '../' . substr($value, strlen("update/extract/$get_file/"));
		$status = copy_recursive("$value","$target");

	}

	$_SESSION['errors_cnt'] = $cnt_errors;

}



/**
 * remove old versions or unused files
 * $remove_files from core/updatelist
 */

function remove_old_files() {
	global $remove_files;
	
	foreach ($remove_files as $file) {
    	if(is_file("../$file")) {
	    	unlink("../$file");
    	}    
   }
}



/**
 * compare installed and remote version
 */

function compare_versions() {

	global $lang;
	global $remote_versions;
	global $icon;
	
	if(is_file("versions.php")){
		include 'versions.php';
	} else {
		$fc_version_name = '';
		$fc_version_build = '';
	}
	
	echo '<table class="table table-condensed">';
	echo '<thead>';
	echo '<tr>
					<th>'.$icon['database'].'  '. $_SERVER['SERVER_NAME'] .'</th>
					<th>'.$icon['server'].' flatCore.org</th>
					<th width="33%">'.$icon['github'].' GitHub (preview)</th>
				</tr>';
	echo '</thead>';
	
	echo '<tr>';
	echo "<td>$fc_version_name (Build $fc_version_build)</td>";
	echo "<td>$remote_versions[1] (Build $remote_versions[2])</td>";
	echo '<td><span class="badge badge-danger">Attention!</span> You should not perform this function in a real environment. Load latest from  <a href="?tn=system&sub=update&github=master">branch master</a> or load latest from <a href="?tn=system&sub=update&github=develop">branch develop</a></td>';
	echo '</tr>';
	echo '</table>';
	
	/* compare build numbers */
		
	$start_dl = '';
	
	if(($remote_versions[2] > $fc_version_build) && ($remote_versions[2] != 'master')) {
		$start_dl  = 'acp.php?tn=system&sub=update&a=start';
		if(isset($_GET['beta']) && $_GET['beta'] > 0) {
			$start_dl .= '&beta=1';
		}
	}
	if($remote_versions[2] == 'master') {
		$start_dl  = 'acp.php?tn=system&sub=update&a=start&github=master';
	}
	if($remote_versions[2] == 'develop') {
		$start_dl  = 'acp.php?tn=system&sub=update&a=start&github=develop';
	}
	
	if($start_dl != '') {
		echo '<div class="alert alert-info"><p>' . $lang['msg_update_available'] . '</p><hr>';
		echo '<p>';
		echo '<a href="'.$start_dl.'" class="btn btn-success">'.$icon['sync_alt'].' Update</a>';
		echo '</p>';
		echo '</div>';		
	} else {
		echo '<div class="alert alert-success"><p>' . $lang['msg_no_update_available'] . '</p></div>';
	}

}


/**
 * returns all files and directories
 * return array()
 */

function scandir_recursive($dir) { 
	$root = scandir($dir); 
  foreach($root as $value) { 
  	if($value === '.' || $value === '..') {continue;} 
	  $result[]="$dir/$value";
	  if(is_dir("$dir/$value")) {
	    foreach(scandir_recursive("$dir/$value") as $value) { 
	    	$result[]=$value; 
	    }
    }
   }
   $result = array_filter($result);
   return $result;  
}


/**
 * copy/move directory with its including contents
 */
 
function copy_recursive($source, $target) {
	
	if(is_dir($source)) {
		if(!is_dir("$target")) {
			$_SESSION['protocol'] .= "missing: $target <|>";
			mkdir_recursive($target,0777);
		}
		
		$dir = dir($source);
		while(FALSE !== ($entry = $dir->read())) {
			
			if($entry == '.' || $entry == '..') { continue; }
			
			$sub = $source . '/' . $entry;
			
			if(is_dir($sub)) {
				chmod("$sub", 0755);
				copy_recursive($sub, $target . '/' . $entry);
				//continue;
			}
			copy($sub, $target . '/' . $entry);
		}
 
		$dir->close();
	} else {
		chmod("$target", 0777);
		unlink("$target");
		if(copy($source, $target)) {
			$_SESSION['protocol'] .= '<b>copied:</b> '.$target.'<|>';
		} else {
			$errors = error_get_last();
			$_SESSION['protocol'] .= '<b class="text-danger">ERROR:</b> '.$errors['type']. '</b> ' . $errors['message'].'<|>';
			$_SESSION['errors_cnt']++;
			return $error_msg;
		}
	}
}



/**
 * Update the database
 */

function update_database($dbfile) {
	
	global $fc_db_user;
	global $fc_db_stats;
	global $fc_db_index;
	global $all_tables;

	/* build an array from all php files in folder ../install/contents */
	$all_tables = glob("../install/contents/*.php");

	for($i=0;$i<count($all_tables);$i++) {
	
		unset($db_path,$table_name,$database);
		
		include $all_tables[$i]; // returns $cols and $table_name
		
		/*
		if($database == "content") {
			$db_path = "../$dbfile";
		} elseif($database == "user") {
			$db_path = "../$fc_db_user";
		} elseif($database == "tracker") {
			$db_path = "../$fc_db_stats";
		} elseif($database == "index") {
			$db_path = "../$fc_db_index";
		} else {
			$_SESSION['protocol'] .= '<b class="text-danger">DATABASE UNKNOWN:</b> '.$database.'<|>';
			$_SESSION['errors_cnt']++;
			continue;
		}
		
		if(!is_file($db_path)) {
			$_SESSION['protocol'] .= '<b class="text-danger">DATABASE NOT FOUND:</b> '.$db_path.'<|>';
			$_SESSION['errors_cnt']++;
			continue;
		}
		*/
	
		$is_table = table_exists("$database","$table_name");
	
		if($is_table < 1) {
			if($table_type == 'virtual') {
				add_virtual_table("$database","$table_name",$cols);
			} else {
				add_table("$database","$table_name",$cols);
			}			

			$_SESSION['protocol'] .= '<b class="text-success">new table:</b> '.$table_name.' in '.$database.'<|>';
		}
	
	
		$existing_cols = get_collumns("$database","$table_name");
	
	
		foreach ($cols as $k => $v) {
	   
	  		if(!array_key_exists("$k", $existing_cols)) {
	  			//update_table -> column, type, table, database
	  			update_table("$k","$cols[$k]","$table_name","$db_path");
	  			$_SESSION['protocol'] .= '<b class="text-success">new column:</b> '.$k.' in table '.$table_name.'<|>';
	  		}
	     
		} // eo foreach
	
		/* updates are done, check all columns again */	
		$existing_cols = get_collumns("$database","$table_name");

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

/**
 * create directory (recursive)
 */
 
function mkdir_recursive($dir, $chmod=0777){
  $dirs = explode('/', $dir);
  $directory='';
  foreach ($dirs as $part) {
  	$directory .= $part.'/';
    if(!is_dir($directory) && strlen($directory)>0) {
    	mkdir($directory, $chmod);
    	chmod("$sub", $chmod);
    	$_SESSION['protocol'] .= "created: $directory <|>";
    }
  }
}

?>