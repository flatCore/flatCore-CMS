<?php

/*
Used for tinyMCE
*/

// You can't simply echo everything right away because we need to set some headers first!
$output = ''; // Here we buffer the JavaScript code we want to send to the browser.
$delimiter = "\n"; // for eye candy... code gets new lines

$output .= 'var tinyMCETemplateList = new Array(';

$directory = "../templates/"; // Use your correct (relative!) path here

// Since TinyMCE3.x you need absolute image paths in the list...
$abspath = preg_replace('~^/?(.*)/[^/]+$~', '/\\1', $_SERVER['SCRIPT_NAME']);

if(is_dir($directory)) {
    $direc = opendir($directory);


    while ($file = readdir($direc)) {
        if (!preg_match('~^\.~', $file)) { // no hidden files / directories here...
            if(is_file("$directory/$file")) {
            	if(preg_match("#^editor(.*)\.html#i",$file)){
                // We got ourselves a file! Make an array entry:
                $output .= $delimiter
                    . '["'
                    . utf8_encode("$file")
                    . '", "'
                    . utf8_encode("$abspath/$directory/$file")
                    . '"],';
            	}
        	} 
        }
    }

    $output = substr($output, 0, -1); // remove last comma from array item list (breaks some browsers)
    $output .= $delimiter;

    closedir($direc);
}

$output .= ');'; // Finish code: end of array definition. Now we have the JavaScript code ready!

header('Content-type: text/javascript'); // Make output a real JavaScript file!

echo $output; // Now we can send data to the browser because all headers have been set!

?>