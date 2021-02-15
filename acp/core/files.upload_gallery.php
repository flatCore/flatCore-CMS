<?php

$year = date('Y',time());
$gallery_id = 'gallery'. (int) $_REQUEST['gal'];
$uploads_dir = '../../content/galleries/'.$year.'/'.$gallery_id;

$max_width = (int) $_REQUEST['w']; // max image width
$max_height = (int) $_REQUEST['h']; // max image height
$max_width_tmb = (int) $_REQUEST['w_tmb']; // max thumbnail width
$max_height_tmb = (int) $_REQUEST['h_tmb']; // max thumbnail height

if(!is_dir($uploads_dir)) {
	mkdir($uploads_dir, 0777, true);
}

if(!is_dir($uploads_dir)) {
	$result['uploaddir'] = "Folder missing";
} else {
	$result['uploaddir'] = "Folder okay";
}

if(array_key_exists('file',$_FILES) && $_FILES['file']['error'] == 0 ){
	$tmp_name = $_FILES["file"]["tmp_name"];
	$timestring = microtime(true);
	      
	$suffix = strrchr($_FILES["file"]["name"],".");
	$org_name = $timestring . $suffix;
	$img_name = $timestring."_img.jpg";
	$tmb_name = $timestring."_tmb.jpg";
        
	if(move_uploaded_file($tmp_name, "$uploads_dir/$org_name")) {
		create_thumbs($uploads_dir,$org_name,$img_name, $max_width,$max_height,90);
		create_thumbs($uploads_dir,$img_name,$tmb_name, $max_width_tmb,$max_height_tmb,80);
		unlink("$uploads_dir/$org_name");
		print ('Uploaded');
	}
}
function create_thumbs($updir, $img, $name, $thumbnail_width, $thumbnail_height, $quality){
	$arr_image_details	= GetImageSize("$updir/$img");
	$original_width		= $arr_image_details[0];
	$original_height	= $arr_image_details[1];
	$a = $thumbnail_width / $thumbnail_height;
  $b = $original_width / $original_height;
	
	
	if ($a<$b) {
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
		$old_image	= $imgcreatefrom("$updir/$img");
		$new_image	= imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($new_image,$old_image,0,0,0,0,$new_width,$new_height,$original_width,$original_height);
		imagejpeg($new_image,"$updir/$name",$quality);
		imagedestroy($new_image);
	}
}
?>