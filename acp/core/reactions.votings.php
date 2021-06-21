<?php
//error_reporting(E_ALL ^E_NOTICE);
//prohibit unauthorized access
require 'core/access.php';

if($_POST['delete_reactions']) {
	
	$delete = $db_content->delete("fc_comments", [
		"AND" => [
			"comment_id" => $_POST['bulk_delete'],
			"comment_type" => ["upv","dnv"]
		]
	]);
	
	echo '<div class="alert alert-info">';
	echo 'Deleted Data ('.$delete->rowCount().')';
	echo '</div>';
	
}

/* get all post ids and titles */
$get_posts = $db_posts->select("fc_posts", ["post_id","post_title"],[
	"ORDER" => ["post_id" => "DESC"]
	]);

foreach($get_posts as $posts) {
	$temp_post[$posts['post_id']] = $posts['post_title'];	
}

$get_votes = $db_content->select("fc_comments", "*",[
	"OR" => [
		"comment_type" => ["upv","dnv"]
	]
]);


if($_POST['filter_by_post'] == 'all') {
	unset($_SESSION['filter_by_post']);
}

if(isset($_POST['filter_by_post']) && is_numeric($_POST['filter_by_post'])) {
	$_SESSION['filter_by_post'] = (int) $_POST['filter_by_post'];
}

if($_SESSION['filter_by_post'] != '') {
	$get_votes = $db_content->select("fc_comments", "*",[
		"AND" => [
			"OR" => [
				"comment_type" => ["upv","dnv"]
			],
			"comment_relation_id" => $_SESSION['filter_by_post']
		]
	]);
	
	$get_voting_data = fc_get_voting_data('post',$_SESSION['filter_by_post']);
}

$cnt_get_votes = count($get_votes);

if(is_array($get_voting_data)) {
	$filter_data = '<span class="badge bg-secondary">'.$get_voting_data['all'].'</span> <span class="badge bg-success">'.$icon['thumbs_up'].' '.$get_voting_data['upv'].'</span> <span class="badge bg-danger"> '.$icon['thumbs_down'].' '.$get_voting_data['dnv'].'</span>';
}

echo '<div class="subHeader">';
echo $lang['label_votings'] .' '. $filter_data;
echo '</div>';

echo '<div class="app-container">';
echo '<div class="max-height-container">';

echo '<div class="row">';
echo '<div class="col-md-9">';


echo '<div class="card p-3">';
echo '<form action="?tn=reactions&sub=votings" method="POST">';

echo '<div class="position-absolute top-0 end-0 p-3">';
echo '<input type="submit" name="delete_reactions" value="'.$lang['delete_selected'].'" class="btn btn-danger">';
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</div>';

echo '<div class="scroll-box">';
echo '<table class="table table-sm">';



for($i=0;$i<$cnt_get_votes;$i++) {
	
	echo '<tr>';
	
	echo '<td><input class="form-check-input" type="checkbox" name="bulk_delete[]" value="'.$get_votes[$i]['comment_id'].'"></td>';
	
	echo '<td>'.$get_votes[$i]['comment_id'].'</td>';
	echo '<td>'.date('Y-m-d H:i',$get_votes[$i]['comment_time']).'</td>';
	
	if($get_votes[$i]['comment_type'] == 'upv') {
		$show_vote = $icon['thumbs_up'];
	} else {
		$show_vote = '<span class="text-danger">'.$icon['thumbs_down'].'</span>';
	}
	
	echo '<td>'.$show_vote.'</td>';
	
	if(strlen($get_votes[$i]['comment_author']) == 32 && ctype_xdigit($get_votes[$i]['comment_author'])) {
		$voter = '<i>anonymous</i>';
	} else {
		$voter = $get_votes[$i]['comment_author'];
	}
	
	echo '<td>'.$voter.'</td>';
	
	
	$title = $temp_post[$get_votes[$i]['comment_relation_id']];
	
	echo '<td>'.$title.'</td>';
	
	echo '</tr>';
	
}

echo '</table>';
echo '</div>'; // scroll-box

echo '</form>';
echo '</div>'; // card

echo '</div>';
echo '<div class="col-md-3">';

echo '<div class="card p-3">';

echo '<form action="?tn=reactions&sub=votings'.$comment_id.'" method="POST">';
echo '<div class="form-group">';
echo '<label>'.$lang['label_filter'].'</label>';
echo '<select name="filter_by_post" class="custom-select form-control" onchange="this.form.submit()">';
echo '<option value="all">'.$lang['label_show_all_votings'].'</option>';
foreach($get_posts as $posts) {
	
	$sel = '';
	if($_SESSION['filter_by_post'] == $posts['post_id']) {
		$sel = 'selected';
	}
	
	echo '<option value="'.$posts['post_id'].'" '.$sel.'>'.$posts['post_title'].'</option>';
}
echo '<select>';
echo '</div>';
echo '</form>';

echo '</div>'; // card

echo '</div>';
echo '</div>';

echo '</div>'; // max-height-container
echo '</div>'; // app-container

?>