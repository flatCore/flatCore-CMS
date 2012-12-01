<?php

require('config.php');

echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>";
echo "<rss version=\"2.0\">";
echo "<channel>";
echo "<title>Nachrichten von $_SERVER[SERVER_NAME]</title>";
echo "<link>http://$_SERVER[SERVER_NAME]</link>";
echo "<description>Newsfeed von $_SERVER[SERVER_NAME]</description>";
echo "<language>de-de</language>";



$dbh = new PDO("sqlite:$fc_db_content");
$sql = "SELECT * FROM fc_feeds ORDER BY feed_time DESC";

   foreach ($dbh->query($sql) as $row) {
     $result[] = $row;
   }

$cnt_result = count($result);


for($i=0;$i<$cnt_result;$i++) {

$feed_time = date("d.m.Y H:i:s",$result[$i]['feed_time']);
//$feed_time = $result[$i]['feed_time'];

$feed_id = $result[$i]['feed_id'];
$feed_title = $result[$i]['feed_title'];
$feed_text = $result[$i]['feed_text'];
$feed_url = $result[$i]['feed_url'];

echo "	<item>
			<title>$feed_title</title>
			<description>$feed_text</description>
			<link>$feed_url</link>
			<pubDate>$feed_time</pubDate>
		</item>\n";


} // eo $i

echo "</channel>";
echo "</rss>";

?>