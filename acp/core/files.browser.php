<?php

//prohibit unauthorized access
require("core/access.php");

$deleteFile = strip_tags(basename($_GET['delete']));

if(isset($_GET[d])) {
	$_SESSION[disk] = (int) $_GET[d];
}

if(isset($_SESSION[disk])) {
	$disk = "$_SESSION[disk]";
}





if($disk == "2") {
	//show content of files
	$path = "../content/files";
	$disk2_class = "buttonLink_sel";
	$disk1_class = "buttonLink";
} else {
	//show content of images
	$path = "../content/images";
	$disk1_class = "buttonLink_sel";
	$disk2_class = "buttonLink";
}


echo"<a class='$disk1_class' href='$_SERVER[PHP_SELF]?tn=filebrowser&sub=browse&d=1'>Grafiken</a> ";
echo"<a class='$disk2_class' href='$_SERVER[PHP_SELF]?tn=filebrowser&sub=browse&d=2'>Dateien</a>";



/*
DELETE FILE OR IMAGE
*/
if($deleteFile !== "") {

	if(is_file("$path/$deleteFile")) {
	
		if(unlink("$path/$deleteFile")) {
			echo"<div class='alert alert-success'>$lang[msg_file_delete]</div>";
		} else {
			echo"<div class='alert alert-error'>$lang[msg_file_delete_error]</div>";
		}
		
	} else {
		echo"<div class='alert alert-error'>File not found</div>";
	}

}



$a_files = scandir("$path");


$fileinfo = array();
$x=0;


foreach($a_files as $file) {

	/* no files like . or .. or .filename */ 
	if((substr($file, 0, 1) == ".")) {
		continue;
	}
	
	if($file == 'index.html') {
		continue;
	}
	
		$f_suffix = substr (strrchr ($file, "."), 1 );
		$f_time = date ("YmdHis", filemtime("$path/$file"));
		
		if($_SESSION[prefs_showfilesize] == "yes") {
			$f_size =  filesize("$path/$file");
		}
		
	
		$imgsize = getimagesize("$path/$file");
	
		
		
	if($imgsize[0] > 0) {
		$fileinfo[$x][filetype] = "image";
	} else {
		$fileinfo[$x][filetype] = "other";
	}
	
	$fileinfo[$x][filename] = "$file";
	$fileinfo[$x][size] = "$f_size";
	$fileinfo[$x][suffix] = "$f_suffix";
	$fileinfo[$x][time] = "$f_time";
	
	$x++;
} // eol foreach 


//count all files
$nbr_of_files = count($fileinfo);

$files_per_page = 20;
$show_numbers = 6;
$start = 0;

$cnt_pages = ceil($nbr_of_files/$files_per_page);


if(isset($_GET[start])) {
	$start = (int) $_GET[start];
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



$pag_backlink = "<a class='buttonLink' href='$_SERVER[PHP_SELF]?tn=filebrowser&start=$prev_start'>$lang[pagination_backward]</a>";

$pag_forwardlink = "<a class='buttonLink' href='$_SERVER[PHP_SELF]?tn=filebrowser&start=$next_start'>$lang[pagination_forward]</a>";


unset($pag_string);
for($x=0;$x<$cnt_pages;$x++) {

	$aclass = "buttonLink";
	$page_start = $x*$files_per_page;
	$page_nbr = $x+1;
	
	if($page_start == $start) {
		$aclass = "buttonLink_sel";
		
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




echo"<div id='filesbox'>";



//list all files 
for($i=$start;$i<$end;$i++) {

	$filename = $fileinfo[$i][filename];
	$filetime = $fileinfo[$i][time];
	
	if($_SESSION[prefs_showfilesize] == "yes") {
		$filesize = round ($fileinfo[$i][size] / 1024) . " KB";
	} else {
		$filesize = "";
	}
	
	$show_filetime = readable_timestring($filetime);


	if($fileinfo[$i][filetype] == "image") {
		$set_style = "background-image: url($path/$filename); background-position: center; background-repeat: no-repeat;";
		$preview_button = "<a href='$path/$filename' data-milkbox='single' title='$path/$filename' class='btn btn-mini'>Vorschau</a>";
	} else {
		$set_style = "background-image: url(images/no-preview.gif); background-position: center; background-repeat: no-repeat;";
		$preview_button = "<a href='#' class='btn btn-mini disabled'>Vorschau</a>";
	}


echo"<div class='floating-box'>";

echo"<h4>$filename</h4>";

echo"<div id='previewbox' style=\"$set_style\";></div>";

echo"<div id='fileslist_text'>";

echo"<span class='text'>$filesize</span>";
echo"<span class='text'>$show_filetime</span>";
echo"<span class='text'>";

echo'<div class="btn-group">';
echo"$preview_button ";
echo"<a href='$_SERVER[PHP_SELF]?tn=filebrowser&sub=browse&delete=$filename&d=$disk' onclick=\"return confirm('$lang[confirm_delete_file]')\" class='btn btn-danger btn-small btn-mini'>L&ouml;schen</a></span>";
echo'</div>';

echo"<div id='clear'></div>";

echo"</div>";
echo"</div>";




} // eol $i - list all files 



echo"</div>"; // eol div filesbox

echo"<div style='clear:both;'></div>";


echo"<div id='pagina'><p>";
echo"$pag_backlink ";
foreach(range($pag_start, $pag_end) as $number) {
    echo "$a_pag_string[$number]";
}
echo" $pag_forwardlink";
echo"</p></div>\n"; //EOL PAGINATION


?>