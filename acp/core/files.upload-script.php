<?php
session_start();
error_reporting(0);

require '../../config.php';
define("CONTENT_DB", "../../$fc_db_content");

if($_SESSION['user_class'] != "administrator"){
	header("location:../index.php");
	die("PERMISSION DENIED!");
}

if($_POST['csrf_token'] !== $_SESSION['token']) {
	die('Error: CSRF Token is invalid');
}

$time = time();

$max_w = (int) $_REQUEST['w']; // max image width
$max_h = (int) $_REQUEST['h']; // max image height
$max_fz = (int) $_REQUEST['fz']; // max filesize

if(strpos($_REQUEST['upload_destination'],"/images") !== false) {
	$destination = '../'.$_REQUEST['upload_destination'];
	$upload_type = 'images';
} else if(strpos($_REQUEST['upload_destination'],"/files") !== false) {
	$destination = '../'.$_REQUEST['upload_destination'];
	$upload_type = 'files';
}


/* upload images to /content/images/ */
if($upload_type == 'images') {
	if(array_key_exists('file',$_FILES) && $_FILES['file']['error'] == 0 ){
		$tmp_name = $_FILES['file']['tmp_name'];
		$org_name = $_FILES['file']['name'];
		$suffix = substr(strrchr($org_name,'.'),1);
		$prefix = basename($org_name,".$suffix");
		$img_name = clean_filename($prefix,$suffix);
		$target = "$destination/$img_name";
		
		//$fc_upload_img_types from config.php
		if(!in_array($suffix, $fc_upload_img_types)) {
			exit;
		} else {

			if($_REQUEST['unchanged'] == 'yes' OR $suffix == 'svg') {
				@move_uploaded_file($tmp_name, $target);
			} else {
				resize_image($tmp_name,$target, $max_w,$max_h,90);
			}
			$filetype = mime_content_type(realpath($target));
			$filesize = filesize(realpath($target));
			if($_POST['file_mode'] != 'overwrite') {
				fc_write_media_data_name($target,$filesize,$time,$filetype);
			}
			
		}

	}
}


/* upload files to /content/files/ */
if($upload_type == 'files') {
	if(array_key_exists('file',$_FILES) && $_FILES['file']['error'] == 0 ){
		$tmp_name = $_FILES["file"]["tmp_name"];   
	  $org_name = $_FILES["file"]["name"];
	  $suffix = substr(strrchr($org_name,'.'),1);
	  $prefix = basename($org_name,".$suffix");
	  $files_name = clean_filename($prefix,$suffix);
	  $target = "$destination/$files_name";
	  
	  $fc_upload_types = array_merge($fc_upload_img_types,$fc_upload_file_types);
	  if(!in_array($suffix, $fc_upload_types)) {
			exit;
		} else {
			@move_uploaded_file($tmp_name, $target);
			$filetype = mime_content_type(realpath($target));
			$filesize = filesize(realpath($target));
			if($_POST['file_mode'] != 'overwrite') {
				fc_write_media_data_name($target,$filesize,$time,$filetype);
			}		
		}
	  


	}
}

/* upload files to /upload/plugins/ */
if($_REQUEST['upload_type'] == 'plugin') {
	if(array_key_exists('file',$_FILES) && $_FILES['file']['error'] == 0 ){
		$tmp_name = $_FILES["file"]["tmp_name"];   
	  $org_name = $_FILES["file"]["name"];
	  $suffix = strtolower(substr(strrchr($org_name,'.'),1));
	  $prefix = basename($org_name,".$suffix");
	  $files_name = clean_filename($prefix,$suffix);
	  if(!is_dir('../../upload/plugins')) {
		  mkdir("../../upload/plugins", 0777, true);
	  }
	  $target = "../../upload/plugins/$files_name";
		@move_uploaded_file($tmp_name, $target);
	}
}

/* upload files to /upload/themes/ */
if($_REQUEST['upload_type'] == 'theme') {
	if(array_key_exists('file',$_FILES) && $_FILES['file']['error'] == 0 ){
		$tmp_name = $_FILES["file"]["tmp_name"];   
	  $org_name = $_FILES["file"]["name"];
	  $suffix = strtolower(substr(strrchr($org_name,'.'),1));
	  $prefix = basename($org_name,".$suffix");
	  $files_name = clean_filename($prefix,$suffix);
	  if(!is_dir('../../upload/themes')) {
		  mkdir("../../upload/themes", 0777, true);
	  }
	  $target = "../../upload/themes/$files_name";
		@move_uploaded_file($tmp_name, $target);
	}
}

/* upload files to /upload/modules/ */
if($_REQUEST['upload_type'] == 'module') {
	if(array_key_exists('file',$_FILES) && $_FILES['file']['error'] == 0 ){
		$tmp_name = $_FILES["file"]["tmp_name"];   
	  $org_name = $_FILES["file"]["name"];
	  $suffix = strtolower(substr(strrchr($org_name,'.'),1));
	  $prefix = basename($org_name,".$suffix");
	  $files_name = clean_filename($prefix,$suffix);
	  if(!is_dir('../../upload/modules')) {
		  mkdir("../../upload/modules", 0777, true);
	  }
	  $target = "../../upload/modules/$files_name";
		@move_uploaded_file($tmp_name, $target);
	}
}


function resize_image($img, $name, $thumbnail_width, $thumbnail_height, $quality){

	$arr_image_details	= GetImageSize("$img");
	$original_width		= $arr_image_details[0];
	$original_height	= $arr_image_details[1];
	
	$a = $thumbnail_width / $thumbnail_height;
  $b = $original_width / $original_height;


	if($a<$b) {
     $new_width = $thumbnail_width;
     $new_height	= intval($original_height*$new_width/$original_width);
  } else {
     $new_height = $thumbnail_height;
     $new_width	= intval($original_width*$new_height/$original_height);
  }
  
  if(($original_width <= $thumbnail_width) AND ($original_height <= $thumbnail_height)) {
	  $new_width = $original_width;
	  $new_height = $original_height;
  }
  
  

	if($arr_image_details[2]==1) { $imgt = "imagegif"; $imgcreatefrom = "imagecreatefromgif";  }
	if($arr_image_details[2]==2) { $imgt = "imagejpeg"; $imgcreatefrom = "imagecreatefromjpeg";  }
	if($arr_image_details[2]==3) { $imgt = "imagepng"; $imgcreatefrom = "imagecreatefrompng";  }

	if($imgt == 'imagejpeg') { 
		$old_image	= $imgcreatefrom("$img");
		$new_image	= imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($new_image,$old_image,0,0,0,0,$new_width,$new_height,$original_width,$original_height);
		imagejpeg($new_image,"$name",$quality);
		imagedestroy($new_image);
	}

	if($imgt == 'imagepng') { 
		$old_image	= $imgcreatefrom("$img");
		$new_image	= imagecreatetruecolor($new_width, $new_height);
		imagealphablending($new_image, false);
		imagesavealpha($new_image, true);
		$transparency = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
		imagefilledrectangle($new_image, 0, 0, $new_width, $new_height, $transparency);
		imagecopyresampled($new_image,$old_image,0,0,0,0,$new_width,$new_height,$original_width,$original_height);
		imagepng($new_image,"$name",0);
	}	

	if($imgt == 'imagegif') {	
		return $name;
	}

}


function increment_prefix($cnt,$target) {

	$nbr = $cnt+1;
	$path = pathinfo($target);
	$filepath = $path['dirname'];
	$filename = $path['filename'];
	$extension = $path['extension'];

	if(substr("$filename", -2,1) == '_' AND is_numeric(substr("$filename", -1))) {
		$filename_without_nbr = substr("$filename", 0,-2);
		$new_filename = $filename_without_nbr.'_'.$nbr;
		$new_target = "$filepath/$new_filename.$extension";

		if(is_file("$new_target")) {
			$nbr = increment_prefix($nbr,$new_target);
		}

	} else {
		$new_target = "$filepath/$filename"."_$nbr.".$extension;
		if(is_file("$new_target")) {
			$nbr = increment_prefix($nbr,$new_target);
		}
	}
	return $nbr;
}


function clean_filename($prefix,$suffix) {

	global $destination;
	$prefix = strtolower($prefix);

	$a = array('ä','ö','ü','ß',' - ',' + ','_',' / ','/'); 
	$b = array('ae','oe','ue','ss','-','-','_','-','-');
	$prefix = str_replace($a, $b, $prefix);
	$prefix = preg_replace('/\s/s', '_', $prefix);  // replace blanks -> '_'
	$prefix = preg_replace('/[^a-z0-9_-]/isU', '', $prefix); // only a-z 0-9
	$prefix = trim($prefix);

	$target = "$destination/$prefix.$suffix";

	if((is_file($target) && $_POST['file_mode'] != 'overwrite')) {
		$prefix = $prefix . '_' . increment_prefix('0',"$target");	    
	}


	$filename = $prefix . '.' . $suffix;
	$filename = strtolower($filename);

	return $filename; 
}



function fc_write_media_data_name($filename,$filesize,$time,$mediatype) {
	$filename = substr($filename, 3,strlen($filename));
	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = "INSERT INTO fc_media ( media_id, media_file, media_filesize, media_lastedit, media_type ) VALUES ( NULL, :media_file, :media_filesize, :media_lastedit, :media_type ) ";
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':media_file', $filename, PDO::PARAM_STR);
	$sth->bindParam(':media_filesize', $filesize, PDO::PARAM_STR);
	$sth->bindParam(':media_lastedit', $time, PDO::PARAM_STR);
	$sth->bindParam(':media_type', $mediatype, PDO::PARAM_STR);
	$cnt_changes = $sth->execute();
	$dbh = null;
}

?>