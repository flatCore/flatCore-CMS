<?php
ob_start();
set_time_limit (0);

//prohibit unauthorized access
require("core/access.php");
require_once('core/pclzip.lib.php');
include('updatelist.php');

define('INSTALLER', TRUE);
include('../install/php/functions.php');

/* build an array from all php files in folder ../install/contents */
$all_tables = glob("../install/contents/*.php");

$remote_versions_file = file_get_contents("http://updates.flatCore.de/versions.txt");

if(isset($_GET['beta']) && $_GET['beta'] > 0) {
	$remote_versions_file = file_get_contents("http://updates.flatCore.de/versions-beta.txt");
}

// example string: 2013-06-29<|>Release Candidate 3<|>39<|>fc_b39.zip
$remote_versions = explode("<|>",$remote_versions_file);

if(isset($_GET['github']) && $_GET['github'] > 0) {
	$remote_versions[0] = '';
	$remote_versions[1] = 'GitHub';
	$remote_versions[2] = 'master';
	$remote_versions[3] = 'flatCore-CMS-master.zip';
}

echo '<fieldset>';
echo '<legend>'.$lang['system_update'].'</legend>';

compare_versions();

if(isset($_GET['a']) && $_GET['a'] == 'start') {
	start_update();
}

echo '</fieldset>';

ob_flush();
flush();
	
/**
 * start the update
 * 1. load the zip file from "http://updates.flatCore.de/zip/...
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
	} else {
		$source_file = 'http://updates.flatcore.de/zip/'.$get_file;
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

	echo "<h3>" . count($new_files) . " updated Files:</h3>";
	echo '<div style="height:350px;overflow:auto;margin:0;" class="well well-sm">';
			
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
		
		if(substr(basename($value), 0,1) == ".") { continue;}
		if($value === '.' || $value === '..') {continue;}
		if(basename($value) == "README.md") { continue;}
		if(basename($value) == "robots.txt") { continue;}
			
		/**
		 * copy files from 'update/extract/*'
		 */
		$target = "../" . substr($value, strlen("update/extract/$get_file/"));
		$copy_string .= "<tr><td>$i</td><td>$target</td>";
		$status = copy_recursive("$value","$target");
		
		if($status == 'success') {
			$show_status = "<span class='label label-success'>ok</span>";
		} else {
			$show_status = '<span class="label label-danger">'.$status.'</span>';
			$cnt_errors++;
		}
		
		echo '<dl class="dl-horizontal">';
		echo '<dt>'.$i.'</dt>';
		echo '<dd>update/..'. basename($value) .' > '. $target .' '. $show_status .'</dd>';
		echo '</dl>';
		
		ob_flush();
		flush();
	}
	
	echo '<p>Errors: '.$cnt_errors.'</p>';
	
	echo '</div>';

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
	
	if(is_file("versions.php")){
		include("versions.php");
	} else {
		$fc_version_name = '';
		$fc_version_build = '';
	}
	
	echo '<table class="table table-condensed table-bordered">';
	echo '<thead>';
	echo '<tr>
					<th><span class="glyphicon glyphicon-hdd"></span>  '. $_SERVER['SERVER_NAME'] .'</th>
					<th><span class="glyphicon glyphicon-globe"></span> updates.flatCore.de</th>
				</tr>';
	echo '</thead>';
	
	echo '<tr>';
	echo "<td>$fc_version_name (Build $fc_version_build)</td>";
	echo "<td>$remote_versions[1] (Build $remote_versions[2])</td>";
	echo '</tr>';
	echo '</table>';
	
	/* compare build numbers */
		
	$start_dl = '';
	
	if(($remote_versions[2] > $fc_version_build) && ($remote_versions[2] != 'master')) {
		$start_dl  = 'acp.php?tn=system&sub=update&a=start';
		if(isset($_GET['beta']) && $_GET['beta'] > 0) {
			$start_dl .= '&beta=1';
		}
	} elseif ($remote_versions[2] == 'master') {
		$start_dl  = 'acp.php?tn=system&sub=update&a=start&github=1';
	}
	
	if($start_dl != '') {
		echo '<div class="alert alert-info"><p>' . $lang['msg_update_available'] . '</p><hr>';
		echo '<p>';
		echo '<a href="'.$start_dl.'" class="btn btn-success"><span class="glyphicon glyphicon-cloud-download"></span> Update</a>';
		echo '</p>';
		echo '</div>';		
	} else {
		echo '<div class="alert alert-success"><p>' . $lang['msg_no_update_available'] . '</p></div>';
	}

	ob_flush();
	flush();
}


/**
 * returns all files and directories
 * return array()
 */

function scandir_recursive($dir) { 
	$root = scandir($dir); 
  foreach($root as $value) { 
  	if($value === '.' || $value === '..') {continue;} 
    if(is_file("$dir/$value")) {$result[]="$dir/$value";continue;} 
    foreach(scandir_recursive("$dir/$value") as $value) { 
    	$result[]=$value; 
    } 
   } 
   return $result;  
}


/**
 * copy/move directory with its including contents
 */
 
function copy_recursive($source, $target) {
	if(is_dir($source)) {
	
		if(!is_dir("$target")) {
			mkdir("$target", 0755, true);
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
 * Update the database
 */

function update_database($dbfile) {
	
	global $fc_db_user;
	global $fc_db_stats;
	global $all_tables;

	/* build an array from all php files in folder ../install/contents */
	$all_tables = glob("../install/contents/*.php");

	for($i=0;$i<count($all_tables);$i++) {
	
		unset($db_path,$table_name,$database);
		
		include("$all_tables[$i]"); // returns $cols and $table_name
		
		if($database == "content") {
			$db_path = "../$dbfile";
		} elseif($database == "user") {
			$db_path = "../$fc_db_user";
		} elseif($database == "tracker") {
			$db_path = "../$fc_db_stats";
		} else {
			echo '<div class="alert alert-danger">DATABASE UNKNOWN: '.$database.'</div>';
			continue;
		}
		
		if(!is_file($db_path)) {
			echo "DATABASE NOT FOUND | $db_path | $database | $all_tables[$i] <br>";
			continue;
		}
	
	
		$is_table = table_exists("$db_path","$table_name");
	
		if($is_table < 1) {
			add_table("$db_path","$table_name",$cols);
			$table_updates[] = "New Table: <b>$table_name</b> in Database <b>$database</b>";
		}
	
	
		$existing_cols = get_collumns("$db_path","$table_name");
	
	
		foreach ($cols as $k => $v) {
	   
	  		if(!array_key_exists("$k", $existing_cols)) {
	  			//update_table -> column, type, table, database
	  			update_table("$k","$cols[$k]","$table_name","$db_path");
	  			$col_updates[] = "New Column: <b>$k</b> in table <b>$table_name</b>";	
	  		}
	     
		} // eo foreach
	
		/* updates are done, check all columns again */
	
		$existing_cols = get_collumns("$db_path","$table_name");
	
		foreach ($cols as $b => $x) {
	       
	  		if(!array_key_exists("$b", $existing_cols)) {
	  			$fails[] = "Missing Column: <b>$b</b> - table: <b>$table_name</b>";  	
	  		} else {
	  			$wins[] = "Column <b>$b</b> in table <b>$table_name</b> is ready";
	  	}
	  
		} // eo foreach
	
	
	} // EO $i
	
	
	if(is_array($fails)) {
		echo "<h3>" . count($fails) . " ERRORS</h3>";
		
		foreach ($fails as $value) {
				echo"<span class='red'>$value</span><br />";
			}
		
	} else {
		echo "<h3>" . count($wins) . " Columns are ready</h3>";
		
		if(is_array($table_updates)) {
			foreach ($table_updates as $value) {
				echo"<span class='green'>$value</span><br />";
			}
		}
		
		if(is_array($col_updates)) {
			foreach ($col_updates as $value) {
				echo"<span class='green'>$value</span><br />";
			}
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