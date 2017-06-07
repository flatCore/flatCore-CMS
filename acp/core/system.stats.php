<?php

$time_s = microtime(true);

//prohibit unauthorized access
require("core/access.php");


// defaults

$entries_per_page = 200;

if($_SESSION['start'] == "") {
	$_SESSION['start'] = 0;
}

if($_REQUEST['start'] != "") {
	$_SESSION['start'] = (int) ($_REQUEST['start']*$entries_per_page);
}

if($_SESSION['filename'] == "") {
	//default: today's logfile
	$_SESSION['filename'] = "logfile" . date('Y') . date('m') . ".sqlite3";
}

if($_POST['select_logfile']) {
	$_SESSION['filename'] = strip_tags($_POST['select_logfile']);
	$_SESSION['start'] = 0; // reset pagination
}




$start = $_SESSION['start'];
$filename = $_SESSION['filename'];





/* scan FC_CONTENT_DIR and return all logfiles */

$log_dir = "../" . FC_CONTENT_DIR . "/SQLite";
$logfiles = glob("$log_dir/logfile*");

echo"<fieldset>";
echo"<legend>$lang[select_logfile]</legend>";
echo"<form action='acp.php?tn=system&sub=stats' method='POST' class='form-inline'>";
echo '<div class="form-group">';
echo"<select name='select_logfile' class='form-control'>";

foreach($logfiles as $fn) {
	
	$fn = basename($fn);
	$get_month = 'm' . substr("$fn", 11, 2);
	$month = $lang[$get_month];
	$get_year = substr("$fn", 7, 4);

	unset($selected);
	if($filename == $fn) { $selected = "selected"; }
   		echo"<option $selected value='$fn'>$month $get_year</option>";
}

echo"</select> ";
echo '</div> ';
echo"<input type='submit' class='btn btn-default' name='select_log' value='$lang[choose]'>";
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo"</form>";
echo"</fieldset>";



$logfile_path = "../" . FC_CONTENT_DIR . "/SQLite/$filename";


if(is_file("$logfile_path")) {


// connect to database
$dbh = new PDO("sqlite:$logfile_path");


$sql_stat = "
SELECT count(*) AS 'All',
(SELECT count(*) FROM fc_logfile WHERE log_ua LIKE '%safari%' ) AS 'Safari', 
(SELECT count(*) FROM fc_logfile WHERE log_ua LIKE '%firefox%' ) AS 'Firefox',
(SELECT count(*) FROM fc_logfile WHERE log_ua LIKE '%msie%' ) AS 'Internet Explorer',
(SELECT count(*) FROM fc_logfile WHERE log_ua LIKE '%chrome%' ) AS 'Google Chrome',
(SELECT count(*) FROM fc_logfile WHERE log_ua LIKE '%netscape%' ) AS 'Netscape',
(SELECT count(*) FROM fc_logfile WHERE log_ua LIKE '%opera%' ) AS 'Opera',
(SELECT count(*) FROM fc_logfile WHERE log_ua LIKE '%camino%' ) AS 'Camino',
(SELECT count(*) FROM fc_logfile WHERE log_ua LIKE '%konqueror%' ) AS 'Konqueror',
(SELECT count(*) FROM fc_logfile WHERE log_ua LIKE '%icab%' ) AS 'iCab',
(SELECT count(*) FROM fc_logfile WHERE log_ua LIKE '%ipad%' ) AS 'iPad',
(SELECT count(*) FROM fc_logfile WHERE log_ua LIKE '%iphone%' or log_ua LIKE '%ipod%' ) AS 'iPhone/iPod',
(SELECT count(*) FROM fc_logfile WHERE log_ua LIKE '%bot%' or log_ua LIKE '%java%' or log_ua LIKE '%spider%' ) AS 'Bots'
FROM fc_logfile
";

$stat_result = $dbh->query("$sql_stat")->fetch(PDO::FETCH_ASSOC);



$cnt_entries = $stat_result['All'];


$sql = "SELECT * FROM fc_logfile
				ORDER BY log_time DESC
				LIMIT $start, $entries_per_page";

unset($result);
foreach ($dbh->query($sql) as $row) {
	$result[] = $row;
}
   
$cnt_result = count($result);
$dbh = null;





$filesize = round((filesize("$logfile_path") / 1024), 2);

$get_month = 'm' . substr("$filename", 11, 2);
$month = $lang[$get_month];
$get_year = substr("$filename", 7, 4);


echo"<p style='background:#ddd;padding:4px;'><b>$month $get_year</b> » $cnt_entries $lang[logfile_hits] » $filesize kb</p>";

echo"<div style='float:left;width:200px;padding:8px;'>";


echo"<table class='table table-condensed'>";

arsort($stat_result);

foreach ($stat_result as $k => $v) {
	if($v > 0){
    	echo "	<tr>
    						<td>$k:</td>
    						<td align='right'>$v</td>
    		  		</tr> ";
    }
}


echo"</table>";

echo"</div>"; // eo float



/* listing */

echo"<div style='margin-left:220px;padding:8px;'>";

echo"<div style='height:350px;overflow:auto;margin:0;padding:10px;background-color:#ddd;'>";


for($i=0;$i<$cnt_result;$i++) {

	$log_time = date("d.m.Y H:i:s",$result[$i]['log_time']);
	$log_ip = $result[$i]['log_ip'];
	$log_ref = $result[$i]['log_ref'];
	$log_ua = $result[$i]['log_ua'];
	$log_query = $result[$i]['log_query'];
	$log_id = $result[$i]['log_id'];


	if( $i%2 == "0" ) {
		$bg_color = "#fff";
	} else {
		$bg_color = "#f1f1f1";
	}


	echo"<dl class='dl-horizontal' style='background-color:$bg_color;margin:0;'>";

	echo"<dt>Zeit:</dt> <dd>$log_time</dd>";
	echo"<dt>IP:</dt> <dd>$log_ip</dd>";

	if($log_query != "") {
		echo"<dt>query:</dt> <dd>$log_query</dd>";
	}
	if($log_ref != "") {
		echo"<dt>Referer:</dt> <dd><span style='color:#390;'>$log_ref</span></dd>";
	}
	if($log_ua != "") {
		echo"<dt>User Agent:</dt> <dd>$log_ua</dd>";
	}
	echo"</dl>";

} // eol $i



echo"</div>";



/* pagination */
$pages = ceil($cnt_entries/$entries_per_page);
echo"<div id='pagina'><p>";
	for($i=0;$i<$pages;$i++) {
	$nbr = $i+1;
	$pag_class = "buttonLink";
	if(($i*$entries_per_page) == "$start") { $pag_class = "buttonLink_sel"; }
		echo"<a class='$pag_class' href='$_SERVER[PHP_SELF]?tn=system&sub=stats&start=$i'>$nbr</a> ";
	}

echo"</p></div>";
/* pagination */




echo"</div>"; // eo float div

} else {

echo"<div id='sys_message_error'>No logfile</div>";

}



?>