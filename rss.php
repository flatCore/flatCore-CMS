<?php

require('config.php');

echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>";
echo "<rss version=\"2.0\">";
echo "<channel>";
echo "<title>RSS | $_SERVER[SERVER_NAME]</title>";
echo "<link>http://$_SERVER[SERVER_NAME]</link>";
echo "<description>Newsfeed $_SERVER[SERVER_NAME]</description>";
echo "<language>de-de</language>";

$ts_now = time();
$ts_min_release = $ts_now - (60*60);

$dbh = new PDO("sqlite:$fc_db_content");
$sql = "SELECT * FROM fc_feeds WHERE feed_time > $ts_min_release ORDER BY feed_time DESC";

   foreach ($dbh->query($sql) as $row) {
     $rssItems[] = $row;
   }

$cnt_rssItems = count($rssItems);


for($i=0;$i<$cnt_rssItems;$i++) {

	$feed_time = date("d.m.Y H:i:s",$rssItems[$i]['feed_time']);
	$feed_id = $rssItems[$i]['feed_id'];
	$feed_title = $rssItems[$i]['feed_title'];
	$feed_text = $rssItems[$i]['feed_text'];
	$feed_url = $rssItems[$i]['feed_url'];
	
	echo "<item>
					<title>$feed_title</title>
					<description>$feed_text</description>
					<link>$feed_url</link>
					<pubDate>$feed_time</pubDate>
				</item>\n";
}

echo "</channel>";
echo "</rss>";

?>