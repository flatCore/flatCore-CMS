<?php
//error_reporting(E_ALL ^E_NOTICE);
//prohibit unauthorized access
require 'core/access.php';

echo '<div class="subHeader">';
echo 'Events';
echo '</div>';


if($_POST['delete_reactions']) {
	
	$delete = $db_content->delete("fc_comments", [
		"AND" => [
			"comment_id" => $_POST['bulk_delete'],
			"comment_type" => "evc"
		]
	]);
	
	echo '<div class="alert alert-info">';
	echo 'Deleted Data ('.$delete->rowCount().')';
	echo '</div>';
	
}


/* get all events ids and titles */
$get_events = $db_posts->select("fc_posts", ["post_id","post_title"],[
	"post_type" => "e",
	"ORDER" => ["post_id" => "DESC"]
	]);


foreach($get_events as $event) {
	$temp_event[$event['post_id']] = $event['post_title'];
}

$get_commitments = $db_content->select("fc_comments", "*",[
	"OR" => [
		"comment_type" => "evc",
	],
			"ORDER" => [
			"comment_time" => "DESC"
		]
]);




if($_POST['filter_by_event'] == 'all') {
	unset($_SESSION['filter_by_event']);
}

if(isset($_POST['filter_by_event']) && is_numeric($_POST['filter_by_event'])) {
	$_SESSION['filter_by_event'] = (int) $_POST['filter_by_event'];
}

if($_SESSION['filter_by_event'] != '') {
	$get_commitments = $db_content->select("fc_comments", "*",[
		"AND" => [
			"OR" => [
				"comment_type" => "evc"
			],
			"comment_relation_id" => $_SESSION['filter_by_event']
		]
	]);	
}

$cnt_get_commitments = count($get_commitments);

echo '<div class="app-container">';
echo '<div class="max-height-container">';

echo '<div class="row">';
echo '<div class="col-md-9">';

echo '<div class="card p-3">';
echo '<form action="?tn=reactions&sub=events" method="POST">';

echo '<div class="position-absolute top-0 end-0 p-3">';
echo '<input type="submit" name="delete_reactions" value="'.$lang['delete_selected'].'" class="btn btn-danger">';
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</div>';

echo '<div class="scroll-box">';
echo '<table class="table table-sm">';

for($i=0;$i<$cnt_get_commitments;$i++) {
	
	echo '<tr>';
	
	echo '<td><input class="form-check-input" type="checkbox" name="bulk_delete[]" value="'.$get_commitments[$i]['comment_id'].'"></td>';
	
	echo '<td>'.$get_commitments[$i]['comment_id'].'</td>';
	echo '<td>'.date('Y-m-d H:i',$get_commitments[$i]['comment_time']).'</td>';
	
	if(strlen($get_commitments[$i]['comment_author']) == 32 && ctype_xdigit($get_commitments[$i]['comment_author'])) {
		$voter = '<i>anonymous</i>';
	} else {
		$voter = $get_commitments[$i]['comment_author'];
	}
	
	echo '<td>'.$voter.'</td>';
	
	
	$title = $temp_event[$get_commitments[$i]['comment_relation_id']];
	
	echo '<td>'.$title.'</td>';
	
	echo '</tr>';
	
}

echo '</table>';

echo '</div>'; // scroll-box
echo '</div>'; // card

echo '</form>';

echo '</div>';
echo '<div class="col-md-3">';

echo '<div class="card p-3">';

echo '<form action="?tn=reactions&sub=events" method="POST">';
echo '<div class="form-group">';
echo '<label>'.$lang['label_filter'].'</label>';
echo '<select name="filter_by_event" class="custom-select form-control" onchange="this.form.submit()">';
echo '<option value="all">'.$lang['label_show_all_events'].'</option>';
foreach($get_events as $events) {
	
	$sel = '';
	if($_SESSION['filter_by_event'] == $events['post_id']) {
		$sel = 'selected';
	}
	
	echo '<option value="'.$events['post_id'].'" '.$sel.'>'.$events['post_title'].'</option>';
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