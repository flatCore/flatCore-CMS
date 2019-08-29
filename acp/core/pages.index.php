<?php

//prohibit unauthorized access
require 'core/access.php';

$status_msg = '';

/* delete items from excludes list */
if(isset($_GET['del_exclude']) && is_numeric($_GET['del_exclude'])) {
	fc_delete_excludes($_GET['del_exclude']);
}

if(isset($_POST['add_exclude_url'])) {
	fc_write_exclude_url($_POST['exclude_url']);
}

if(isset($_POST['add_excludes'])) {
	fc_write_exclude_elements($_POST['exclude_element'],$_POST['exclude_attribute']);
}


$exclude_items = fc_get_exclude_elements();
$exclude_urls = fc_get_exclude_urls();


echo '<div class="app-container">';

echo '<div class="subHeader">';
echo '<h3>Indexing</h3>';
echo '</div>';



echo '<div class="max-height-container">';
echo '<div class="row">';
echo '<div class="col-sm-8">';


if(isset($_GET['a']) && $_GET['a'] == 'start') {
	if(empty($_GET['id'])) {
		fc_crawler();
	} else {
		fc_crawler($_GET['id']);
	}	
}

if(isset($_GET['a']) && $_GET['a'] == 'update') {
	$fc_upi = fc_update_page_index($_GET['id']);
	$status_msg = 'Script running '.$fc_upi['duration'].' seconds';
}

if(isset($_GET['a']) && $_GET['a'] == 'update_bulk') {
	fc_update_bulk_page_index();
}





$indexed_entries = fc_get_indexed_pages();
$cnt_indexed_entries = count($indexed_entries);

echo '<fieldset>';
echo '<legend>' . $lang['page_index'] . ' ('.$cnt_indexed_entries.')</legend>';
echo '<div class="scroll-box">';
echo '<div class="p-3">';

if($status_msg != '') {
	echo '<div class="alert alert-info alert-dismissible fade show autoclose">'.$status_msg.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>
  </button></div>';
}


$item_tpl = file_get_contents('templates/list-indexed-pages-item.tpl');

	$page_img_title_errors = 0;
	$page_img_alt_errors = 0;
	$page_img_file_not_found = 0;
	$page_link_title_errors = 0;
	
	$cnt_meta_errors = 0;
		
	$cnt_error_meta_description = 0;
	$cnt_error_meta_title = 0;
	$cnt_error_img_alt = 0;
	$cnt_error_img_title = 0;
	$cnt_images_errors = 0;
	$cnt_error_link_title = 0;
	$cnt_headlines = 0;
	$cnt_error_h1 = 0;
	$cnt_error_h2 = 0;

for($i=0;$i<$cnt_indexed_entries;$i++) {

	
	$url = $indexed_entries[$i]['page_url'];
	$title = $indexed_entries[$i]['page_title'];
	$description = $indexed_entries[$i]['page_description'];
	$indexed_time = $indexed_entries[$i]['indexed_time'];
	
	if($title == '') {
		$title = '<span class="text-danger">No Title</span>';
		$cnt_error_meta_title++;
	}
	if($description == '') {
		$description = '<span class="text-danger">No Description</span>';
		$cnt_error_meta_description++;
	}
	
	$cnt_meta_errors = $cnt_error_meta_description+$cnt_error_meta_title;
	
	if($indexed_time == '') {
		$indexed_time = '<span class="text-danger">Not Indexed</span>';
	} else {
		$indexed_time = date('Y-m-d H:i',$indexed_time);
	}
	
	
	/**
	 * headlines
	 */
	 
	/* h1 */
	$h1s = explode('<|>', $indexed_entries[$i]['page_h1']);
	$cnt_h1s = count($h1s);
	if($cnt_h1s > 0) {
		$h1str = '<ul>';
		foreach($h1s as $str) {
			$h1str .= '<li>'.$str.'</li>';
		}
		$h1str .= '</ul>';
	} else {
		$h1str = '<span class="text-danger strong">'.$lang['msg_missing_headline'].'</span>';
		$cnt_error_h1++;
	}
	
	/* h2 */
	$h2s = explode('<|>', $indexed_entries[$i]['page_h2']);
	$cnt_h2s = count($h2s);
	if($cnt_h2s > 0) {
		$h2str = '<ul>';
		foreach($h2s as $str) {
			$h2str .= '<li>'.$str.'</li>';
		}
		$h2str .= '</ul>';
	} else {
		$h2str = '<span class="text-danger">'.$lang['msg_missing_headline'].'</span>';
		$cnt_error_h2++;
	}
	
	/* h3 */
	$h3s = explode('<|>', $indexed_entries[$i]['page_h3']);
	$cnt_h3s = count($h3s);
	if($cnt_h3s > 0) {
		$h3str = '';
		foreach($h3s as $str) {
			$h3str .= '<span class="badge badge-secondary">'.$str.'</span> ';
		}
	} else {
		$h3str = $lang['msg_missing_headline'];
	}
	
	$cnt_headlines = $cnt_h1s+$cnt_h2s;
	
	$headlines_str = '<table class="table table-sm table-striped">';
	$headlines_str .= '<tr><td>H1 ('.$cnt_h1s.')</td><td>'.$h1str.'</td></tr>';
	$headlines_str .= '<tr><td>H2 ('.$cnt_h2s.')</td><td>'.$h2str.'</td></tr>';
	$headlines_str .= '<tr><td>H3 ('.$cnt_h3s.')</td><td>'.$h3str.'</td></tr>';
	$headlines_str .= '</table>';
	
	
	
	
	/* images */
	$img_str = '';
	$img_array = array_filter(explode('<|-|>', $indexed_entries[$i]['page_images']));
	$cnt_images = count($img_array);
	if(is_array($img_array) && count($img_array) > 0) {
		$img_str = '<table class="table table-sm">';
		$img_str .= '<tr><td>SRC</td><td>ALT</td><td>TITLE</td></tr>';
		foreach($img_array as $k) {
			$missing_img = false;
			$this_img = explode('<|>', $k);
			$img_str .= '<tr>';
			
			if(substr($this_img[0], 0,4) == 'http') {
				$file_headers = @get_headers($remote_filename);
				if (stripos($file_headers[0],"404 Not Found") > 0  || (stripos($file_headers[0], "302 Found") > 0 && stripos($file_headers[7],"404 Not Found") > 0)) {
					$img_str .= '<td>'.$this_img[0].' <span class="text-danger">(extern file not found)</span></td>';
					$missing_img = true;
					$page_img_file_not_found++;
				} else {
					$img_str .= '<td><strong><a href="'.$this_img[0].'" data-fancybox="images">'.basename($this_img[0]).'</a></strong></td>';
				}
			} else {
				if(!is_file('../'.FC_ROOT.$this_img[0])) {
					$img_str .= '<td>'.FC_ROOT.$this_img[0].' <span class="text-danger">(file not found)</span></td>';
					$missing_img = true;
					$page_img_file_not_found++;
				} else {
					$img_str .= '<td><strong><a href="'.$this_img[0].'" data-fancybox="images">'.basename($this_img[0]).'</a></strong></td>';
				}
			}
			
			if($this_img[1] == '' && $missing_img === false) {
				$this_img[1] = '<span class="text-danger">NULL</span>';
				$cnt_error_img_alt++;
				$page_img_alt_errors++;
			}
	
			if($this_img[2] == '' && $missing_img === false) {
				$this_img[2] = '<span class="text-danger">NULL</span>';
				$cnt_error_img_title++;
				$page_img_title_errors++;
			}
			
			$img_str .= '<td>'.$this_img[1].'</td>';
			$img_str .= '<td>'.$this_img[2].'</td>';
			$img_str .= '</tr>';
		}
		$img_str .= '</table>';
		
		$img_rerror_str = '<span class="text-danger">Errors:</span> ';
		$img_rerror_str .= 'Title: '.$page_img_title_errors.' ';
		$img_rerror_str .= 'Alt: '.$page_img_alt_errors. ' ';
		$img_rerror_str .= 'Not found: '.$page_img_file_not_found;
		
		$cnt_images_errors = $page_img_title_errors+$page_img_alt_errors+$page_img_file_not_found;
		
	}
	
	
	/* links */
	$link_str = '';
	$link_array = array_filter(explode('<|-|>', $indexed_entries[$i]['page_links']));
	$link_cnt = count($link_array);
	if(is_array($link_array) && count($link_array) > 0) {
		$link_str = '<table class="table table-sm">';
		$link_str .= '<tr><td>href</td><td>Text</td><td>Title</td></tr>';
		
		$maxChars = 25;
		
		foreach($link_array as $k) {
			$missing_title = false;
			$this_link = explode('<|>', $k);
			
			$link_text = $this_link[2];
			$textLength = strlen($this_link[2]);
			if($textLength > $maxChars) {
				$link_text = substr_replace($this_link[2], ' ... ', $maxChars/2, $textLength-$maxChars);
			}
			
			$link_str .= '<tr>';
			$link_str .= '<td><a href="'.$this_link[2].'" title="'.$this_link[2].'" class="">'.$link_text.'</a></td>';

			if($this_link[1] == '') {
				$this_link[1] = '<span class="text-danger">NULL</span>';
				$cnt_error_link_title++;
				$page_link_title_errors++;
			}			
	
			if($this_link[2] == '') {
				$this_link[2] = '<span class="text-danger">NULL</span>';
			}
			
			$link_str .= '<td>'.$this_link[0].'</td>';
			$link_str .= '<td>'.$this_link[1].'</td>';
			$link_str .= '</tr>';
		}
		$link_str .= '</table>';
		
		$link_errors_str = '<span class="text-danger">Errors:</span> Title: '.$page_link_title_errors.'<br>';
	}	
	
	
	
	
	$tpl = $item_tpl;
	$tpl = str_replace("{id}", $indexed_entries[$i]['page_id'], $tpl);
	$tpl = str_replace("{url}", $url, $tpl);
	$tpl = str_replace("{title}", $title, $tpl);
	$tpl = str_replace("{description}", $description, $tpl);
	$tpl = str_replace("{indexed_time}", $indexed_time, $tpl);
	$tpl = str_replace("{btn_update_info}", $icon['sync_alt'], $tpl);
	$tpl = str_replace("{btn_show_info}", $icon['info_circle'], $tpl);
	$tpl = str_replace("{btn_start_index}", $icon['sitemap'], $tpl);

	$tpl = str_replace('{meta_str}', $meta_str, $tpl);
	$tpl = str_replace('{cnt_meta_errors}', $cnt_meta_errors, $tpl);
	
	$tpl = str_replace('{cnt_links}', $link_cnt, $tpl);
	$tpl = str_replace('{cnt_links_errors}', $page_link_title_errors, $tpl);
	$tpl = str_replace('{link_str}', $link_str, $tpl);
	
	$tpl = str_replace('{headline_str}', $headlines_str, $tpl);
	$tpl = str_replace('{cnt_headlines}', $cnt_headlines, $tpl);
	$tpl = str_replace('{cnt_headline_errors}', $cnt_error_h1, $tpl);
		
	$tpl = str_replace('{cnt_images}', $cnt_images, $tpl);
	$tpl = str_replace('{cnt_images_errors}', $cnt_images_errors, $tpl);
	$tpl = str_replace('{images_str}', $img_str, $tpl);
	
	echo $tpl;
}



echo '</div>';
echo '</div>';
echo '</fieldset>';

echo '</div>';
echo '<div class="col-sm-4">';



/**
 * preferences for page index

 */

echo '<fieldset>';
echo '<legend>' . $lang['tab_page_preferences'] . '</legend>';
echo '<div class="scroll-box">';
echo '<div class="p-3">';

echo '<h5>Start</h5>';
echo '<div class="row">';
echo '<div class="col-8">';
echo '<pre>'.$fc_base_url.'</pre>';
echo '</div>';
echo '<div class="col-4">';
echo '<a href="acp.php?tn=pages&sub=index&a=start" class="btn btn-save btn-block">'.$icon['sitemap'].' Start Index</a>';
echo '</div>';
echo '</div>';

echo '<a href="acp.php?tn=pages&sub=index&a=update_bulk" class="btn btn-save btn-block">'.$icon['sync_alt'].' Bulk update</a>';

echo '<hr>';

echo '<div class="well well-sm">';
echo '<table class="table table-sm">';
echo '<tr><td class="text-right">'.$cnt_error_img_alt.'</td><td>'.$lang['label_missing_img_alt_tags'].'</td></tr>';
echo '<tr><td class="text-right">'.$cnt_error_img_title.'</td><td>'.$lang['label_missing_img_title_tags'].'</td></tr>';
echo '<tr><td class="text-right">'.$cnt_error_link_title.'</td><td>'.$lang['label_missing_link_title_tags'].'</td></tr>';
echo '<tr><td class="text-right">'.$cnt_error_h1.'</td><td>'.$lang['label_missing_h1'].'</td></tr>';
echo '<tr><td class="text-right">'.$cnt_error_h2.'</td><td>'.$lang['label_missing_h2'].'</td></tr>';
echo '</table>';
echo '</div>';

echo '<h5>Exclude elements</h5>';

echo '<table class="table table-sm">';
foreach($exclude_items as $ex_item) {
	echo '<tr>';
	echo '<td><code>'.$ex_item['item_element'].'</code></td>';
	echo '<td><code>'.$ex_item['item_attributes'].'</code></td>';
	echo '<td class="text-right"><a href="acp.php?tn=pages&sub=index&del_exclude='.$ex_item['item_id'].'" class="btn btn-danger btn-sm">'.$icon['trash_alt'].'</a></td>';
	echo '<tr>';
}
echo '</table>';


/* form for exclude elements */

echo '<form action="acp.php?tn=pages&sub=index" method="POST">';
echo '<div class="row">';
echo '<div class="col-4">';
echo '<div class="form-group">';
echo '<label for="elements">Element</label>';
echo '<input type="text" class="form-control" name="exclude_element" id="elements" placeholder="div, span, footer ...">';
echo '</div>';
echo '</div>';
echo '<div class="col-4">';
echo '<div class="form-group">';
echo '<label for="attribute">ID/Class</label>';
echo '<input type="text" class="form-control" name="exclude_attribute" id="attribute" placeholder="#id, .class">';
echo '</div>';
echo '</div>';
echo '<div class="col-4">';
echo '<div class="form-group">';
echo '<label for="attribute"><br></label>';
echo '<input type="submit" name="add_excludes" value="'.$lang['save'].'" class="btn btn-save btn-block">';
echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</form><hr>';



echo '<h5>Exclude URLs</h5>';





echo '<table class="table table-sm">';
foreach($exclude_urls as $ex_url) {
	echo '<tr>';
	echo '<td><code>'.$ex_url['item_url'].'</code></td>';
	echo '<td class="text-right"><a href="acp.php?tn=pages&sub=index&del_exclude='.$ex_url['item_id'].'" class="btn btn-danger btn-sm">'.$icon['trash_alt'].'</a></td>';
	echo '<tr>';
}
echo '</table>';


/* form for exclude urls */

echo '<form action="acp.php?tn=pages&sub=index" method="POST">';
echo '<div class="row">';
echo '<div class="col-8">';
echo '<div class="form-group">';
echo '<label for="excludeUrls">URL</label>';
echo '<input type="text" name="exclude_url" class="form-control" id="excludeUrls" placeholder="/search/">';
echo '</div>';
echo '</div>';
echo '<div class="col-4">';
echo '<div class="form-group">';
echo '<label for="attribute"><br></label>';
echo '<input type="submit" name="add_exclude_url" value="'.$lang['save'].'" class="btn btn-save btn-block">';
echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</form>';


echo '</div>';
echo '</div>';
echo '</fieldset>';


echo '</div>';
echo '</div>';



echo '</div>'; // .max-height-container
echo '</div>'; // .app-container

?>
