<?php

/**
 * Indexing Functions
 */

//error_reporting(E_ALL ^E_NOTICE);

/**
 * crawl a single page ($url) and return the links
 * we skip  - external and relative links and
 *          - excluded urls
 *          - images, scripts, stylesheets etc.
 */
 
function fc_crawler($id='') {
	
	global $fc_base_url;
	global $exclude_urls;
	
		
	$allowed_extensions = array('.html','.htm','.php');
	$links = array();
	
	
	if($id == '') {
		$url = '';
	} else {
		/* get url bei id */
		$dbh = new PDO("sqlite:".INDEX_DB);
		$sql = "SELECT page_url FROM pages WHERE page_id = :pid LIMIT 1";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':pid', $id, PDO::PARAM_STR);
		$sth->execute();
		$item = $sth->fetch(PDO::FETCH_ASSOC);
		$sth = null;
		$dbh = null;
		
		$url = $item['page_url'];
	}
	
	if(substr($url, 0,1) == '/') {
		$url = substr($url, 1,strlen($url));
	}
	
	$check_page = $fc_base_url.$url;
	
	if(isset($_POST['start_index']) && $_POST['start_index'] != '') {
		$check_page = $_POST['start_index'];
	}
	

	$get_html = fc_loadSourceCode($check_page);
	$links = fc_get_links($get_html);
	
	foreach($links as $link) {
		
		$href = $link['url'];

		/* we accept absolute urls only */
		if(substr("$href",0,1) !== '/') {
			continue;
		}
		
		/* if the last char issn't '/', check if we have an allowed extension */
		if(substr("$href",-1) !== '/') {
			$skip = TRUE;
			
			foreach($allowed_extensions as $extension) {
				
				$extension_length = strlen($extension);				
				$url_extension = substr($href, -$extension_length);
				
				if(in_array($url_extension, $allowed_extensions)) {
					$skip = FALSE;
				}			
			
			}
			
			if($skip === TRUE) {
				continue;
			}
		}
		
		/* skip the excluded urls */
		$skip_exclude = false;
		foreach($exclude_urls as $ex) {
			if(stristr($href, $ex['item_url']) !== FALSE) {
				$skip_exclude = true;
				break;
			}
		}

		if($skip_exclude === true) {
			continue;
		}
		
		fc_add_url($href);
		
	}

}


/**
 * get the sourcecode from $url
 */


function fc_loadSourceCode($url) {

  $ch = curl_init();
  
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, 'flatCoreBot/1.0 (+https://flatcore.org)');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE); 
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	curl_setopt($ch, CURLOPT_FAILONERROR, FALSE);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, TRUE);
	curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	curl_setopt($ch, CURLOPT_ENCODING,  '');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,TRUE);

  $data = curl_exec($ch);
  
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  
  if($http_code>=200 && $http_code<300) {
	  return $data;
  } else {
	  return false;
  }
  
}



/**
 * get al the links from $html	
 */

function fc_get_links($html) { 

    $xml = new DOMDocument(); 
    $xml->loadHTML($html,LIBXML_NOERROR); 

    $links = array(); 

    foreach($xml->getElementsByTagName('a') as $link) { 
        $links[] = array('url' => $link->getAttribute('href'), 'text' => $link->nodeValue); 
    } 

    return $links; 
}



/**
 * Collect SEO relevant data
 */

function fc_get_html_data($html) {
	
	$data = array();
	
	$d = new DOMDocument();
	$d->loadHTML($html,LIBXML_NOERROR);

	foreach($d->getElementsByTagName('h1') as $item){
  	$h1[] = $item->textContent;
  }
	foreach($d->getElementsByTagName('h2') as $item){
  	$h2[] = $item->textContent;
  }
	foreach($d->getElementsByTagName('h3') as $item){
  	$h3[] = $item->textContent;
  }

	foreach($d->getElementsByTagName('a') as $item){
  	$l = $item->textContent;
  	$links[$l]['href'] = $item->attributes->getNamedItem('href')->nodeValue;
  	$links[$l]['title'] = $item->attributes->getNamedItem('title')->nodeValue;
  }
	foreach($d->getElementsByTagName('img') as $image){
  	$i = $image->attributes->getNamedItem('src')->nodeValue;
  	$imgs[$i]['alt'] = $image->attributes->getNamedItem('alt')->nodeValue;
  	$imgs[$i]['title'] = $image->attributes->getNamedItem('title')->nodeValue;  	
  }

	$img_str = '';
	foreach($imgs as $k => $v) {	
		$img_str .= $k.'<|>'.$v['alt'].'<|>'.$v['title'].'<|-|>';
	}
	
	$data['img_str'] = substr($img_str, 0,-5);
	
	$link_str = '';
	foreach($links as $k => $v) {	
		$link_str .= $k.'<|>'.$v['title'].'<|>'.$v['href'].'<|-|>';
	}
	
	$data['link_str'] = substr($link_str, 0,-5);
	
	$data['h1_str'] = implode("<|>", $h1);
	$data['h2_str'] = implode("<|>", $h2);
	$data['h3_str'] = implode("<|>", $h3);
	
	return $data;
	
}


/**
 * save cleaned page content	
 */

function fc_update_page_index($id) {
	
	global $fc_base_url;
	global $exclude_items;
	global $exclude_urls;
	$time = time();
	
	$dbh = new PDO("sqlite:".INDEX_DB);
	
	$sql = "select page_url from pages where page_id = :id";
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':id', $id, PDO::PARAM_STR);
	$sth->execute();
	$item = $sth->fetch(PDO::FETCH_ASSOC);
	$sth = null;
	$dbh = null;
	
	$url = $item['page_url'];
	
	if(substr($url, 0,1) == '/') {
		$url = substr($url, 1,strlen($url));
	}
	
	foreach($exclude_urls as $u) {
		if($u['item_url'] == "/$url") {
			echo '<div class="alert alert-danger">removed from index: '.$url.' (page defined as excluded)</div>';
			fc_delete_url("/$url");
			return;
		}
	}
	
	$check_page = $fc_base_url.$url;
	$get_html = fc_loadSourceCode($check_page);
	
	if($get_html === false) {
		/* this page isn't available anymore */
		echo '<div class="alert alert-danger">removed from index: '.$url.' (page is not available)</div>';
		fc_delete_url("/$url");
		return;
	}
	
	$html_data = fc_get_html_data($get_html);
	
	/* extract page infos */
	
	preg_match("/<title>(.+)<\/title>/siU", $get_html, $matches);
	$page_title = $matches[1];

	$k = "<meta\s+name=['\"]??keywords['\"]??\s+content=['\"]??(.+)['\"]??\s*\/?>";
	preg_match("/$k/siU", $get_html, $matches);
	$page_keywords = $matches[1];
			
	$d = "<meta\s+name=['\"]??description['\"]??\s+content=['\"]??(.+)['\"]??\s*\/?>";
	preg_match("/$d/siU", $get_html, $matches);
	$page_description = $matches[1];
				
	
	/* cleaning */

	$get_html = str_replace("\n\r"," ",$get_html);
	$get_html = str_replace("\n"," ",$get_html);
	$get_html = str_replace("\r"," ",$get_html);
	$get_html = str_replace("/>"," />",$get_html);
			
	foreach($exclude_items as $ex_item) {
		
		$el = $ex_item['item_element']; // the element
		$att = $ex_item['item_attributes']; // the .class or #id
		$clean_att = substr($att, 1); // the attribute without "#" or "."
		
		if(substr($att, 0, 1) == '.') {
			$pattern = '~<'.$el.'([^>]*)(class\\s*=\\s*["\']'.$clean_att.'["\'])([^>]*)>(.*?)</'.$el.'>~si';
		} else if(substr($att, 0, 1) == '#') {
			$pattern = '~<'.$el.'([^>]*)(id\\s*=\\s*["\']'.$clean_att.'["\'])([^>]*)>(.*?)</'.$el.'>~si';
		} else {
			continue;
		}
		
		$get_html = preg_replace($pattern, '', $get_html);
		
	}
	
	$get_html = preg_replace("/<( )*script([^>])*>/i", "<script>", $get_html);
	$get_html = preg_replace("/<script[^>]*>[\s\S]*?<\/script>/i", "", $get_html);
	$get_html = preg_replace("/<style[^>]*>[\s\S]*?<\/style>/i", "", $get_html);
	$get_html = preg_replace("/<iframe[^>]*>[\s\S]*?<\/iframe>/i", "", $get_html);
	$get_html = preg_replace("/<head[^>]*>[\s\S]*?<\/head>/i", "", $get_html);
	$get_html = preg_replace("/<meta([^>])*\/>/i", "", $get_html);
	$get_html = preg_replace("/<td[^>]*>/", " ", $get_html);
	$get_html = preg_replace("/<br[^>]*>/", " ", $get_html);
	$get_html = preg_replace("/<[^>]*>/", "", $get_html);
		
	$get_html = str_replace("&nbsp;"," ",$get_html);
	while(strpos($get_html,"\t")!==false) $get_html = preg_replace("/\t/", " ", $get_html);
	while(strpos($get_html,'  ')!==false) $get_html = preg_replace("/  /", " ", $get_html);
	$get_html = trim($get_html);
	
	
	$dbh = new PDO("sqlite:".INDEX_DB);
	$sql = "UPDATE pages SET
		page_content = :page_content,
		page_title = :page_title,	page_description = :page_description,
		page_keywords = :page_keywords, indexed_time = :indexed_time,
		page_h1 = :page_h1, page_h2 = :page_h2, page_h3 = :page_h3,
		page_images = :page_images, page_links = :page_links
		WHERE page_id = :page_id";
		
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':page_content', $get_html, PDO::PARAM_STR);
	$sth->bindParam(':page_title', $page_title, PDO::PARAM_STR);
	$sth->bindParam(':page_description', $page_description, PDO::PARAM_STR);
	$sth->bindParam(':page_keywords', $page_keywords, PDO::PARAM_STR);
	$sth->bindParam(':page_id', $id, PDO::PARAM_STR);
	$sth->bindParam(':page_h1', $html_data['h1_str'], PDO::PARAM_STR);
	$sth->bindParam(':page_h2', $html_data['h2_str'], PDO::PARAM_STR);
	$sth->bindParam(':page_h3', $html_data['h3_str'], PDO::PARAM_STR);
	$sth->bindParam(':page_images', $html_data['img_str'], PDO::PARAM_STR);
	$sth->bindParam(':page_links', $html_data['link_str'], PDO::PARAM_STR);
	$sth->bindParam(':indexed_time', $time, PDO::PARAM_STR);
	$cnt_changes = $sth->execute();
	$dbh = null;
	
	$end_time = time();
	
	$return['duration'] = $end_time-$time;
	$return['url'] = $url;
	
	
	return $return;

}



/**
 * update the page index of the (num) oldest pages
 */

function fc_update_bulk_page_index($num=5) {

	$dbh = new PDO("sqlite:".INDEX_DB);
	$sql = "SELECT page_id FROM pages ORDER BY indexed_time ASC LIMIT $num";

	$items = $dbh->query($sql);
	$items = $items->fetchAll(PDO::FETCH_ASSOC);

	$dbh = null;
	
	foreach($items as $item) {
		$update = fc_update_page_index($item['page_id']);
	}
	

}



/**
 * crawl for links in the (num) oldest pages
 */

function fc_crawler_bulk($num=5) {

	$dbh = new PDO("sqlite:".INDEX_DB);
	$sql = "SELECT page_id FROM pages ORDER BY indexed_time ASC LIMIT $num";

	$items = $dbh->query($sql);
	$items = $items->fetchAll(PDO::FETCH_ASSOC);

	$dbh = null;
	
	foreach($items as $item) {
		$update = fc_crawler($item['page_id']);
	}
	

}




/**
 * update the page index
 * this function is called by pages.edit.php if you save a plublic or ghost page
 * if page is in index, update contents
 * if not, add as new entry
 */

function fc_update_or_insert_index($permalink) {
	
	$url = '/'.$permalink;
	$dbh = new PDO("sqlite:".INDEX_DB);
	$sql_check = "select page_id from pages where page_url = :url LIMIT 1";
	$sth = $dbh->prepare($sql_check);
	$sth->bindParam(':url', $url, PDO::PARAM_STR);
	$sth->execute();
	$entry = $sth->fetch(PDO::FETCH_ASSOC);
	$sth = null;
	$dbh = null;
	
	if($entry['page_id'] == '') {
		/* we have a new entry */
	} else {
		/* update entry */
		$update = fc_update_page_index($entry['page_id']);
	}
	
		
}



/**
 * Add URL to list
 * check for existing entries
 */

function fc_add_url($url) {
	
	
	$dbh = new PDO("sqlite:".INDEX_DB);
	$sql_check = "select count(1) from pages where page_url = :url";
	$sth = $dbh->prepare($sql_check);
	$sth->bindParam(':url', $url, PDO::PARAM_STR);
	$sth->execute();
	$cnt_entries = $sth->fetch(PDO::FETCH_NUM);
	
	if($cnt_entries[0] < 1) {
		echo '<div class="alert alert-success">Add to index: '.$url.'</div>';
	}
	
	
	$page_id = md5($url);
	
	
	$sql_insert = "INSERT INTO pages (
				docid, page_id, page_url
				) VALUES (
				NULL, :page_id, :url ) ";
	
	$std = $dbh->prepare($sql_insert);
	
	if($cnt_entries[0] < 1) {
		
		$url = htmlentities($url);		
		$std->bindParam(':url', $url, PDO::PARAM_STR);
		$std->bindParam(':page_id', $page_id, PDO::PARAM_STR);
		$add = $std->execute();
		
		
	}
	
	
	$dbh = null;
	
	
}



/**
 * delete URL from pages table
 */

function fc_delete_url($url) {
	
	$dbh = new PDO("sqlite:".INDEX_DB);
	$sql = "DELETE FROM pages WHERE page_url = :page_url";
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':page_url', $url, PDO::PARAM_STR);
	$cnt_changes = $sth->execute();
	$dbh = null;
}


/**
 * get entries from pages table
 */

function fc_get_indexed_pages() {
	$dbh = new PDO("sqlite:".INDEX_DB);
	$sql = "SELECT * FROM pages ORDER BY indexed_time ASC";

	$items = $dbh->query($sql);
	$items = $items->fetchAll(PDO::FETCH_ASSOC);

	$dbh = null;
	
	return $items;	
}






/**
 * delete from excludes (elements | url) by id
 */

function fc_delete_excludes($id) {
	
	$dbh = new PDO("sqlite:".INDEX_DB);
	$sql = "DELETE FROM excludes WHERE item_id = :item_id";
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':item_id', $id, PDO::PARAM_INT);
	$cnt_changes = $sth->execute();
	$dbh = null;
}





/**
 * write exclude elements
 * elements -> div, span, footer, aside ...
 * attribute -> id or class name f.e. #footer #sidebar
 */
 
function fc_write_exclude_elements($element,$attribute) {
	
	$dbh = new PDO("sqlite:".INDEX_DB);

	$sql = "INSERT INTO excludes	(
			item_id, item_element, item_attributes
			) VALUES (
			NULL, :item_element, :item_attributes ) ";
			
	$sth = $dbh->prepare($sql);
	
	$sth->bindParam(':item_element', $element, PDO::PARAM_STR);
	$sth->bindParam(':item_attributes', $attribute, PDO::PARAM_STR);

	$cnt_changes = $sth->execute();
	$dbh = null;
		
}




/**
 * get exclude elements
 */

function fc_get_exclude_elements() {
	$dbh = new PDO("sqlite:".INDEX_DB);
	$sql = "SELECT * FROM excludes WHERE item_url IS NULL";

	$items = $dbh->query($sql);
	$items = $items->fetchAll(PDO::FETCH_ASSOC);

	$dbh = null;
	
	return $items;	
}





/**
 * write exclude urls
 */

function fc_write_exclude_url($url) {
	
	$dbh = new PDO("sqlite:".INDEX_DB);

	$sql = "INSERT INTO excludes	(
			item_id , item_url
			) VALUES (
			NULL, :item_url ) ";
			
	$sth = $dbh->prepare($sql);
	
	$sth->bindParam(':item_url', $url, PDO::PARAM_STR);

	$cnt_changes = $sth->execute();
	$dbh = null;	
	
}




/**
 * get exclude urls
 */
 
function fc_get_exclude_urls() {
	$dbh = new PDO("sqlite:".INDEX_DB);
	$sql = "SELECT * FROM excludes WHERE item_url IS NOT NULL";

	$items = $dbh->query($sql);
	$items = $items->fetchAll(PDO::FETCH_ASSOC);

	$dbh = null;
	
	return $items;	
}


?>