<?php

//prohibit unauthorized access
require 'core/access.php';

foreach($_POST as $key => $val) {
	$$key = @htmlspecialchars($val, ENT_QUOTES); 
}

/* update labels */

if(isset($_POST['update_label'])) {
	
	$data = $db_content->update("fc_labels", [
			"label_color" =>  $label_color,
			"label_title" =>  $label_title,
			"label_description" =>  $label_description
		], [
		"label_id" => $label_id
		]);
	
}


/* new label */

if(isset($_POST['new_label'])) {
	
	$data = $db_content->insert("fc_labels", [
			"label_color" =>  $label_color,
			"label_title" =>  $label_title,
			"label_description" =>  $label_description
		]);
	
}

/* delete label */

if(isset($_POST['delete_label'])) {

	$label_id = (int) $_POST['label_id'];

	$data = $db_content->delete("fc_labels", [
		"label_id" => $label_id
		]);
	
}







echo '<fieldset>';
echo '<legend>'.$lang['labels'].'</legend>';

$fc_labels = fc_get_labels();
$cnt_labels = count($fc_labels);



for($i=0;$i<$cnt_labels;$i++) {
	echo '<form action="acp.php?tn=system&sub=labels" method="POST" class="clearfix" id="labels">';
	echo '<div class="row mb-1">';
	echo '<div class="col-md-2">';
	echo '<div class="input-group">';
	echo '<div class="input-group-prepend">';
	echo '<span class="input-group-text" id="basic-addon1" style="background-color:'.$fc_labels[$i]['label_color'].'"></span>';
	echo '</div>';
	echo '<input class="form-control" type="text" name="label_color" value="'.$fc_labels[$i]['label_color'].'">';
	echo '</div>';
	echo '</div>';
	echo '<div class="col-md-3">';
	echo '<input class="form-control" type="text" name="label_title" value="'.$fc_labels[$i]['label_title'].'">';
	echo '</div>';
	echo '<div class="col-md-5">';
	echo '<input class="form-control" type="text" name="label_description" value="'.$fc_labels[$i]['label_description'].'">';
	echo '</div>';
	echo '<div class="col-md-2">';
	echo '<input type="hidden" name="label_id" value="'.$fc_labels[$i]['label_id'].'">';
	echo '<div class="btn-group" role="group">';
	echo '<button type="submit" name="update_label" class="btn btn-save">'.$icon['sync_alt'].'</button>';
	echo '<button type="submit" name="delete_label" class="btn btn-fc text-danger">'.$icon['trash_alt'].'</button>';
	echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
	echo '</div>';
	echo '</div>';
	
	echo '</div>';
	echo '</form>';
	
	if($i == $cnt_labels-1) {
		echo '<hr>';
	}
	
}


echo '<form action="acp.php?tn=system&sub=labels" method="POST" class="form-horizontal">';
echo '<div class="row">';
echo '<div class="col-md-2">';
echo $lang['label_color'];
echo '<div class="input-group">';
echo '<span class="input-group-addon" id="basic-addon1" style="background-color:'.$fc_labels[$i]['label_color'].'"></span>';
echo '<input class="form-control" type="text" name="label_color" value="" placeholder="#3366cc">';
echo '</div>';
echo '</div>';
echo '<div class="col-md-3">';
echo $lang['label_title'];
echo '<input class="form-control" type="text" name="label_title" value="">';
echo '</div>';
echo '<div class="col-md-5">';
echo $lang['label_description'];
echo '<input class="form-control" type="text" name="label_description" value="">';
echo '</div>';
echo '<div class="col-md-2">';
echo '<br><button type="submit" name="new_label" class="btn btn-save">'.$lang['save'].'</button>';
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</div>';
echo '</div>';
echo '</form>';



echo"</fieldset>";


?>