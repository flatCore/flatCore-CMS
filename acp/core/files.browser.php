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
	$_SESSION['disk'] = fc_filter_filepath($_REQUEST['selected_folder']);
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
	if($db_type == 'mysql') {
		$_SESSION['sort_by'] = 'CAST(media_filesize AS SIGNED)';
	} else {
		$_SESSION['sort_by'] = 'CAST(media_filesize AS INTEGER)';
	}
}

if((isset($_GET['sort_by'])) && ($_GET['sort_by'] == 'time')) {
	$_SESSION['sort_by'] = 'media_lastedit';
}

if(!isset($_SESSION['sort_direction'])) {
	$_SESSION['sort_direction'] = 'DESC';
}

if(isset($_GET['sort_direction'])) {
	switch_sort();
}




/* labels */
if(!isset($_SESSION['checked_label_str'])) {
	$_SESSION['checked_label_str'] = '';
}

$a_checked_labels = explode('-', $_SESSION['checked_label_str']);

if(isset($_GET['switchLabel'])) {
	
		if(in_array($_GET['switchLabel'], $a_checked_labels)) {
			/* remove label*/
			if(($key = array_search($_GET['switchLabel'], $a_checked_labels)) !== false) {
				unset($a_checked_labels[$key]);
			}
		} else {
			/* add label */
			$a_checked_labels[] = $_GET['switchLabel'];
		}

		$_SESSION['checked_label_str'] = implode('-', $a_checked_labels);
}
$a_checked_labels = explode('-', $_SESSION['checked_label_str']);





unset($check_lastedit,$check_size,$check_name);

if($_SESSION['sort_by'] == 'media_lastedit') {
	$check_lastedit = 'active';
} else if($_SESSION['sort_by'] == 'media_file') {
	$check_name = 'active';	
} else {
	$check_size = 'active';
}



$select_dir = '';
$select_dir  .= '<form action="acp.php?tn=filebrowser&sub=browse" method="POST" class="d-inline dirtyignore">';

$select_dir  .= '<div class="row">';
if($disk != $path_img AND $disk != $path_files) {
	$level_up = dirname($disk);
	$select_dir  .= '<div class="col-1">';
	$select_dir .= '<a href="acp.php?tn=filebrowser&sub=browse&selected_folder='.$level_up.'" class="btn btn-fc">'.$icon['level_up_alt'].'</a> ';
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
$tpl_container_class = 'list-container';
if(strpos($disk,$path_img) !== FALSE) {
	$tpl_file = file_get_contents('templates/list-files-thumbs.tpl');
	$tpl_file_type = 'thumbs';
	$tpl_container_class = 'row';
}

/* create new directory */

if((isset($_POST['new_folder'])) && ($_POST['new_folder'] != '')) {
	$folder_name = clean_filename($_POST['new_folder']);
	$create_path = $disk . '/' . $folder_name;	
	mkdir($create_path, 0777, true);
}


/**
 * Delete Files
 * or Images and Thumbnails
 */
if(isset($_POST['delete'])) {
	
	$deleteFile = (int) $_POST['delete'];
	$get_file_data = fc_get_media_data_by_id($deleteFile);
	
	$delete_file = $get_file_data['media_file'];
	$delete_thumb = $get_file_data['media_thumb'];
		
	if(is_file($delete_file)) {
		if(unlink($delete_file)) {
			fc_delete_media_data($delete_file);
			if(is_file($delete_thumb)) {
				unlink($delete_thumb);
			}
			echo '<div class="alert alert-success alert-auto-close">'.$lang['msg_file_delete'].'</div>';
		} else {
			echo '<div class="alert alert-danger"><strong>'.$delete_file.'</strong><br>'.$lang['msg_file_delete_error'].'</div>';
		}	
	} else {
		echo '<div class="alert alert-error">File ('.$delete_file.') not found</div>';
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


if(isset($_POST['clear_tmb'])) {
	fc_clear_thumbs_directory();	
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

if(isset($_POST['rebuild']) && ($_POST['rebuild'] == 'database')) {
	
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

	
	/* add missing entries to database */
	foreach($a_files as $file) {

		unset($mediaData);
		$filename = $file;
		
		if(is_dir($filename)) { continue; }

		$mediaData = $db_content->get("fc_media", ["media_file"], [
			"media_file" => "$filename"
		]);
				
		if(!is_array($mediaData)) {
			
			$filesize = filesize($filename);
			$filemtime = filemtime($filename);
			$filetype = mime_content_type(realpath($filename));
			
			$db_content->insert("fc_media", [
				"media_file" => "$filename",
				"media_lang" => "$languagePack",
				"media_filesize" => "$filesize",
				"media_lastedit" => "$filemtime",
				"media_upload_time" => "$filemtime",
				"media_type" => "$filetype"
			]);
			
			$cnt_files_rebuild++;
		}	
	
		if((time()-$rebuild_start) > 5) {
			$incomplete = TRUE;
			break;
		}	
	}

	
	$storedFiles = $db_content->select("fc_media", '*', [
		"media_file[~]" => "$disk"
	]);
	
	foreach($storedFiles as $k) {
		if(!is_file($k['media_file'])) {
			fc_delete_media_data($k['media_file']);
			$cnt_files_removed++;
				if((time()-$rebuild_start) > 5) {
					$incomplete = TRUE;
					break;
				}
		}
	}
	
	
	/* check if thumbnail exists and create missing thumbnail file */
	$cnt_created_tmbs = 0;
	foreach($storedFiles as $k) {

		/* thumbnail directories */
		$tmb_dir = '../'.$img_tmb_path;
		$tmb_dir_year = $tmb_dir.'/'.date('Y',$k['media_upload_time']);
		$tmb_destination = $tmb_dir_year.'/'.date('m',$k['media_upload_time']);
				
		if(!is_dir($tmb_destination)) {
			mkdir($tmb_destination, 0777, true);
		}
		
		$tmb_name = md5($k['media_file']).'.jpg';
		
		$ckeck_tmb = $tmb_destination.'/'.$tmb_name;
		
		if(!file_exists($ckeck_tmb)) {
			$cnt_created_tmbs++;
			fc_create_thumbnail($k['media_file'], $tmb_name, $tmb_destination, $fc_preferences['prefs_maxtmbwidth'], $fc_preferences['prefs_maxtmbheight'], 80);
			$db_content->update("fc_media", [
				"media_thumb" => $ckeck_tmb
				],[
					"media_file" => $k['media_file']
			]);	
			
		}
		
		
	}


	/* create missing thumbnail files if 'media_thumb' is empty */
	$missing_thumb = $db_content->select('fc_media', '*', [
		"AND" => [
			'OR' => [
				'media_thumb' => null,'OR #Empty' => ['media_thumb' => '']
			],
			"media_type[~]" => "image"
		]	
	]);
	
	if(count($missing_thumb)>0) {
		foreach($missing_thumb as $row) {
			
			$image = $row['media_file'];
			$tmb_name = md5($image).'.jpg';
			$store_tmb_name = $tmb_destination.'/'.$tmb_name;
			
			fc_create_thumbnail($row['media_file'], $tmb_name, $tmb_destination, $fc_preferences['prefs_maxtmbwidth'], $fc_preferences['prefs_maxtmbheight'], 80);

			$db_content->update("fc_media", [
				"media_thumb" => $tmb_name
				],[
					"media_file" => $row['media_file']
			]);			
			
		}
	}
	
	
	$missing_rows = $db_content->select('fc_media', '*', [
		"AND" => [
			'OR' => ['media_filesize' => null,'OR #Empty' => ['media_filesize' => '']],
			'OR' => ['media_lastedit' => null,'OR #Empty' => ['media_lastedit' => '']],
			'OR' => ['media_upload_time' => null,'OR #Empty' => ['media_upload_time' => '']],
			'OR' => ['media_type' => null,'OR #Empty' => ['media_type' => '']]
		]	
	]);
	
	if(count($missing_rows)>0) {
		foreach($missing_rows as $row) {		
			if(is_file($row['media_file'])) {
							
				$filesize = filesize($row['media_file']);
				$filemtime = filemtime($row['media_file']);
				$filetype = mime_content_type(realpath($row['media_file']));

				$db_content->update("fc_media", [
				"media_filesize" => $filesize,
				"media_lastedit" => $filemtime,
				"media_upload_time" => $filemtime,
				"media_type" => $filetype
				],[
					"media_file" => $row['media_file']
					]);
				
				$cnt_infos_completed++;
			}
		
			if((time()-$rebuild_start) > 5) {
				$incomplete = TRUE;
				break;
			}
		
		}
	}
	
	echo '<div class="alert alert-info">';
	echo 'Created Add <strong>'.$cnt_files_rebuild. '</strong> Files, removed <strong>'.$cnt_files_removed.'</strong> Files from Database<br>';
	echo 'Completed <strong>'.$cnt_infos_completed. '</strong> File-Informations in Database<br>';
	echo 'Created <strong>'.$cnt_created_tmbs. '</strong> Thumbnails.';
	echo '</div>';
	
	if($incomplete === TRUE) {
		echo '<div class="alert alert-info">Maximum Time reached, <a href="?tn=filebrowser&sub=browse&rebuild=database">start again</a>.</div>';
	}
	
}



/* expand filter */
if(isset($_POST['media_filter']) && (trim($_POST['media_filter']) != '')) {
	$add_media_filter = clean_filename(filter_var($_POST['media_filter'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH));
	$_SESSION['media_filter'] = $_SESSION['media_filter'] . ' ' . $add_media_filter;
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
		$btn_remove_keyword .= '<a class="btn btn-fc btn-sm" href="acp.php?tn=filebrowser&sub=browse&rm_keyword='.$f.'">'.$icon['times_circle'].' '.$f.'</a> ';
	}
}



$kw_form  = '<form action="acp.php?tn=filebrowser&sub=browse&d=" method="POST" class="form-inline dirtyignore">';
$kw_form .= '<div class="input-group">';
$kw_form .= '<span class="input-group-text">'.$icon['search'].'</span>';
$kw_form .= '<input class="form-control" type="text" name="media_filter" value="" placeholder="'.$lang['button_search'].'">';
$kw_form .= '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
$kw_form .= '</div>';
$kw_form .= '</form>';


echo '<div class="subHeader">';
echo '<div class="row">';

echo '<div class="col-md-8">';
echo $select_dir;
echo '</div>';

echo '<div class="col-md-4">';
echo '<form action="acp.php?tn=filebrowser&sub=browse" method="POST" class="form dirtyignore">';
echo '<div class="input-group">';
echo '<input type="text" name="new_folder" class="form-control">';
echo '<div class="input-group-append">';
echo '<input type="submit" name="submit" value="'.$lang['create_new_folder'].'" class="btn btn-fc">';
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</div>';
echo '</div>';
echo '</form>';
echo '</div>';

echo '<div class="col-md-4">';

echo '<div class="btn-toolbar float-end">';


echo '</div>';

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


/* build SQL query for labels */

$set_label_filter = '';
$checked_labels_array = explode('-', $_SESSION['checked_label_str']);

for($i=0;$i<count($fc_labels);$i++) {
	$label = $fc_labels[$i]['label_id'];
	if(in_array($label, $checked_labels_array)) {
		$set_label_filter .= "media_labels LIKE '%,$label,%' OR media_labels LIKE '%,$label' OR media_labels LIKE '$label,%' OR media_labels = '$label' OR ";
	}
}

$set_label_filter = substr("$set_label_filter", 0, -3); // cut the last ' OR'

if($set_label_filter != "") {
	$_SESSION['media_filter_string'] .= " AND ($set_label_filter)";
}


$sql_cnt = "SELECT count(*) AS 'all' FROM fc_media WHERE media_file LIKE '%$disk%' ".$_SESSION['media_filter_string'];
$all_files = $db_content->query($sql_cnt)->fetch();
$nbr_of_files = $all_files['all'];


$files_per_page = 36;
$show_numbers = 9;
$start = 0;
$disable_next_start = '';
$disable_prev_start = '';


if(isset($_GET['start'])) {
	$_SESSION['file_browser_start'] = (int) $_GET['start'];
}

if(isset($_SESSION['file_browser_start'])) {
	$start = (int) $_SESSION['file_browser_start'];
}

if($start<0) {
	$start = 0;
}


$next_start = $start+$files_per_page;
$prev_start = $start-$files_per_page;

if($start>($nbr_of_files-$files_per_page)) {
	$next_start = $start;
	$disable_next_start = 'disabled';
}

if($start < 1) {
	$disable_prev_start = 'disabled';
}

$order_sql = 'ORDER BY '.$_SESSION['sort_by'].' '.$_SESSION['sort_direction']. ' ';
$where_sql = 'WHERE media_id IS NOT NULL AND ';
$where_sql .= " (media_file like '%$disk%')";
//$where_sql .= " AND (media_lang LIKE '$languagePack' OR media_lang IS NULL)";

$limit_sql = "LIMIT $start,$files_per_page ";


$sql = "SELECT * FROM fc_media $where_sql ".$_SESSION['media_filter_string']." $order_sql $limit_sql";
$get_files = $db_content->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$get_files = fc_unique_multi_array($get_files,'media_file');

$cnt_pages = ceil($nbr_of_files/$files_per_page);
$cnt_get_files = count($get_files);

$pag_backlink = '<li class="page-item"><a class="btn btn-fc '.$disable_prev_start.'" href="acp.php?tn=filebrowser&start='.$prev_start.'">'.$icon['angle_double_left'].'</a></li>';
$pag_forwardlink = '<li class="page-item"><a class="btn btn-fc '.$disable_next_start.'" href="acp.php?tn=filebrowser&start='.$next_start.'">'.$icon['angle_double_right'].'</a></li>';

unset($pag_string);
for($x=0;$x<$cnt_pages;$x++) {

	$aclass = "btn btn-fc";
	$page_start = $x*$files_per_page;
	$page_nbr = $x+1;
	
	if($page_start == $start) {
		$aclass = "btn btn-fc active";
		
		$pag_start = 	$x-($show_numbers/2);
		
		if($pag_start < 0) {
			$pag_start = 0;
		}
		
		$pag_end = 		$pag_start+$show_numbers;
		if($pag_end > $cnt_pages) {
			$pag_end = $cnt_pages;
		}
	}
	
	$a_pag_string[] = '<li class="page-item"><a class="'.$aclass.'" href="acp.php?tn=filebrowser&start='.$page_start.'">'.$page_nbr.'</a></li>';

}

if($disk != $path_img AND $disk != $path_files) {
	echo '<div class="container-fluid">';
	echo '<form class="inline pull-right" action="acp.php?tn=filebrowser&sub=browse&selected_folder='.dirname($disk).'" method="POST">';
	echo '<input type="submit" value="'.$lang['delete_folder'].'" class="btn btn-danger" onclick="return confirm(\''.$lang['confirm_delete_folder'].'\')">';
	echo '<input type="hidden" name="delete_folder" value="'.$disk.'">';
	echo '</form>';
	echo '</div>';
}


echo '<div class="row">';
echo '<div class="col-md-9">';

echo '<div class="card p-3">';

echo '<nav aria-label="Page navigation example">';
echo '<ul class="pagination justify-content-center">';
echo $pag_backlink .' ';
foreach(range($pag_start, $pag_end) as $number) {
    echo $a_pag_string[$number];
}
echo ' '. $pag_forwardlink;
echo '</ul>';
echo '</nav>';

echo '<div class="'.$tpl_container_class.'">';


//list all files
for($i=0;$i<$cnt_get_files;$i++) {
	
	$filename = '';
	$id = $get_files[$i]['media_id'];
	$filename = $get_files[$i]['media_file'];
	$filename_thumb = $get_files[$i]['media_thumb'];
	$filetime = $get_files[$i]['media_lastedit'];
	$filesize = readable_filesize($get_files[$i]['media_filesize']);
	$show_filetime = date('d.m.Y H:i',$filetime);

	if($tpl_file_type == 'grid') {
		$short_filename = str_replace('../content/files/', '', $filename);
	} else {
		$short_filename = str_replace('../content/images/', '', $filename);
	}
	
	
	$delete_btn = '<button type="submit" onclick="return confirm(\''.$lang['confirm_delete_file'].'\')" class="btn btn-fc btn-sm w-100 text-danger" name="delete" value="'.$id.'">'.$icon['trash_alt'].'</button>';
	//$edit_btn = '<a data-fancybox data-type="ajax" data-src="/acp/core/ajax.media.php?file='.$filename.'&folder='.$disk.'" href="javascript:;" class="btn btn-sm btn-fc w-100 text-success">'.$icon['edit'].'</a>';
	
	$edit_btn = '<button type="submit" class="btn btn-sm btn-fc w-100 text-success">'.$icon['edit'].'</button>';
	
	$tpl_list = $tpl_file;
	
	$fileinfo = pathinfo($filename);
	$suffix = $fileinfo['extension'];
	$ext = array("jpeg","jpg","png","svg","gif","webp");
	
	if(in_array($suffix,$ext) === true) {
		$set_style = '';
		
		if(file_exists($filename_thumb)) {
			$preview_img = "<img src='$filename_thumb' class='card-img-top'>";
		} else {
			$preview_img = "<img src='$filename' class='card-img-top'>";
		}
		
		
	} else {
		$preview_img = '<p class="text-right p-0 m-0">'.$icon['file'].' <span class="badge badge-secondary">'.$suffix.'</span></p>';
	}
	
	$tpl_list = str_replace('{preview_link}', $filename, $tpl_list);

	
	if(is_dir($filename)) {
		$set_style = '';
		$preview_img = '<a href="acp.php?tn=filebrowser&sub=browse&selected_folder='.$filename.'"><img src="images/folder.png" class="card-img"></a>';
		$tpl_list = str_replace('{preview_link}', 'acp.php?tn=filebrowser&sub=browse&selected_folder={filename}', $tpl_list);
		$edit_btn = '';
		$delete_btn = '';
		$filesize = '';
	}
	
	
	$labels = '';
	if($get_files[$i]['media_labels'] != '') {
		$get_media_labels = explode(',',$get_files[$i]['media_labels']);
		foreach($get_media_labels as $media_label) {
			
			foreach($fc_labels as $l) {
				if($media_label == $l['label_id']) {
					$label_color = $l['label_color'];
					$label_title = $l['label_title'];
				}
			}
			
			$labels .= '<span class="label-dot" style="background-color:'.$label_color.';" title="'.$label_title.'"></span>';
		}
	}
	

	$tpl_list = str_replace('{short_filename}', $short_filename, $tpl_list);
	$tpl_list = str_replace('{labels}', $labels, $tpl_list);
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



echo '</div>'; // columns


echo '<nav aria-label="Page navigation example">';
echo '<ul class="pagination justify-content-center">';
echo $pag_backlink .' ';
foreach(range($pag_start, $pag_end) as $number) {
    echo $a_pag_string[$number];
}
echo ' '. $pag_forwardlink;
echo '</ul>';
echo '</nav>';


echo '</div>'; // card

echo '</div>';
echo '<div class="col-md-3">';

/* sidebar */

echo '<div class="card p-3">';

echo "$kw_form";

echo '<div class="pt-1">'.$btn_remove_keyword.'</div>';

echo '<hr>';

echo '<fieldset>';
echo '<legend>'.$lang['h_page_sort'].'</legend>';

echo '<div class="btn-group d-flex">';
echo '<a class="btn btn-sm btn-fc w-100 '.$check_lastedit.'" href="acp.php?tn='.$tn.'&sub=browse&d='.$disk.'&sort_by=time">'.$lang['date_of_change'].'</a>';
echo '<a class="btn btn-sm btn-fc w-100 '.$check_name.'" href="acp.php?tn='.$tn.'&sub=browse&d='.$disk.'&sort_by=name">'.$lang['filename'].'</a>';
echo '<a class="btn btn-sm btn-fc w-100 '.$check_size.'" href="acp.php?tn='.$tn.'&sub=browse&d='.$disk.'&sort_by=size">'.$lang['filesize'].'</a>';
echo '<a class="btn btn-sm btn-fc" href="acp.php?tn='.$tn.'&sub=browse&d='.$disk.'&sort_direction=1">'. show_sort_arrow() .'</a>';
echo '</div>';

echo '</fieldset>';


$label_filter_box  = '<div class="card mt-2">';
$label_filter_box .= '<div class="card-header p-1 px-2">'.$lang['labels'].'</div>';
$label_filter_box .= '<div class="card-body">';
$this_btn_status = '';
foreach($fc_labels as $label) {
	
	if(in_array($label['label_id'], $a_checked_labels)) {
		$this_btn_status = 'active';
	} else {
		$this_btn_status = '';
	}		

	$label_title = '<span class="label-dot" style="background-color:'.$label['label_color'].';"></span> '.$label['label_title'];
	$label_filter_box .= '<a href="acp.php?tn=filebrowser&switchLabel='.$label['label_id'].'" class="btn btn-fc btn-sm m-1 '.$this_btn_status.'">'.$label_title.'</a>';
	
}
$label_filter_box .= '</div>';
$label_filter_box .= '</div>'; // card

echo $label_filter_box;



echo '<form action="acp.php?tn=filebrowser&sub=browse" method="POST" class="mt-4">';
echo '<div class="btn-group d-flex" role="group">';
echo '<button class="btn btn-sm btn-fc w-100" type="submit" name="rebuild" value="database">Database '.$icon['wrench'].'</button>';
echo '<button class="btn btn-sm btn-fc w-100" type="submit" name="clear_tmb">Thumbnails '.$icon['trash_alt'].'</button>';
echo '</div>';
echo '</form>';

echo '</div>'; // card


echo '</div>';
echo '</div>';



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