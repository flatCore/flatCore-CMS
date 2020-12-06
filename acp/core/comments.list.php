<?php
//error_reporting(E_ALL ^E_NOTICE);
//prohibit unauthorized access
require 'core/access.php';


/* update comment */
$update_msg = '';
if(isset($_POST['update_comment'])) {
	
	$update_id = (int) $_POST['update_comment'];
	$lastedit = time();
	$lastedit_from = $_SESSION['user_nick'];
	
	$update = $db_content->update("fc_comments", [
		"comment_author" => $_POST['comment_author'],
		"comment_author_mail" => $_POST['comment_author_mail'],
		"comment_text" => $_POST['comment_text'],
		"comment_lastedit" => $lastedit,
		"comment_lastedit_from" => $lastedit_from
	],[
		"comment_id" => $update_id
	]);
	
	$update_msg = '<div class="alert alert-success">'.$update_id.'</div>';
	
	$editid = $update_id;
	
}


/* delete comment */
if(isset($_POST['delid'])) {
	$delete_id = (int) $_POST['delid'];
	$delete = $db_content->delete("fc_comments", [
		"comment_id" => $delete_id
	]);
	
	if($delete->rowCount() > 0) {
		echo '<div class="alert alert-success">'.$lang['msg_entry_delete'].'</div>';
	}
}


/* change status of comment */
if(isset($_POST['change_status'])) {
	
	$get_status = $db_content->get("fc_comments", "comment_status", [
	"comment_id" => $_POST['change_status']
	]);
	
	$set_status = 2;
	if($get_status == 2) {
		$set_status = 1;
	}
	
	$update = $db_content->update("fc_comments", [
		"comment_status" => $set_status
	],[
		"comment_id" => $_POST['change_status']
	]);
	
}



/**
 * build array for all pages with comments
 * return array $cpages[page_id] => page_title
 */
 
$page_comments = $db_content->select("fc_comments", "comment_relation_id",[
				"AND" => [
				"comment_type" => "p"
			]
]);

$page_comment_ids = array_values(array_unique($page_comments));
$all_pages = $db_content->select("fc_pages", ["page_id","page_title"]);

foreach($page_comment_ids as $id) {
	$key = array_search($id, array_column($all_pages, 'page_id'));
	$this_page_id = $all_pages[$key]['page_id'];
	$this_page_title = $all_pages[$key]['page_title'];
	$cpages[$this_page_id] = $this_page_title;
}

/**
 * build array for all posts with comments
 * return array $cpposts[posts_id] => post_title
 */

$blog_comments = $db_content->select("fc_comments", "comment_relation_id",[
				"AND" => [
				"comment_type" => "b"
			]
]);

$blog_comment_ids = array_values(array_unique($blog_comments));
$all_posts = $db_content->select("fc_posts", ["post_id","post_title"]);

foreach($blog_comment_ids as $id) {	
	$key = array_search($id, array_column($all_posts, 'post_id'));
	$this_post_id = $all_posts[$key]['post_id'];
	$this_post_title = $all_posts[$key]['post_title'];
	$cposts[$this_post_id] = $this_post_title;
}




// defaults
$comments_start = 0;
$comments_limit = 100;
$comments_order = 'id';
$comments_direction = 'DESC';
$comments_filter = array();

if($_SESSION['cf_status'] == '') {
	$_SESSION['cf_status'] = 'all';
}

if($_SESSION['cf_relation_id'] == '') {
	$_SESSION['cf_relation_id'] = 'all';
}
if($_SESSION['cf_type'] == '') {
	$_SESSION['cf_type'] = 'all';
}


if(isset($_POST['filter_by_status'])) {
	$_SESSION['cf_status'] = $_POST['filter_by_status'];
}

if(isset($_POST['filter_by_page_id'])) {
	$_SESSION['cf_relation_id'] = $_POST['filter_by_page_id'];
	$_SESSION['cf_type'] = 'p';
}

if(isset($_POST['filter_by_post_id'])) {
	$_SESSION['cf_relation_id'] = $_POST['filter_by_post_id'];
	$_SESSION['cf_type'] = 'b';
}


$cf_filter['relation_id'] = $_SESSION['cf_relation_id'];
$cf_filter['type'] = $_SESSION['cf_type'];
$cf_filter['status'] = $_SESSION['cf_status'];


$get_comments = fc_get_comments($comments_start,$comments_limit,$cf_filter);
$cnt_comments = count($get_comments);

if(isset($_POST['editid'])) {
	$editid = (int) $_POST['editid'];
}


echo '<div class="row">';
echo '<div class="col-md-9">';

if($cnt_comments < 1) {
	echo '<div class="alert alert-info">'.$lang['msg_no_entries_so_far'].'</div>';
}


for($i=0;$i<$cnt_comments;$i++) {
	
	$comment_time = date('d.m.Y H:i:s', $get_comments[$i]['comment_time']);
	$comment_id = $get_comments[$i]['comment_id'];
	$comment_relation_id = $get_comments[$i]['comment_relation_id'];
	$comment_status = $get_comments[$i]['comment_status'];
		
	echo '<div class="card mb-1" id="comid'.$comment_id.'">';
	echo '<div class="card-body">';
	echo '<h5 class="card-title"><span class="badge">#'.$comment_id.'</span> '.$get_comments[$i]['comment_author'].' ['.$get_comments[$i]['comment_author_mail'].'] <small>'.$comment_time.'</small></h5>';
	echo '<p class="card-text">'.$get_comments[$i]['comment_text'].'</p>';
	
	echo '<div class="row">';
	echo '<div class="col-md-8">';
	if($get_comments[$i]['comment_type'] == 'p') {
		echo 'PAGE: '.$cpages[$comment_relation_id];
	}
	
	if($get_comments[$i]['comment_type'] == 'b') {
		echo 'POST: '.$cposts[$comment_relation_id];
	}
	
	echo '</div>';
	echo '<div class="col-md-2">';
	
	echo '<form class="form-inline" action="?tn=comments&sub=list#comid'.$comment_id.'" method="POST">';
	echo '<button type="submit" class="btn btn-sm btn-block btn-fc" name="editid" value="'.$comment_id.'">'.$lang['edit'].'</button>';
	echo '</form>';
	echo '</div>';
	echo '<div class="col-md-1">';
	$btn_class = 'btn-success';
	if($comment_status == 1) {
		$btn_class = 'btn-fc';
	}
	echo '<form class="form-inline" action="?tn=comments&sub=list" method="POST">';
	echo '<button type="submit" class="btn btn-sm btn-block '.$btn_class.'" name="change_status" value="'.$comment_id.'">'.$icon['check'].'</button>';
	echo '</form>';
	echo '</div>';
	echo '<div class="col-md-1">';	
	echo '<form class="form-inline" action="?tn=comments&sub=list" method="POST">';
	echo '<button type="submit" class="btn btn-sm btn-block btn-danger" name="delid" value="'.$comment_id.'">'.$icon['trash_alt'].'</button>';
	echo '</form>';
	echo '</div>';
	echo '</div>';
	
	echo '</div>';
	
	/* edit form */
	if($get_comments[$i]['comment_id'] == $editid) {
		
		if($update_msg != '') {
			echo $update_msg;
		}
				
		echo '<div class="well well-sm p-3 m-3">';
		echo '<form action="?tn=comments&sub=list#comid'.$comment_id.'" method="POST">';
		echo '<div class="form-group">';
		echo '<label>'.$lang['label_name'].'</label>';
		echo '<input type="text" class="form-control" name="comment_author" value="'.$get_comments[$i]['comment_author'].'">';
		echo '</div>';
		echo '<div class="form-group">';
		echo '<label>'.$lang['label_mail'].'</label>';
		echo '<input type="text" class="form-control" name="comment_author_mail" value="'.$get_comments[$i]['comment_author_mail'].'">';
		echo '</div>';
		echo '<div class="form-group">';
		echo '<label>'.$lang['label_comment'].'</label>';
		echo '<textarea class="form-control" name="comment_text" rows="10">'.$get_comments[$i]['comment_text'].'</textarea>';
		echo '</div>';
		echo '<button type="submit" class="btn btn-sm btn-fc" name="update_comment" value="'.$comment_id.'">'.$lang['update'].'</button>';
		echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
		echo '</form>';
		echo '</div>';
	}
	
	
	echo '</div>';
	
}

echo '</div>';
echo '<div class="col-md-3">';

/* sidebar */

/* show select for status */

if($_SESSION['cf_status'] == 'all') {
	$sel_status_all = 'selected';
} else if($_SESSION['cf_status'] == '1') {
	$sel_status_1 = 'selected';
} else {
	$sel_status_2 = 'selected';
}

echo '<fieldset>';
echo '<legend>'.$lang['label_filter_by_status'].'</legend>';
echo '<form action="?tn=comments&sub=list" method="POST">';
echo '<select name="filter_by_status" class="custom-select form-control" onchange="this.form.submit()">';
echo '<option value="all" '.$sel_status_all.'>'.$lang['label_all_comments'].'</option>';
echo '<option value="1" '.$sel_status_1.'>'.$lang['label_comments_status1'].'</option>';
echo '<option value="2" '.$sel_status_2.'>'.$lang['label_comments_status2'].'</option>';
echo '</select>';
echo '</form>';
echo '</fieldset>';


/* show select for pages with comments */
echo '<fieldset>';
echo '<legend>'.$lang['label_filter_comments_by_page'].'</legend>';
echo '<form action="?tn=comments&sub=list" method="POST">';
echo '<select name="filter_by_page_id" class="custom-select form-control" onchange="this.form.submit()">';
echo '<option value="all">'.$lang['label_all_comments'].'</option>';

foreach($cpages as $k => $v) {
	$sel = '';
	if($_SESSION['cf_relation_id'] == $k && $_SESSION['cf_type'] == 'p') {
		$sel = 'selected';
	}
	echo '<option value="'.$k.'" '.$sel.'>'.$v.'</option>';
}

echo '</select>';
echo '</form>';
echo '</fieldset>';


/* show select for posts with comments */
echo '<fieldset>';
echo '<legend>'.$lang['label_filter_comments_by_posts'].'</legend>';
echo '<form action="?tn=comments&sub=list" method="POST">';
echo '<select name="filter_by_post_id" class="custom-select form-control" onchange="this.form.submit()">';
echo '<option value="all">'.$lang['label_all_comments'].'</option>';

foreach($cposts as $k => $v) {
	$sel = '';
	if($_SESSION['cf_relation_id'] == $k && $_SESSION['cf_type'] == 'b') {
		$sel = 'selected';
	}
	echo '<option value="'.$k.'" '.$sel.'>'.$v.'</option>';
}

echo '</select>';
echo '</form>';
echo '</fieldset>';



echo '</div>';
echo '</div>';
?>