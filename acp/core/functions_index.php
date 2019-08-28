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
		$sql = "SELECT page_url FROM pages WHERE page_id = :pid";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':pid', $id, PDO::PARAM_STR);
		$sth->execute();
		$item = $sth->fetch(PDO::FETCH_ASSOC);

		$dbh = null;
		
		$url = $item['page_url'];
	}
	
	if(substr($url, 0,1) == '/') {
		$url = substr($url, 1,strlen($url));
	}
	//$check_page = $url;
	
	$check_page = $fc_base_url.$url;
	
	
	echo '<div class="alert alert-info">checking: '.$check_page.'</div>';
	

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
				$url_extension = substr($link, -$extension_length);
				
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
	
	return $links;
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
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 110);
	curl_setopt($ch, CURLOPT_TIMEOUT, 110);
	curl_setopt($ch, CURLOPT_FAILONERROR, FALSE);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, TRUE);
	curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	curl_setopt($ch, CURLOPT_ENCODING,  '');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,TRUE);
	
	curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
	
	curl_setopt($handle, CURLOPT_PROXY, "127.0.0.1");
	curl_setopt($handle, CURLOPT_PROXYPORT, 8888);

  $data = curl_exec($ch);
  $info = curl_getinfo($ch);
 
  curl_close($ch);
 
  return $data;
}



/**
 * get al the links from $html	
 */

function fc_get_links($html) { 

    // Create a new DOM Document to hold our webpage structure 
    $xml = new DOMDocument(); 

    // Load the url's contents into the DOM 
    $xml->loadHTML($html); 

    // Empty array to hold all links to return 
    $links = array(); 

    //Loop through each <a> tag in the dom and add it to the link array 
    foreach($xml->getElementsByTagName('a') as $link) { 
        $links[] = array('url' => $link->getAttribute('href'), 'text' => $link->nodeValue); 
    } 

    //Return the links 
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
	$time = time();

	$dbh = new PDO("sqlite:".INDEX_DB);
	
	$sql = "select page_url from pages where page_id = :id";
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':id', $id, PDO::PARAM_STR);
	$sth->execute();
	$item = $sth->fetch(PDO::FETCH_ASSOC);
	
	$dbh = null;
	
	$url = $item['page_url'];
	
	if(substr($url, 0,1) == '/') {
		$url = substr($url, 1,strlen($url));
	}
	
	$check_page = $fc_base_url.$url;
	$get_html = fc_loadSourceCode($check_page);
	
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
	
	
	return $return;

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
	
	$dbh = null;
	
	if($cnt_entries[0] < 1) {
		
		$url = htmlentities($url);		
		$dbh = new PDO("sqlite:".INDEX_DB);
		
		$sql_insert = "INSERT INTO pages (
				page_id, page_url
				) VALUES (
				:page_id, :url ) ";
		
		try {
			$sth = $dbh->prepare($sql_insert);
			$sth->bindParam(':url', $url, PDO::PARAM_STR);
			$sth->bindParam(':page_id', md5($url), PDO::PARAM_STR);
			$add = $sth->execute();
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
		$dbh = null;
	}
	
	
}




/**
 * get entries from pages table
 */

function fc_get_indexed_pages() {
	$dbh = new PDO("sqlite:".INDEX_DB);
	$sql = "SELECT * FROM pages";

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