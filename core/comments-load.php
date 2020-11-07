<?php

session_start();

use Medoo\Medoo;



define("FC_SOURCE", "frontend");
require '../config.php';

require FC_CORE_DIR.'/database.php';

$start = 0;
$limit = 10;


if(is_numeric($_GET['page_id'])) {
	$filter['relation_id'] = (int) $_GET['page_id'];
	$filter['type'] = 'p';
}

if(is_numeric($_GET['post_id'])) {
	$filter['relation_id'] = (int) $_GET['post_id'];
	$filter['type'] = 'b';
}


$theme = $db_content->get("fc_preferences", "prefs_template", [
	"prefs_status" => "active"
]);

if(is_file(FC_CORE_DIR.'/styles/'.$theme.'/templates/comments/comment_entry.tpl')){
	$comment_tpl = file_get_contents(FC_CORE_DIR.'/styles/'.$theme.'/templates/comments/comment_entry.tpl');
} else {
	$comment_tpl = file_get_contents(FC_CORE_DIR.'/styles/default/templates/comments/comment_entry.tpl');
}

$comments = fc_get_comments($start,$limit,$filter);

foreach ($comments as $comment) {
	
	$comment_time = date('d.m.Y H:i',$comment['comment_time']);
	/* default avatar image */
	$comment_avatar = '/styles/default/images/avatar.jpg';
	
	$comment_avatar_img = '<img src="'.$comment_avatar.'" class="img-avatar img-fluid rounded-circle" alt="" title="'.$comment['comment_author'].'">';
	
	$this_comment = $comment_tpl;
	$this_comment = str_replace('{comment_author}', $comment['comment_author'], $this_comment);
	$this_comment = str_replace('{comment_text}', $comment['comment_text'], $this_comment);
	$this_comment = str_replace('{comment_time}', $comment_time, $this_comment);
	$this_comment = str_replace('{comment_avatar}', $comment_avatar_img, $this_comment);
	$this_comment = str_replace('{comment_id}', $comment['comment_id'], $this_comment);
	
	echo $this_comment;
}

?>