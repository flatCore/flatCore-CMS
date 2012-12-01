<?php

/**
 * create logfiles
 * used in acp > system > stats
 */

// Prevent direct access
if(!defined("FC_CORE_DIR")) {
	 header("Location: ../../index.php");
}

$date_month = date('m');
$date_year = date('Y');

$filename = "logfile" . $date_year . $date_month . ".sqlite3";

$logfile_path = FC_CONTENT_DIR . "/SQLite/$filename";

if(!is_file($logfile_path)) {
	// create logfile

	$schema = "CREATE TABLE fc_logfile ( 
				log_id INTEGER NOT NULL PRIMARY KEY ,
				log_time VARCHAR , 
				log_ip VARCHAR ,
				log_ref VARCHAR , 
				log_ua INTEGER ,
				log_query VARCHAR );
				";

	$dbh = new PDO("sqlite:$logfile_path");
	$dbh->query($schema);
	$dbh = null;

	@chmod("$logfile_path", 0755);

} // eo create logfile




/* collect needed data */

$log = array();

$log_time = time();
$log_pid = $p;
$log_ip = $_SERVER['REMOTE_ADDR'];
$log_ua = $_SERVER['HTTP_USER_AGENT'];
$log_ref = $_SERVER['HTTP_REFERER'];
$log_query = $_SERVER['QUERY_STRING'];

$parse_ref = parse_url($log_ref);

if($parse_ref[host] == $_SERVER['HTTP_HOST']) {
	$log_ref = "";
}


$dbh = new PDO("sqlite:$logfile_path");

$sql = "INSERT INTO fc_logfile (
			  log_id, log_query, log_ref, log_ip, log_time, log_ua
		    ) VALUES (
		 	  NULL, :log_query, :log_ref, :log_ip, :log_time, :log_ua )";

$sth = $dbh->prepare($sql);
$sth->bindParam(':log_query', $log_query, PDO::PARAM_STR);
$sth->bindParam(':log_ref', $log_ref, PDO::PARAM_STR);
$sth->bindParam(':log_ip', $log_ip, PDO::PARAM_STR);
$sth->bindParam(':log_time', $log_time, PDO::PARAM_STR);
$sth->bindParam(':log_ua', $log_ua, PDO::PARAM_STR);


$sth->execute();


$dbh = null;


?>