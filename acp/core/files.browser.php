<?php

//error_reporting(E_ALL ^E_NOTICE);
//prohibit unauthorized access
require 'core/access.php';


$path_img = '../'.IMAGES_FOLDER;
$img_dirs = fc_get_dirs_rec($path_img);
array_unshift($img_dirs, $path_img);
$path_files = '../'.FILES_FOLDER;
$files_dirs = fc_get_dirs_rec($path_files);
array_unshift($files_dirs, $path_files);

$img_folder = basename($path_img);
$files_folder = basename($path_files);


if(isset($_REQUEST['selected_folder'])) {
	$_SESSION['disk'] = $_REQUEST['selected_folder'];
}

if(isset($_SESSION['disk'])) {
	$disk = $_SESSION['disk'];
} else {
	$disk = $path_img;
}


if(!isset($_SESSION['sort_by'])) {
	$_SESSION['sort_by'] = 'media_lastedit';
}

if((isset($_GET['sort_by'])) && ($_GET['sort_by'] == 'name')) {
	$_SESSION['sort_by'] = 'media_file';
}

if((isset($_GET['sort_by'])) && ($_GET['sort_by'] == 'size')) {
	$_SESSION['sort_by'] = 'CAST(media_filesize AS INTEGER)';
}

if((isset($_GET['sort_by'])) && ($_GET['sort_by'] == 'time')) {
	$_SESSION['sort_by'] = 'media_lastedit';
}

if(isset($_GET['sort_direction'])) {
	switch_sort();
}

unset($check_lastedit,$check_size,$check_name);

if($_SESSION['sort_by'] == 'media_lastedit') {
	$check_lastedit = 'active';
} else if($_SESSION['sort_by'] == 'media_file') {
	$check_name = 'active';	
} else {
	$check_size = 'active';
}



$select_dir = '';
$select_dir  .= '<form action="acp.php?tn=filebrowser&sub=browse" method="POST" class="form-inlinne dirtyignore">';

$select_dir  .= '<div class="row">';
if($disk != $path_img AND $disk != $path_files) {
	$level_up = dirname($disk);
	$select_dir  .= '<div class="col-1">';
	$select_dir .= '<a href="acp.php?tn=filebrowser&sub=browse&selected_folder='.$level_up.'" class="btn btn-dark">'.$icon['level_up_alt'].'</a> ';
	$select_dir  .= '</div>';
}
$select_dir  .= '<div class="col">';
$select_dir .= '<select name="selected_folder" onchange="this.form.submit()" class="form-control custom-select">';
$select_dir .= '<optgroup label="'.$lang['images'].'">';
foreach($img_dirs as $d) {
	$selected = '';
	if($disk == $d) {
		$selected = 'selected';
	}
	$short_d = str_replace($path_img, '', $d);
	$select_dir .= '<option value="'.$d.'" '.$selected.'>'.$img_folder.$short_d.'</option>';
}
$select_dir .= '</optgroup>';
$select_dir .= '<optgroup label="'.$lang['files'].'">';
foreach($files_dirs as $d) {
	$selected = '';
	if($disk == $d) {
		$selected = 'selected';
	}
	$short_d = str_replace($path_files, '', $d);
	$select_dir .= '<option value="'.$d.'" '.$selected.'>'.$files_folder.$short_d.'</option>';
}
$select_dir .= '</optgroup>';
$select_dir .= '</select>';
$select_dir  .= '</div>';
$select_dir  .= '</div>';
$select_dir .= '</form>';



/* template file */

$tpl_file = file_get_contents('templates/list-files-grid.tpl');
$tpl_file_type = 'grid';
$tpl_container_id = 'list-container';
if(strpos($disk,$path_img) !== FALSE) {
	$tpl_file = file_get_contents('templates/list-files-thumbs.tpl');
	$tpl_file_type = 'thumbs';
	$tpl_container_id = 'masonry-container';
}

/* create new directory */

if((isset($_POST['new_folder'])) && ($_POST['new_folder'] != '')) {
	$folder_name = clean_filename($_POST['new_folder']);
	$create_path = $disk . '/' . $folder_name;	
	mkdir($create_path, 0777, true);
}


/* DELETE FILE OR IMAGE */
if(isset($_POST['delete'])) {
	
	$deleteFile = basename($_POST['file']);
	
	if(is_file("$disk/$deleteFile")) {
		if(unlink("$disk/$deleteFile")) {
			fc_delete_media_data("$disk/$deleteFile");
			echo '<div class="alert alert-success alert-auto-close">'.$lang['msg_file_delete'].'</div>';
		} else {
			echo '<div class="alert alert-danger"><strong>'.$disk.'/'.$deleteFile.'</strong><br>'.$lang['msg_file_delete_error'].'</div>';
		}	
	} else {
		echo '<div class="alert alert-error">File ('.$disk.'/'.$deleteFile.') not found</div>';
	}
}

/* delete folder */

if(isset($_POST['delete_folder']) && $_POST['delete_folder'] != '') {
	delete_folder($_POST['delete_folder']);
}

function delete_folder($dir) {
	
	global $disk;
	$delete_folder = $disk.'/'.basename($dir);
   $files = array_diff(scandir($delete_folder), array('.','..')); 
    foreach ($files as $file) {
	    if(is_dir("$dir/$file")) {
		    delete_folder("$dir/$file");
	    } else {
		    unlink("$dir/$file");
		    fc_delete_media_data("$dir/$file");
	    }
	    
    } 
    return rmdir($delete_folder); 
  } 


/**
 * check if all files stored in media database
 * if not, catch up
 *
 * check if entries stored in media database are still there
 * if not, drop entry
 *
 * check if media_filsize or media_lastedit is empty
 * if yes, fill up
 */

if(isset($_GET['rebuild']) && ($_GET['rebuild'] == 'database')) {
	
	$incomplete = FALSE;
	$rebuild_start = time();
	$cnt_files_rebuild = 0;
	$cnt_files_removed = 0;
	$cnt_infos_completed = 0;


	unset($scan_files);
	$scan_files = scandir($disk);
	
	foreach ($scan_files as $key => $value) { 
	   if(in_array($value,array('..', '.','.DS_Store','index.html'))) {
	      continue;
	    }
	   $a_files[] = $disk.'/'.$value;
	}
	
	
	$cnt_all_files = count($a_files);


	$dbh = new PDO("sqlite:".CONTENT_DB);
	
	/* add missing entries to database */
	foreach($a_files as $file) {

		unset($mediaData);
		$filename = $file;
		
		if(is_dir($filename)) { continue; }
		
		$sql = "SELECT media_file FROM fc_media WHERE media_file = :filename ";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':filename', $filename, PDO::PARAM_STR);
		$sth->execute();
	
		$mediaData = $sth->fetch(PDO::FETCH_ASSOC);
	
		if(!is_array($mediaData)) {
			$filesize = filesize($filename);
			$filemtime = filemtime($filename);
			$sql = "INSERT INTO fc_media ( media_id, media_file, media_lang, media_filesize, media_lastedit ) VALUES ( NULL, :media_file, :media_lang, :media_filesize, :media_lastedit) ";
			$sth = $dbh->prepare($sql);
			$sth->bindParam(':media_file', $filename, PDO::PARAM_STR);
			$sth->bindParam(':media_lang', $languagePack, PDO::PARAM_STR);
			$sth->bindParam(':media_filesize', $filesize, PDO::PARAM_STR);
			$sth->bindParam(':media_lastedit', $filemtime, PDO::PARAM_STR);
			$sth->execute();
			$cnt_files_rebuild++;
		}
	
		if((time()-$rebuild_start) > 5) {
			$incomplete = TRUE;
			break;
		}
	
	}
	
	/* remove files from database if the file no longer exists */
	$sql = "SELECT media_file FROM fc_media WHERE media_file like '%$disk%'";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	$storedFiles = $sth->fetchAll(PDO::FETCH_COLUMN);

	
	foreach($storedFiles as $f) {
		if(!is_file($f)) {
			fc_delete_media_data($f);
			$cnt_files_removed++;
				if((time()-$rebuild_start) > 5) {
					$incomplete = TRUE;
					break;
				}
		}
	}
	
	
	/* complete file's informations - filesize and/or filemtime  */
	$sql = "SELECT * FROM fc_media WHERE media_filesize IS NULL OR media_filesize = '' OR media_lastedit IS NULL OR media_lastedit = '' ";
	$sth = $dbh->query($sql);
	$missing_rows = $sth->fetchAll(PDO::FETCH_ASSOC);
	
	if(count($missing_rows)>0) {
		foreach($missing_rows as $row) {		
			if(is_file($row['media_file'])) {
							
				$filesize = filesize($row['media_file']);
				$filemtime = filemtime($row['media_file']);
						
				$sql = "UPDATE fc_media SET media_filesize = :media_filesize, media_lastedit = :media_lastedit WHERE media_file = :media_file ";
				$sth = $dbh->prepare($sql);
				$sth->bindParam(':media_file', $row['media_file'], PDO::PARAM_STR);
				$sth->bindParam(':media_filesize', $filesize, PDO::PARAM_STR);
				$sth->bindParam(':media_lastedit', $filemtime, PDO::PARAM_STR);
				$sth->execute();
				$cnt_infos_completed++;
			}
		
			if((time()-$rebuild_start) > 5) {
				$incomplete = TRUE;
				break;
			}
		
		}
	}
		
	$dbh = null;
	
	echo '<div class="alert alert-info">Add '.$cnt_files_rebuild.' Files, removed '.$cnt_files_removed.' Files from Database. Completed '.$cnt_infos_completed.' File-Informations</div>';
	
	if($incomplete === TRUE) {
		echo '<div class="alert alert-info">Maximum Time reached, <a href="?tn=filebrowser&sub=browse&rebuild=database">start again</a>.</div>';
	}
	
}



/* expand filter */
if(isset($_POST['media_filter']) && (trim($_POST['media_filter']) != '')) {
	$_SESSION['media_filter'] = $_SESSION['media_filter'] . ' ' . $_POST['media_filter'];
}

/* remove keyword from filter list */
if($_REQUEST['rm_keyword'] != "") {
	$all_filter = explode(" ", $_SESSION['media_filter']);
	unset($_SESSION['media_filter'],$f);
	foreach($all_filter as $f) {
		if($_REQUEST['rm_keyword'] == "$f") { continue; }
		if($f == "") { continue; }
		$_SESSION['media_filter'] .= "$f ";
	}
	unset($all_filter);
}


if($_SESSION['media_filter'] != "") {
	unset($all_filter);
	$all_filter = explode(" ", $_SESSION['media_filter']);
	foreach($all_filter as $f) {
		if($_REQUEST['rm_keyword'] == "$f") { continue; }
		if($f == "") { continue; }
		$btn_remove_keyword .= '<a class="btn btn-dark btn-sm" href="acp.php?tn=filebrowser&sub=browse&rm_keyword='.$f.'"><span class="glyphicon glyphicon-remove"></span> '.$f.'</a> ';
	}
}



$kw_form  = '<form action="acp.php?tn=filebrowser&sub=browse&d=" method="POST" class="form-inline dirtyignore">';
$kw_form .= '<div class="input-group">';
$kw_form .= '<span class="input-group-addon"><span class="glyphicon glyphicon-filter"></span></span>';
$kw_form .= '<input class="form-control" type="text" name="media_filter" value="" placeholder="Filter">';
$kw_form .= '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
$kw_form .= '</div>';
$kw_form .= '</form>';


echo '<div class="subHeader">';
echo '<div class="row">';

echo '<div class="col-md-4">';
echo $select_dir;
echo '</div>';

echo '<div class="col-md-4">';
echo '<form action="acp.php?tn=filebrowser&sub=browse" method="POST" class="form dirtyignore">';
echo '<div class="input-group">';
echo '<input type="text" name="new_folder" class="form-control">';
echo '<div class="input-group-append">';
echo '<input type="submit" name="submit" value="'.$lang['create_new_folder'].'" class="btn btn-dark">';
echo '</div>';
echo '</div>';
echo '</div>';

echo '<div class="col-md-4">';

echo '<div class="btn-toolbar float-right">';

echo '<div class="btn-group float-right mr-1">';
echo '<a class="btn btn-sm btn-dark" href="acp.php?tn='.$tn.'&sub=browse&rebuild=database">'.$icon['wrench'].'</a>';
echo '</div>';
echo '<div class="btn-group float-right">';
echo '<a class="btn btn-sm btn-dark '.$check_lastedit.'" href="acp.php?tn='.$tn.'&sub=browse&d='.$disk.'&sort_by=time">'.$lang['date_of_change'].'</a>';
echo '<a class="btn btn-sm btn-dark '.$check_name.'" href="acp.php?tn='.$tn.'&sub=browse&d='.$disk.'&sort_by=name">'.$lang['filename'].'</a>';
echo '<a class="btn btn-sm btn-dark '.$check_size.'" href="acp.php?tn='.$tn.'&sub=browse&d='.$disk.'&sort_by=size">'.$lang['filesize'].'</a>';
echo '<a class="btn btn-sm btn-dark" href="acp.php?tn='.$tn.'&sub=browse&d='.$disk.'&sort_direction=1">'. show_sort_arrow() .'</a>';
echo '</div>';
echo '</div>';

echo '</div>';
echo '</div>';

echo '<div class="row" style="margin-top:10px;">';
echo '<div class="col-md-4">';
echo "$kw_form";
echo '</div>';
echo '<div class="col-md-8">';
echo "$btn_remove_keyword";
echo '</div>';
echo '</div>';
echo '</div>';

unset($_SESSION['media_filter_string']);
if(is_array($all_filter)) {
	$add_keyword_filter = ' AND ';
	foreach($all_filter as $f) {
		if($f == "") { continue; }
		$add_keyword_filter .= "(	media_file like '%$f%' OR
															media_title like '%$f%' OR
															media_notes like '%$f%' OR
															media_keywords like '%$f%' OR
															media_credit like '%$f%' OR
															media_text like '%$f%'
															) AND ";
	}

	$add_keyword_filter = substr($add_keyword_filter, 0,-5);

	$_SESSION['media_filter_string'] = $add_keyword_filter;
}

$dbh = new PDO("sqlite:".CONTENT_DB);

$sql_cnt = "SELECT count(*) AS 'all' FROM fc_media WHERE media_file LIKE '%$disk%' AND (media_lang LIKE '$languagePack' OR media_lang IS NULL) ".$_SESSION['media_filter_string'];
$sth = $dbh->prepare($sql_cnt);
$sth->execute();
$all_files = $sth->fetch();
$nbr_of_files = $all_files['all'];

$files_per_page = 20;
$show_numbers = 6;
$start = 0;
$end = $files_per_page;


if(isset($_GET['start'])) {
	$start = (int) $_GET['start'];
}

if($start<0) {
	$start = 0;
}


$end = $start+$files_per_page;
$next_start = $start+$end;
$prev_start = $start-$end;

if($start>($nbr_of_files-$end)) {
	$next_start = $start;
}

if($end>$nbr_of_files) {
	$end = $nbr_of_files;
}

$order_sql = 'ORDER BY '.$_SESSION['sort_by'].' '.$_SESSION['sort_direction']. ' ';
$where_sql = 'WHERE media_id IS NOT NULL AND ';
$where_sql .= " (media_file like '%$disk%')";
$where_sql .= " AND (media_lang LIKE '$languagePack' OR media_lang IS NULL)";

$limit_sql = "LIMIT '$start','$files_per_page' ";


$sql = "SELECT * FROM fc_media $where_sql ".$_SESSION['media_filter_string']." $order_sql $limit_sql";
$sth = $dbh->prepare($sql);
$sth->execute();
$get_files = $sth->fetchAll(PDO::FETCH_ASSOC);

$dbh = null;

$cnt_pages = ceil($nbr_of_files/$files_per_page);

$cnt_get_files = count($get_files);


$pag_backlink = '<a class="btn btn-dark" href="acp.php?tn=filebrowser&start='.$prev_start.'">'.$icon['angle_double_left'].'</a>';
$pag_forwardlink = '<a class="btn btn-dark" href="acp.php?tn=filebrowser&start='.$next_start.'">'.$icon['angle_double_right'].'</a>';

unset($pag_string);
for($x=0;$x<$cnt_pages;$x++) {

	$aclass = "btn btn-dark";
	$page_start = $x*$files_per_page;
	$page_nbr = $x+1;
	
	if($page_start == $start) {
		$aclass = "btn btn-dark active";
		
		$pag_start = 	$x-($show_numbers/2);
		
		if($pag_start < 0) {
			$pag_start = 0;
		}
		
		$pag_end = 		$pag_start+$show_numbers;
		if($pag_end > $cnt_pages) {
			$pag_end = $cnt_pages;
		}
	}
	
	$a_pag_string[] = "<a class='$aclass' href='acp.php?tn=filebrowser&start=$page_start'>$page_nbr</a> ";

}

if($disk != $path_img AND $disk != $path_files) {
	echo '<div class="container-fluid">';
	echo '<form class="inline pull-right" action="acp.php?tn=filebrowser&sub=browse&selected_folder='.dirname($disk).'" method="POST">';
	echo '<input type="submit" value="'.$lang['delete_folder'].'" class="btn btn-danger" onclick="return confirm(\''.$lang['confirm_delete_folder'].'\')">';
	echo '<input type="hidden" name="delete_folder" value="'.$disk.'">';
	echo '</form>';
	echo '</div>';
}

echo '<div id="container">';
echo '<div id="'.$tpl_container_id.'">';


//list all files
for($i=0;$i<$cnt_get_files;$i++) {
	
	$filename = '';

	$filename = $get_files[$i]['media_file'];
	$filetime = $get_files[$i]['media_lastedit'];
	$filesize = readable_filesize($get_files[$i]['media_filesize']);
	$show_filetime = date('d.m.Y H:i',$filetime);

	if($tpl_file_type == 'grid') {
		$short_filename = str_replace('../content/files/', '', $filename);
	} else {
		$short_filename = str_replace('../content/images/', '', $filename);
	}
	
	
	$delete_btn = '<button type="submit" onclick="return confirm(\''.$lang['confirm_delete_file'].'\')" class="btn btn-dark btn-sm w-100 text-danger" name="delete" value="'.$lang['delete'].'">'.$icon['trash_alt'].'</button>';
	$edit_btn = '<a data-fancybox data-type="ajax" data-src="/acp/core/ajax.media.php?file='.$filename.'&folder='.$disk.'" href="javascript:;" class="btn btn-sm btn-dark w-100 text-success">'.$icon['edit'].'</a>';
	
	
	
	$tpl_list = $tpl_file;


	$imgsize = getimagesize($filename);
	
	if($imgsize[0] > 0) {
		$set_style = '';
		$preview_img = "<img src='$filename' class='img-fluid'>";
		$tpl_list = str_replace('{preview_link}', $filename, $tpl_list);
	} else {
		$set_style = "background-image: url(images/no-preview.gif); background-position: center; background-repeat: no-repeat;";
		$preview_img = '';
		$tpl_list = str_replace('{preview_link}', $filename, $tpl_list);
	}
	
	if(is_dir($filename)) {
		$set_style = '';
		$preview_img = '<a href="acp.php?tn=filebrowser&sub=browse&selected_folder='.$filename.'"><img src="images/folder.png" class="img-fluid"></a>';
		$tpl_list = str_replace('{preview_link}', 'acp.php?tn=filebrowser&sub=browse&selected_folder={filename}', $tpl_list);
		$edit_btn = '';
		$delete_btn = '';
		$filesize = '';
	}	

	$tpl_list = str_replace('{short_filename}', $short_filename, $tpl_list);
	$tpl_list = str_replace('{filename}', $filename, $tpl_list);
	$tpl_list = str_replace('{set_style}', $set_style, $tpl_list);
	$tpl_list = str_replace('{preview_img}', $preview_img, $tpl_list);
	$tpl_list = str_replace('{show_filetime}', $show_filetime, $tpl_list);
	$tpl_list = str_replace('{filesize}', $filesize, $tpl_list);
	$tpl_list = str_replace('{edit_button}', $edit_btn, $tpl_list);
	$tpl_list = str_replace('{delete_button}', $delete_btn, $tpl_list);
	$tpl_list = str_replace('{csrf_token}', $_SESSION['token'], $tpl_list);
	
	echo $tpl_list;

} // eol $i - list all files 



echo '</div>'; // masonry-container
echo '</div>';
echo '<div class="clearfix"></div>';


echo '<div id="well well-sm"><p class="text-center">';
echo $pag_backlink .' ';
foreach(range($pag_start, $pag_end) as $number) {
    echo $a_pag_string[$number];
}
echo ' '. $pag_forwardlink;
echo '</p></div>'; //EOL PAGINATION




function switch_sort() {
	if($_SESSION['sort_direction'] == 'ASC') {
		$_SESSION['sort_direction'] = 'DESC';
	} else {
		$_SESSION['sort_direction'] = 'ASC';
	}
}

function show_sort_arrow() {
	global $icon;
	if($_SESSION['sort_direction'] == 'ASC') {
		$ic = '<i class="far fa-angle-up"></i>';
	} else {
		$ic = '<i class="far fa-angle-down"></i>';
	}
	return $ic;
}


?>