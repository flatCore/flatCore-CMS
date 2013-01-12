<?php
header("Content-Type: application/rss+xml");

require('config.php');

echo '<?xml version="1.0" encoding="utf-8" ?>';
echo '<rss version="2.0">';
echo '<channel>';
echo "<title>RSS | $_SERVER[SERVER_NAME]</title>";
echo "<link>http://$_SERVER[SERVER_NAME]</link>";
echo "<description>Newsfeed $_SERVER[SERVER_NAME]</description>";
echo '<language>de-de</language>';


$ts_now = time();

$dbh = new PDO("sqlite:$fc_db_content");
$sql = "SELECT * FROM fc_feeds ORDER BY feed_time DESC";

   foreach ($dbh->query($sql) as $row) {
     $rssItems[] = $row;
   }

$cnt_rssItems = count($rssItems);

for($i=0;$i<$cnt_rssItems;$i++) {

	$feed_time = $rssItems[$i]['feed_time'];
	$time_diff = $feed_time + $fc_rss_time_offset;
	
	if($time_diff < $ts_now) {
	
		$feed_date = date("d.m.Y H:i:s",$feed_time);
		$feed_id = $rssItems[$i]['feed_id'];
		$feed_title = stripslashes($rssItems[$i]['feed_title']);
		$feed_text = stripslashes($rssItems[$i]['feed_text']);
		$feed_url = $rssItems[$i]['feed_url'];
		
		echo "<item>
						<title>$feed_title</title>
						<description>$feed_text</description>
						<link>$feed_url</link>
						<pubDate>$feed_date</pubDate>
					</item>\n";
	}
}

echo "</channel>";
echo "</rss>";

?>