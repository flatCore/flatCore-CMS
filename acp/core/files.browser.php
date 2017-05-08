<?php

error_reporting(0);

//prohibit unauthorized access
require("core/access.php");


if(isset($_GET['d'])) {
	$_SESSION['disk'] = (int) $_GET['d'];
}

if(isset($_SESSION['disk'])) {
	$disk = $_SESSION['disk'];
} else {
	$disk = 1;
}

if($_SESSION['sort_by_name'] == '' AND $_SESSION['sort_by_size'] == '' AND $_SESSION['sort_by_time'] == '') {
	$_SESSION['sort_by_time'] = 'DESC';
}


if(isset($_GET['sort_by_name'])) {
	switch_sort('sort_by_name');
	unset($_SESSION['sort_by_size'],$_SESSION['sort_by_time']);
}

if(isset($_GET['sort_by_size'])) {
	switch_sort('sort_by_size');
	unset($_SESSION['sort_by_name'],$_SESSION['sort_by_time']);
}

if(isset($_GET['sort_by_time'])) {
	switch_sort('sort_by_time');
	unset($_SESSION['sort_by_size'],$_SESSION['sort_by_name']);
}


$sort_direction = constant(trim('SORT_'.$_SESSION['sort_by_name'].$_SESSION['sort_by_time'].$_SESSION['sort_by_size']));


if($disk == "2") {
	$path = '../content/files';
	$disk2_class = 'btn btn-fc btn-sm active';
	$disk1_class = 'btn btn-fc btn-sm';
	$tpl_file = file_get_contents('templates/list-files-grid.tpl');
} else {
	$path = '../content/images';
	$disk1_class = 'btn btn-fc btn-sm active';
	$disk2_class = 'btn btn-fc btn-sm';
	$tpl_file = file_get_contents('templates/list-files-thumbs.tpl');
}

/* DELETE FILE OR IMAGE */
if(isset($_POST['delete'])) {
	
	$deleteFile = basename($_POST['file']);
	
	if(is_file("$path/$deleteFile")) {
		if(unlink("$path/$deleteFile")) {
			fc_delete_media_data("$path/$deleteFile");
			echo '<div class="alert alert-success alert-auto-close">'.$lang['msg_file_delete'].'</div>';
		} else {
			echo '<div class="alert alert-danger"><strong>'.$path.'/'.$deleteFile.'</strong><br>'.$lang['msg_file_delete_error'].'</div>';
		}	
	} else {
		echo '<div class="alert alert-error">File ('.$path.'/'.$deleteFile.') not found</div>';
	}
}

$scan_images = fc_scandir_rec('../'.FC_CONTENT_DIR.'/images');
$scan_files = fc_scandir_rec('../'.FC_CONTENT_DIR.'/files');

$cnt_all_images = count($scan_images);
$cnt_all_files = count($scan_files);

if($disk == "1") {
	$a_files = $scan_images;
} else {
	$a_files = $scan_files;
}


/**
 * check if all files stored in media database
 * if not, catch up
 *
 * check if entries stored in media database are still there
 * if not, drop entry
 */

$dbh = new PDO("sqlite:".CONTENT_DB);
foreach($a_files as $file) {
	
	unset($mediaData);
	$filename = "$file";
	
	if(is_dir($filename)) { continue; }
	
	$sql = "SELECT media_file FROM fc_media WHERE media_file = :filename ";
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':filename', $filename, PDO::PARAM_STR);
	$sth->execute();
	
	$mediaData = $sth->fetch(PDO::FETCH_ASSOC);
	
	if(!is_array($mediaData)) {
		$sql = "INSERT INTO fc_media ( media_id, media_file, media_lang ) VALUES ( NULL, :media_file, :media_lang) ";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':media_file', $filename, PDO::PARAM_STR);
		$sth->bindParam(':media_lang', $languagePack, PDO::PARAM_STR);
		$sth->execute();
	}
	
}

$sql = "SELECT media_file FROM fc_media";
$sth = $dbh->prepare($sql);
$sth->execute();
$storedFiles = $sth->fetchAll(PDO::FETCH_COLUMN);
$dbh = null;

foreach($storedFiles as $f) {
	if(!is_file($f)) {
		fc_delete_media_data($f);
	}
}
	
$dbh = null;



/* expand filter */
if(isset($_POST['media_filter'])) {
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
		$btn_remove_keyword .= '<a class="btn btn-fc btn-sm" href="acp.php?tn=filebrowser&sub=browse&rm_keyword='.$f.'"><span class="glyphicon glyphicon-remove"></span> '.$f.'</a> ';
	}
}



$kw_form  = '<form action="acp.php?tn=filebrowser&sub=browse&d='.$disk.'" method="POST" class="form-inline">';
$kw_form .= '<div class="input-group">';
$kw_form .= '<span class="input-group-addon"><span class="glyphicon glyphicon-filter"></span></span>';
$kw_form .= '<input class="form-control input-sm" type="text" name="media_filter" value="" placeholder="Filter">';
$kw_form .= '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
$kw_form .= '</div>';
$kw_form .= '</form>';


echo '<div class="well well-sm" style="margin-bottom:10px;">';
echo '<div class="row">';
echo '<div class="col-md-12">';

echo '<div class="btn-toolbar">';
echo '<div class="btn-group">';
echo '<a class="'.$disk1_class.'" href="acp.php?tn='.$tn.'&sub=browse&d=1">'.$lang['btn_images'].' | <span class="">'.$cnt_all_images.'</small></a>';
echo '<a class="'.$disk2_class.'" href="acp.php?tn='.$tn.'&sub=browse&d=2">'.$lang['btn_files'].' | <span class="">'.$cnt_all_files.'</span></a>';
echo '</div>';
echo '<div class="btn-group">';
echo "<a class='btn btn-sm btn-default' href='acp.php?tn=$tn&sub=browse&d=$disk&sort_by_name=1'>". show_sort_arrow($_SESSION['sort_by_name']) ." $lang[filename]</a>";
echo "<a class='btn btn-sm btn-default' href='acp.php?tn=$tn&sub=browse&d=$disk&sort_by_time=1'>". show_sort_arrow($_SESSION['sort_by_time']) ." $lang[date_of_change]</a>";
echo "<a class='btn btn-sm btn-default' href='acp.php?tn=$tn&sub=browse&d=$disk&sort_by_size=1'>". show_sort_arrow($_SESSION['sort_by_size']) ." $lang[filesize]</a>";
echo '</div>';
echo '</div>';

echo '</div>';
echo '</div>';

echo '<div class="row" style="margin-top:10px;">';
echo '<div class="col-md-2">';
echo "$kw_form";
echo '</div>';
echo '<div class="col-md-10">';
echo "$btn_remove_keyword";
echo '</div>';
echo '</div>';
echo '</div>';


$fileinfo = array();
$x=0;


/**
 * if there is a filter
 * reset $a_files from scandir to entries from fc_media
 */

if(is_array($all_filter)) {
	foreach($all_filter as $f) {
		if($f == "") { continue; }
		$set_keyword_filter .= "(	media_file like '%$f%' OR
															media_title like '%$f%' OR
															media_notes like '%$f%' OR
															media_keywords like '%$f%' OR
															media_credit like '%$f%' OR
															media_text like '%$f%'
															) AND";
	}
	$set_keyword_filter = substr("$set_keyword_filter", 0, -4); // cut the last ' AND'

	$filter_string = "WHERE media_id IS NOT NULL "; // -> result = match all entries
	
	if($set_keyword_filter != "") {
		$filter_string .= " AND $set_keyword_filter";
	}
	
	$_SESSION['media_filter_string'] = $filter_string;
	
	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = "SELECT media_file FROM fc_media $_SESSION[media_filter_string] ";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	$filterFiles = $sth->fetchAll(PDO::FETCH_COLUMN);
	
	$dbh = null;
	
	unset($a_files);
	$filterFiles = array_unique($filterFiles);
	foreach($filterFiles as $file) {
		//$file = basename($file);
		if(is_file("$file")) {
			$a_files[] = $file;
		}
	}
	
}

foreach($a_files as $file) {

	$f_suffix = substr (strrchr ($file, "."), 1 );
	$f_time = filemtime("$file");
	$f_size =  filesize("$file");
	$imgsize = getimagesize("$file");
	
	if($imgsize[0] > 0) {
		$fileinfo[$x]['filetype'] = "image";
	} else {
		$fileinfo[$x]['filetype'] = "other";
	}
	
	$fileinfo[$x]['filename'] = "$file";
	$fileinfo[$x]['size'] = "$f_size";
	$fileinfo[$x]['suffix'] = "$f_suffix";
	$fileinfo[$x]['time'] = "$f_time";
	
	$x++;
}

//count all files
$nbr_of_files = count($fileinfo);

/* sorting */
foreach ($fileinfo as $key => $row) {
    $fi_filename[$key] = $row['filename'];
    $fi_size[$key] = $row['size'];
    $fi_time[$key] = $row['time'];
}


if($_SESSION['sort_by_name'] != "") {
	@array_multisort($fi_filename, $sort_direction, $fileinfo);
} elseif($_SESSION['sort_by_size'] != "") {
	@array_multisort($fi_size, $sort_direction, $fileinfo);
} elseif($_SESSION['sort_by_time'] != "") {
	@array_multisort($fi_time, $sort_direction, $fileinfo);
}

$files_per_page = 20;
$show_numbers = 6;
$start = 0;

$cnt_pages = ceil($nbr_of_files/$files_per_page);


if(isset($_GET['start'])) {
	$start = (int) $_GET['start'];
}

if($start<0) {
	$start = 0;
}


$end = $start+$files_per_page;
$next_start = $start+$files_per_page;
$prev_start = $start-$files_per_page;

if($start>($nbr_of_files-$files_per_page)) {
	$next_start = $start;
}

if($end>$nbr_of_files) {
	$end = $nbr_of_files;
}


$pag_backlink = "<a class='btn btn-primary' href='$_SERVER[PHP_SELF]?tn=filebrowser&start=$prev_start'>$lang[pagination_backward]</a>";
$pag_forwardlink = "<a class='btn btn-primary' href='$_SERVER[PHP_SELF]?tn=filebrowser&start=$next_start'>$lang[pagination_forward]</a>";

unset($pag_string);
for($x=0;$x<$cnt_pages;$x++) {

	$aclass = "btn btn-primary";
	$page_start = $x*$files_per_page;
	$page_nbr = $x+1;
	
	if($page_start == $start) {
		$aclass = "btn btn-primary active";
		
		$pag_start = 	$x-($show_numbers/2);
		
		if($pag_start < 0) {
			$pag_start = 0;
		}
		
		$pag_end = 		$pag_start+$show_numbers;
		if($pag_end > $cnt_pages) {
			$pag_end = $cnt_pages;
		}
	}
	
	$a_pag_string[] = "<a class='$aclass' href='$_SERVER[PHP_SELF]?tn=filebrowser&start=$page_start'>$page_nbr</a> ";

} //eol for $x

echo"<div id='container'>";
echo"<div id='masonry-container'>";


//list all files 
for($i=$start;$i<$end;$i++) {

	unset($filename);

	$filename = $fileinfo[$i]['filename'];
	$filetime = $fileinfo[$i]['time'];
	$filesize = round($fileinfo[$i]['size'] / 1024) . ' KB';
	$filesize = readable_filesize($fileinfo[$i]['size']);
	$show_filetime = date('d.m.Y H:i',$filetime);

	if($fileinfo[$i]['filetype'] == "image") {
		$set_style = '';
		$preview_img = "<img src='$filename'>";
	} else {
		$set_style = "background-image: url(images/no-preview.gif); background-position: center; background-repeat: no-repeat;";
		$preview_img = '';
	}
	
	
	$short_filename = str_replace('../content/files/', '', $filename);
	$short_filename = str_replace('../content/images/', '', $filename);
	
	$delete_btn = '<input type="submit" onclick="return confirm(\''.$lang['confirm_delete_file'].'\')" class="btn btn-danger btn-sm" name="delete" value="'.$lang['delete'].'">';
	$edit_btn = '<a data-fancybox data-type="ajax" data-src="/acp/core/ajax.media.php?file='.$filename.'&folder='.$disk.'" href="javascript:;" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-pencil"></span></a>';

	$tpl_list = str_replace('{short_filename}', $short_filename, $tpl_file);
	$tpl_list = str_replace('{filename}', $filename, $tpl_list);
	$tpl_list = str_replace('{set_style}', "$set_style", $tpl_list);
	$tpl_list = str_replace('{preview_img}', "$preview_img", $tpl_list);
	$tpl_list = str_replace('{show_filetime}', "$show_filetime", $tpl_list);
	$tpl_list = str_replace('{filesize}', "$filesize", $tpl_list);
	$tpl_list = str_replace('{edit_button}', "$edit_btn", $tpl_list);
	$tpl_list = str_replace('{delete_button}', "$delete_btn", $tpl_list);
	$tpl_list = str_replace('{csrf_token}', $_SESSION['token'], $tpl_list);
	
	echo $tpl_list;

} // eol $i - list all files 



echo '</div>'; // masonry-container
echo '</div>';
echo '<div class="clearfix"></div>';


echo '<div id="well well-sm"><p class="text-center">';
echo"$pag_backlink ";
foreach(range($pag_start, $pag_end) as $number) {
    echo "$a_pag_string[$number]";
}
echo " $pag_forwardlink";
echo '</p></div>'; //EOL PAGINATION




function switch_sort($session_name) {
	if($_SESSION[$session_name] == 'ASC') {
		$_SESSION[$session_name] = 'DESC';
	} else {
		$_SESSION[$session_name] = 'ASC';
	}
}

function show_sort_arrow($direction) {
	$icon = '';
	if($direction == 'ASC') {
		$icon = '<span class="glyphicon glyphicon-chevron-up"></span>';
	} elseif($direction == 'DESC') {
		$icon = '<span class="glyphicon glyphicon-chevron-down"></span>';
	}
	return $icon;
}


?>