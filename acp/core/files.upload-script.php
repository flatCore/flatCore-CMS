<?php
session_start();

if($_SESSION['user_class'] != "administrator"){
	//move back to site
	header("location:../index.php");
	//or die
	die("PERMISSION DENIED!");
}

$max_w = (int) $_GET[w]; // max image width
$max_h = (int) $_GET[h]; // max image height
$max_fz = (int) $_GET[fz]; // max filesize

if(is_dir("../../$_GET[d]")) {
	$destination = $_GET[d]; // destination
}

/* UPLOAD IMAGES */
if(count($_FILES['imagesToUpload'])) {

	foreach ($_FILES["imagesToUpload"]["error"] as $key => $error) {
		if($error == UPLOAD_ERR_OK) {
	  	$tmp_name = $_FILES["imagesToUpload"]["tmp_name"][$key];
	      
	    $org_name = $_FILES["imagesToUpload"]["name"][$key];
	    $suffix 		= strtolower(substr(strrchr($org_name,'.'),1));
	    $prefix			= basename($org_name,".$suffix");
	    $img_name = clean_filename($prefix) . ".jpg";
	    
	    $target = "../../$destination/$img_name";
	    
	    move_uploaded_file($tmp_name, resize_image($tmp_name,$target, $max_w,$max_h,90));

	  }
	}

}


/* UPLOAD FILES */
if(count($_FILES['filesToUpload'])) {

	foreach ($_FILES["filesToUpload"]["error"] as $key => $error) {
		if($error == UPLOAD_ERR_OK) {
		 	$tmp_name = $_FILES["filesToUpload"]["tmp_name"][$key];   
	    $org_name = $_FILES["filesToUpload"]["name"][$key];
	    $suffix 		= strtolower(substr(strrchr($org_name,'.'),1));
	    $prefix			= basename($org_name,".$suffix");
	    $files_name = clean_filename($prefix) . ".$suffix";
	    
	    $target = "../../$destination/$files_name";
	    
	    move_uploaded_file($tmp_name, $target);

	  }
	}

}






/* functions */

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


	if($imgt) { 
		$old_image	= $imgcreatefrom("$img");
		$new_image	= imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($new_image,$old_image,0,0,0,0,$new_width,$new_height,$original_width,$original_height);
		imagejpeg($new_image,"$name",$quality);
		imagedestroy($new_image);
	}

}





function clean_filename($str) {

	$str = strtolower($str);

	$a = array('ä','ö','ü','ß',' - ',' + ','_',' / ','/'); 
	$b = array('ae','oe','ue','ss','-','-','-','-','-');
	$str = str_replace($a, $b, $str);

	$str = preg_replace('/\s/s', '_', $str);  // replace blanks -> '_'
	$str = preg_replace('/[^a-z0-9_-]/isU', '', $str); // only a-z 0-9

	$str = trim($str); 

	return $str; 
}



?>