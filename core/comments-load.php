<?php
session_start();
error_reporting(0);
use Medoo\Medoo;


define("FC_SOURCE", "frontend");
require '../config.php';

require FC_CORE_DIR.'/database.php';

$start = 0;
$limit = 100;

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
$cnt_comment = count($comments);

echo $lang['comments_title']. $cnt_comment;

$lookup_table = [];
foreach ($comments as $comment_key => $comment) {
	if($comment['comment_parent_id'] == '') {
		$comment['comment_parent_id'] = 0;
	}
  $lookup_table[$comment['comment_parent_id']][$comment_key] = $comment['comment_id'];
}


function recursive_child_display($comments, $lookup_table, $root = 0, $level = 0) {
	
	global $comment_tpl;
	
    if (isset($lookup_table[$root])) {
        foreach ($lookup_table[$root] as $key => $comment_id) {
	        
	        
	        $padding = (int) (20*$level);
	        if(!is_numeric($padding)) {
		        $padding = 0;
	        }
            
            $comment_time = date('d.m.Y H:i',$comments[$key]['comment_time']);
            $comment_avatar = '/styles/default/images/avatar.jpg';
            $comment_avatar_img = '<img src="'.$comment_avatar.'" class="img-avatar img-fluid rounded-circle" alt="" title="'.$comments[$key]['comment_author'].'">';
						$this_comment = $comment_tpl;

						$this_comment = str_replace('{comment_author}', $comments[$key]['comment_author'], $this_comment);
						$this_comment = str_replace('{comment_text}', $comments[$key]['comment_text'], $this_comment);
						$this_comment = str_replace('{comment_time}', $comment_time, $this_comment);
						$this_comment = str_replace('{comment_avatar}', $comment_avatar_img, $this_comment);
						$this_comment = str_replace('{comment_id}', $comments[$key]['comment_id'], $this_comment);
						$a_url = '?cid='.$comments[$key]['comment_id'].'#comment-form';
						$this_comment = str_replace('{url_answer_comment}', $a_url, $this_comment);
						$this_comment = str_replace('{level}', $level, $this_comment);
						
						echo '<div class="comment-level comment-level-'.$level.'">';
						echo $this_comment;
           
            recursive_child_display($comments, $lookup_table, $comment_id, $level+1);
            echo '</div>';

        }
    }
}

recursive_child_display($comments, $lookup_table, 0);


?>