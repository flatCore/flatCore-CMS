<?php

/**
 * list all images from content/images
 * used in tinyMCE's filebrowser
 */
 
include("../../config.php");

$path = "../../$img_path";
$abspath = '/'.$img_path;

$testmode = "off";
$images = Array();
$counter = 0;
if($handle = @opendir($path)) {
  while (false !== ($file=readdir($handle))){
  	if($file == "index.html") {continue;}
    if(strpos($file, ".") != 0) {
      $images[$counter]['title'] = $file;
      $images[$counter]['value'] = $abspath."/".$file;
      $counter++;
    }
  }
  closedir($handle);
} elseif ($testmode == "on") {
	echo "Error: Can't find directory. Please write a valid path.";
  exit;
}

if($counter == 0 && $testmode == "on") {
	echo "Error: This directory seems to be empty.";
	exit;
}

// Let PHP do the sorting an not the OS
ksort($images);

if($testmode == "off") {
  // Make output a real JavaScript file!
  // browser will now recognize the file as a valid JS file
  header('Content-type: text/javascript');
  // prevent browser from caching
  header('pragma: no-cache');
  header('expires: 0'); // i.e. contents have already expired
}

if($testmode == "on") {
	echo "<p><strong>This is the JSON I'll be delivering:</strong></p>";
}

echo json_encode($images);

?>