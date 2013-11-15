<?php

require('config.php');

switch ($_REQUEST['type']) {
	case 'rss':
		$type = 'rss';
		break;
	case 'atom';
		$ype = 'atom';
		break;
	default:
		$type = 'rss';
		
}

$ts_now = time();

$dbh = new PDO("sqlite:$fc_db_content");
$sql = "SELECT * FROM fc_feeds ORDER BY feed_time DESC";

foreach ($dbh->query($sql) as $row) {
 $rssItems[] = $row;
}

$sql_prefs = "SELECT * FROM fc_preferences WHERE prefs_id = 1";
$prefs = $dbh->query($sql_prefs);
$prefs = $prefs->fetch(PDO::FETCH_ASSOC);

$dbh = null;

$cnt_rssItems = count($rssItems);


$header_rss_tpl  = '<?xml version="1.0" encoding="utf-8" ?>';
$header_rss_tpl .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
$header_rss_tpl .= '<channel>';
$header_rss_tpl .= '<title>'.$prefs['prefs_pagetitle'].'</title>';
$header_rss_tpl .= '<link>http://'.$_SERVER['SERVER_NAME'].'</link>';
$header_rss_tpl .= '<description>Feed @ '.$_SERVER['SERVER_NAME'].'</description>';
$header_rss_tpl .= '<atom:link href="http://'.$_SERVER['SERVER_NAME'].'/rss.php" rel="self" type="application/rss+xml" />';

$header_atom_tpl  = '<?xml version="1.0" encoding="utf-8" ?>';
$header_atom_tpl .= '<feed xmlns="http://www.w3.org/2005/Atom">';
$header_atom_tpl .= '<id>tag:'.$_SERVER['SERVER_NAME'].',2013-11-14:'. urlencode($prefs['prefs_pagetitle']) .'</id>';
$header_atom_tpl .= '<link rel="self" type="application/atom+xml" href="http://'.$_SERVER['SERVER_NAME'].'/rss.php?type=atom" />';
$header_atom_tpl .= '<title>'.$prefs['prefs_pagetitle'].'</title>';
$header_atom_tpl .= '<author><name>'.$_SERVER['SERVER_NAME'].'</name></author>';
$header_atom_tpl .= '<subtitle>Feed @ '.$_SERVER['SERVER_NAME'].'</subtitle>';
$header_atom_tpl .= '<updated>'. gmdate("Y-m-d\TH:i:s\Z", $rssItems[0]['feed_time']) .'</updated>';

$entry_rss_tpl = '<item>
										<title>{entryTitle}</title>
										<description><![CDATA[{entryContent}]]></description>
										<link>{entryURL}</link>
										<guid isPermaLink="true">{entryURL}</guid>
										<pubDate>{entryDate}</pubDate>
									</item>';

$entry_atom_tpl = '	<entry>
											<title>{entryTitle}</title>
											<link href="{entryURL}"/>
											<id>{entryID}</id>
											<updated>{entryDate}</updated>
											<summary type="html"><![CDATA[{entryContent}]]></summary>
										</entry>';


$end_rss_tpl = '</channel></rss>';
$end_atom_tpl = '</feed>';


for($i=0;$i<$cnt_rssItems;$i++) {

	$feed_time = $rssItems[$i]['feed_time'];
	$time_diff = $feed_time + $prefs['prefs_rss_time_offset'];
	
	if($time_diff < $ts_now) {
	
		$feed_date = date("d.m.Y H:i:s",$feed_time);
		$feed_id = $rssItems[$i]['feed_id'];
		$feed_title = stripslashes($rssItems[$i]['feed_title']);
		$feed_text = stripslashes($rssItems[$i]['feed_text']);
		$feed_url = str_replace('&','&amp;',$rssItems[$i]['feed_url']);
		
		if($type == 'rss') {
			$item_tpl = $entry_rss_tpl;
			$item_tpl = str_replace('{entryTitle}', $feed_title, $item_tpl);
			$item_tpl = str_replace('{entryContent}', $feed_text, $item_tpl);
			$item_tpl = str_replace('{entryURL}', $feed_url, $item_tpl);
			$item_tpl = str_replace('{entryDate}', date(DATE_RFC822, $feed_time), $item_tpl);
			$entry_str .= $item_tpl;
		} else {
			$item_tpl = $entry_atom_tpl;
			$entry_id = 'tag:'.$_SERVER['SERVER_NAME'].','. date("Y") .':'. $feed_url;
			$item_tpl = str_replace('{entryTitle}', $feed_title, $item_tpl);
			$item_tpl = str_replace('{entryContent}', $feed_text, $item_tpl);
			$item_tpl = str_replace('{entryURL}', $feed_url, $item_tpl);
			$item_tpl = str_replace('{entryID}', $entry_id, $item_tpl);
			$item_tpl = str_replace('{entryDate}', gmdate("Y-m-d\TH:i:s\Z",$feed_time), $item_tpl);
			$entry_str .= $item_tpl;			
		}

	}
}


if($type == 'rss') {
	header("Content-Type: application/rss+xml");
	echo $header_rss_tpl;
	echo $item_tpl;
	echo $end_rss_tpl;
} else {
	header("Content-Type: application/atom+xml");
	echo $header_atom_tpl;
	echo $item_tpl;
	echo $end_atom_tpl;
}

?>