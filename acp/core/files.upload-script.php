<?php
session_start();
error_reporting(0);

require '../../lib/Medoo.php';
use Medoo\Medoo;

if($_SESSION['user_class'] != "administrator"){
	header("location:../index.php");
	die("PERMISSION DENIED!");
}

require '../../config.php';
if(is_file('../../'.FC_CONTENT_DIR.'/config.php')) {
	include '../../'.FC_CONTENT_DIR.'/config.php';
}

if(is_file('../../config_database.php')) {
	include '../../config_database.php';
	$db_type = 'mysql';
	
	$database = new Medoo([

		'database_type' => 'mysql',
		'database_name' => "$database_name",
		'server' => "$database_host",
		'username' => "$database_user",
		'password' => "$database_psw",
	 
		'charset' => 'utf8',
		'port' => $database_port,
	 
		'prefix' => DB_PREFIX
	]);
	
	$db_content = $database;
	$db_user = $database;
	$db_statistics = $database;	
	
	
	
} else {
	$db_type = 'sqlite';
	
	if(isset($fc_content_files) && is_array($fc_content_files)) {
		/* switch database file $fc_db_content */
		include 'core/contentSwitch.php';
	}
	
	
	define("CONTENT_DB", "$fc_db_content");

	$db_content = new Medoo([
		'database_type' => 'sqlite',
		'database_file' => CONTENT_DB
	]);
}




if($_POST['csrf_token'] !== $_SESSION['token']) {
	die('Error: CSRF Token is invalid');
}

$time = time();

$max_w = (int) $_REQUEST['w']; // max image width
$max_h = (int) $_REQUEST['h']; // max image height
$max_w_tmb = (int) $_REQUEST['w_tmb']; // max thumbnail width
$max_h_tmb = (int) $_REQUEST['h_tmb']; // max thumbnail height
$max_fz = (int) $_REQUEST['fz']; // max filesize

if($max_w_tmb < 1) {
	$max_w_tmb = 250;
}

if($max_h_tmb < 1) {
	$max_h_tmb = 250;
}

if(strpos($_REQUEST['upload_destination'],"/images") !== false) {
	$destination = '../'.$_REQUEST['upload_destination'];
	$upload_type = 'images';
} else if(strpos($_REQUEST['upload_destination'],"/files") !== false) {
	$destination = '../'.$_REQUEST['upload_destination'];
	$upload_type = 'files';
}

/* thumbnail directories */
$tmb_dir = '../../'.$img_tmb_path;
$tmb_dir_year = $tmb_dir.'/'.date('Y',time());
$tmb_destination = $tmb_dir_year.'/'.date('m',time());
if(!is_dir($tmb_dir_year)) {
	mkdir($tmb_dir_year);
}
if(!is_dir($tmb_destination)) {
	mkdir($tmb_destination);
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
				resize_image($tmp_name,$target,$max_w,$max_h,100);
				$tmb_name = md5(substr($target, 3,strlen($target))).'.jpg';
				$store_tmb_name = $tmb_destination.'/'.$tmb_name;
				fc_create_tmb($target,$tmb_name,$max_w_tmb,$max_h_tmb,80);
			}
						
			$filetype = mime_content_type(realpath($target));
			$filesize = filesize(realpath($target));
			if($_POST['file_mode'] != 'overwrite') {
				fc_write_media_data_name($target,$store_tmb_name,$filesize,$time,$filetype);
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
				fc_write_media_data_name($target,'',$filesize,$time,$filetype);
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
	if($arr_image_details[2]==18) { $imgt = "imagewebp"; $imgcreatefrom = "imagecreatefromwebp";  }

	if($imgt == 'imagejpeg') { 
		$old_image	= $imgcreatefrom("$img");
		$new_image	= imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($new_image,$old_image,0,0,0,0,$new_width,$new_height,$original_width,$original_height);
		imagejpeg($new_image,"$name",$quality);
		imagedestroy($new_image);
	}
	
	if($imgt == 'imagewebp') { 
		$old_image	= $imgcreatefrom("$img");
		$new_image	= imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($new_image,$old_image,0,0,0,0,$new_width,$new_height,$original_width,$original_height);
		imagewebp($new_image,"$name",$quality);
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



function fc_write_media_data_name($filename,$store_tmb_name,$filesize,$time,$mediatype) {
	
	global $db_content;
	global $languagePack;
	
	$filename = substr($filename, 3,strlen($filename));
	$store_tmb_name = substr($store_tmb_name, 3,strlen($store_tmb_name));
	$uploader = $_SESSION['user_nick'];
	
	$columns = [
		"media_file" => "$filename",
		"media_thumb" => "$store_tmb_name",
		"media_filesize" => "$filesize",
		"media_lastedit" => "$time",
		"media_upload_time" => "$time",
		"media_upload_from" => "$uploader",
		"media_type" => "$mediatype",
		"media_lang" => $_SESSION['lang']	
	];
	
	$cnt_changes = $db_content->insert("fc_media", $columns);

}



function fc_create_tmb($img_src, $tmb_name, $tmb_width, $tmb_height, $tmb_quality) {
	
	global $tmb_destination;
	
	$arr_image_details	= GetImageSize("$img_src");
	$original_width		= $arr_image_details[0];
	$original_height	= $arr_image_details[1];
	$a = $tmb_width / $tmb_height;
  $b = $original_width / $original_height;
	
	
	if ($a<$b) {
     $new_width = $tmb_width;
     $new_height	= intval($original_height*$new_width/$original_width);
  } else {
     $new_height = $tmb_height;
     $new_width	= intval($original_width*$new_height/$original_height);
  }
	
	if(($original_width <= $tmb_width) AND ($original_height <= $tmb_height)) {
	  $new_width = $original_width;
	  $new_height = $original_height;
  }
  
	if($arr_image_details[2]==1) { $imgt = "imagegif"; $imgcreatefrom = "imagecreatefromgif";  }
	if($arr_image_details[2]==2) { $imgt = "imagejpeg"; $imgcreatefrom = "imagecreatefromjpeg";  }
	if($arr_image_details[2]==3) { $imgt = "imagepng"; $imgcreatefrom = "imagecreatefrompng";  }
	if($arr_image_details[2]==18) { $imgt = "imagewebp"; $imgcreatefrom = "imagecreatefromwebp";  }
	
	
	if($imgt) { 
		$old_image	= $imgcreatefrom("$img_src");
		$new_image	= imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($new_image,$old_image,0,0,0,0,$new_width,$new_height,$original_width,$original_height);
		imagejpeg($new_image,"$tmb_destination/$tmb_name",$tmb_quality);
		imagedestroy($new_image);
	}
	
}


?>