<?php

session_start();
error_reporting(0);

require("../../config.php");

define("CONTENT_DB", "../../$fc_db_content");
define("USER_DB", "../$fc_db_user");
define("STATS_DB", "../$fc_db_stats");
define("FC_ROOT", str_replace("/acp","",FC_INC_DIR));
define("IMAGES_FOLDER", "$img_path");
define("FILES_FOLDER", "$files_path");

require_once('access.php');
require_once('functions.php');
require_once('database.php');
require('../../lib/lang/'.$_SESSION['lang'].'/dict-backend.php');

if(isset($_REQUEST['pid'])) {
	$page_id = (int) $_REQUEST['pid'];
	$comments_id = 'p'.$page_id;
}

/* delete comment */
if(isset($_REQUEST['did'])) {
	$delete_id = (int) $_REQUEST['did'];
	$sql_del_entry = "DELETE FROM fc_comments WHERE comment_id = $delete_id";
	$dbh = new PDO("sqlite:".CONTENT_DB);
	$cnt_del_comments = $dbh->exec($sql_del_entry);
	$dbh = null;
}

if(isset($_GET['cid'])) {
	$get_comment = fc_get_comment($_GET['cid']);
	$e_comment_text = $get_comment['comment_text'];
	$e_comment_id = $get_comment['comment_id'];
	if($_SESSION['user_nick'] != $get_comment['comment_author']) {
		//you can't edit others entries
		unset($e_comment_text,$e_comment_id);
	}
}


$comment_entry_tpl = file_get_contents('../templates/comment-entry.tpl');
$chat_form = file_get_contents('../templates/comment-form.tpl');
$chat_form = str_replace('{form_action}', "#", $chat_form);
$chat_form = str_replace('{value_textarea}', "$e_comment_text", $chat_form);
$chat_form = str_replace('{value_send_btn}', $lang['save'], $chat_form);
$chat_form = str_replace('{value_hidden_id}', "$e_comment_id", $chat_form);
$chat_form = str_replace('{value_hidden_parent_id}', "$page_id", $chat_form);
$chat_form = str_replace('{form_legend}', $lang['label_comment'], $chat_form);

$comment_entry_tpl = file_get_contents('../templates/comment-entry.tpl');

echo '<div class="container">' .$chat_form . '</div>';

if(isset($_POST['comment'])) {
	if(is_numeric($_POST['id'])) {
		fc_write_comment($_SESSION['user_nick'], $_POST['comment'], "$comments_id", $_POST['id']);
	} else {
		fc_write_comment($_SESSION['user_nick'], $_POST['comment'], "$comments_id");
	}
}


$comments = fc_get_comments($comments_id);
$cnt_comment = count($comments);

echo '<div class="container">';

for($i=0;$i<$cnt_comment;$i++) {

	$comment_time = date("d.m.Y H:i:s", $comments[$i]['comment_time']);
	$comment_author = $comments[$i]['comment_author'];
	$comment_text = nl2br($comments[$i]['comment_text']);
	$comment_id = $comments[$i]['comment_id'];
	
	$author_avatar_path = '../../'. FC_CONTENT_DIR . '/avatars/' . md5($comment_author) . '.png';
	$author_avatar = '<img src="images/avatar.png" class="img-circle avatar" width="64" height="64">';
	if(is_file("$author_avatar_path")) {
		$author_avatar = '<img src="'.$author_avatar_path.'" class="img-circle avatar" width="64" height="64">';
	}
	
	unset($show_entry);
	
	if($_SESSION['user_nick'] == $comment_author) {
		$show_entry = str_replace('{entry_edit_btn}', '<a class="btn btn-primary btn-xs fancybox" data-fancybox-type="ajax" href="/acp/core/ajax.comments.php?pid='.$page_id.'&cid='.$comment_id.'">'.$lang['edit'].'</a>', $comment_entry_tpl);
		$show_entry = str_replace('{entry_delete_btn}', '<a class="btn btn-danger btn-xs fancybox" data-fancybox-type="ajax" href="/acp/core/ajax.comments.php?pid='.$page_id.'&did='.$comment_id.'">'.$lang['delete'].'</a>', $show_entry);
	} else {
		$show_entry = str_replace('{entry_edit_btn}', '', $comment_entry_tpl);
		$show_entry = str_replace('{entry_delete_btn}', '', $show_entry);
	}
	$show_entry = str_replace('{comment_avatar}', $author_avatar, $show_entry);
	$show_entry = str_replace('{comment_author}', $comment_author, $show_entry);
	$show_entry = str_replace('{comment_time}', $comment_time, $show_entry);
	$show_entry = str_replace('{comment_text}', $comment_text, $show_entry);
	
	echo $show_entry;
}


?>

<script>
$(document).ready(function(){
  $("#comment_form").bind("submit", function() {
      $.ajax({
          type : "POST",
          cache : false,
          url: "../acp/core/ajax.comments.php",
          data: $(this).serializeArray(),
          success:function(data){
              $.fancybox(data);
          }
      });
      return false;
	});
	$('.fancybox').fancybox({
			minWidth: '50%',
			height: '90%'
		});
});

</script>