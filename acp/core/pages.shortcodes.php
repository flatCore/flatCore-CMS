<?php
//error_reporting(E_ALL ^E_NOTICE);
//prohibit unauthorized access
require 'core/access.php';

$show_form = 'false';

/*save or update shortcode */
if(isset($_POST['write_shortcode'])) {
	
	/* labels */
	$arr_labels = $_POST['shortcode_labels'];
	if(is_array($arr_labels)) {
		sort($arr_labels);
		$string_labels = implode(",", $arr_labels);
	} else {
		$string_labels = "";
	}	
	
	
	if($_POST['shortcode_id'] != '') {
		$db_mode = 'update';
		$shortcode_id = (int) $_POST['shortcode_id'];
	} else {
		$db_mode = 'save';
	}
	
	
	if($db_mode == 'update') {
		// update shorcode
		$data = $db_content->update("fc_textlib", [
			"textlib_content" =>  $_POST['longcode'],
			"textlib_shortcode" => $_POST['shortcode'],
			"textlib_labels" => $string_labels,
			"textlib_type" => "shortcode"
		],[
			"textlib_id" => $shortcode_id
		]);
		
	} else {
		// new shortcode

		$data = $db_content->insert("fc_textlib", [
			"textlib_content" =>  $_POST['longcode'],
			"textlib_shortcode" => $_POST['shortcode'],
			"textlib_labels" => $string_labels,
			"textlib_type" => "shortcode"
		]);
		
		$last_insert_id = $db_content->id();
		
	}
	
	$show_form = 'true';
	
}


/* get data from shortcode by id */

if(isset($_GET['edit'])) {
	$shortcode_id = (int) $_GET['edit'];
	$show_form = 'true';
}

if(is_numeric($last_insert_id)) {
	$shortcode_id = (int) $last_insert_id;
	$show_form = 'true';	
}
	
if($shortcode_id != '' && is_numeric($shortcode_id)) {
		$get_shortcode = $db_content->get("fc_textlib", "*", [
			"textlib_id" => $shortcode_id
	]);
}

/* delete by id */

if(isset($_POST['delete'])) {
	$del_id = (int) $_POST['delete'];
	$delete = $db_content->delete("fc_textlib", [
		"AND" => [
			"textlib_id" => $del_id,
			"textlib_type" => "shortcode"
		]
	]);
}



/* print the form */

if($show_form == 'true') {
	echo '<div class="card p-3 mb-3">';
	echo '<form action="?tn=pages&sub=shortcodes" method="POST">';
	
	echo '<div class="row">';
	echo '<div class="col-md-9">';
	
	echo '<div class="form-group">';
	echo '<label for="elements">Shortcode</label>';
	echo '<input type="text" class="form-control" name="shortcode" value="'.$get_shortcode['textlib_shortcode'].'">';
	echo '</div>';
	
	echo '<div class="form-group">';
	echo '<label for="elements">Longcode</label>';
	echo '<textarea name="longcode" rows="8" class="form-control">'.$get_shortcode['textlib_content'].'</textarea>';
	echo '</div>';
	
	
	
	$cnt_labels = count($fc_labels);
	$arr_checked_labels = explode(",", $get_shortcode['textlib_labels']);
	
	for($i=0;$i<$cnt_labels;$i++) {
		$label_title = $fc_labels[$i]['label_title'];
		$label_id = $fc_labels[$i]['label_id'];
		$label_color = $fc_labels[$i]['label_color'];
		
	  if(in_array("$label_id", $arr_checked_labels)) {
			$checked_label = "checked";
		} else {
			$checked_label = "";
		}
		
		$checkbox_set_labels .= '<div class="form-check form-check-inline">';
	 	$checkbox_set_labels .= '<input class="form-check-input" id="label'.$label_id.'" type="checkbox" '.$checked_label.' name="shortcode_labels[]" value="'.$label_id.'">';
	 	$checkbox_set_labels .= '<label class="form-check-label" for="label'.$label_id.'">'.$label_title.'</label>';
		$checkbox_set_labels .= '</div>';
	}
	
	echo '</div>';
	echo '<div class="col-md-3">';
	
	echo '<div class="form-group">';
	echo '<p>'.$lang['labels'].'</p>';
	echo $checkbox_set_labels;
	echo '</div>';
	
	echo '<hr>';
	
	echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
	
	if($get_shortcode['textlib_id'] != '') {
		echo '<input type="hidden" name="shortcode_id" value="'.$get_shortcode['textlib_id'].'">';
		echo '<input type="submit" name="write_shortcode" value="'.$lang['update'].'" class="btn btn-success w-100">';
	} else {
		echo '<input type="submit" name="write_shortcode" value="'.$lang['save'].'" class="btn btn-success w-100">';
	}
	
	echo '</div>';
	echo '</div>';
	
	echo '</form>';
	echo '</div>';
	echo '<hr class="shadow-line">';
}



$label_filter_box  = '<div class="card mt-2">';
$label_filter_box .= '<div class="card-header p-1 px-2">'.$lang['labels'].'</div>';
$label_filter_box .= '<div class="card-body">';
$this_btn_status = '';
foreach($fc_labels as $label) {

	if(in_array($label['label_id'], $a_checked_labels)) {
		$this_btn_status = 'active';
	} else {
		$this_btn_status = '';
	}

	$label_title = '<span class="label-dot" style="background-color:'.$label['label_color'].';"></span> '.$label['label_title'];
	$label_filter_box .= '<a href="acp.php?tn=pages&sub='.$sub.'&switchLabel='.$label['label_id'].'" class="btn btn-fc btn-sm m-1 '.$this_btn_status.'">'.$label_title.'</a>';

}
$label_filter_box .= '</div>';
$label_filter_box .= '</div>'; // card

$label_filter['labels'] = $_SESSION['checked_label_str'];


/* get all shortcodes */

$shortcodes = fc_get_shortcodes($label_filter);
$cnt_shortcodes = count($shortcodes);

echo '<div class="row">';
echo '<div class="col-md-9">';

echo '<table class="table table-sm">';
echo '<thead>';
echo '<tr>';
echo '<th>Shortcode</th>';
echo '<th>Longcode</th>';
echo '<th>Label</th>';
echo '<th></th>';
echo '</tr>';
echo '</thead>';


for($i=0;$i<$cnt_shortcodes;$i++) {
	
	$btn_edit = '<a href="?tn=pages&sub=shortcodes&edit='.$shortcodes[$i]['textlib_id'].'" class="btn btn-fc text-success btn-sm">'.$icon['edit'].'</a>';
	
	$btn_delete  = '<form action="?tn=pages&sub=shortcodes" method="POST" class="d-inline">';
	$btn_delete .= '<button type="submit" name="delete" value="'.$shortcodes[$i]['textlib_id'].'" class="btn btn-danger btn-sm">'.$icon['trash_alt'].'</button>';
	$btn_delete .= '</form>';
	
	$get_sc_labels = explode(',',$shortcodes[$i]['textlib_labels']);
	
	
	$label = '';
	if($shortcodes[$i]['textlib_labels'] != '') {
		foreach($get_sc_labels as $sc_label) {
			
			foreach($fc_labels as $l) {
				if($sc_label == $l['label_id']) {
					$label_color = $l['label_color'];
					$label_title = $l['label_title'];
				}
			}
			
			$label .= '<span class="label-dot" style="background-color:'.$label_color.';" title="'.$label_title.'"></span>';
		}
	}
	
	$longcode = htmlentities($shortcodes[$i]['textlib_content']);
	if(strlen($longcode) > 75) {
		$longcode = substr($longcode, 0,75). ' <em><small>(...)</small></em>';
	}
	
	$copy_shortcode  = '<div class="input-group">';
	$copy_shortcode .= '<input type="text" class="form-control" id="copy_sc_'.$i.'" value="'.$shortcodes[$i]['textlib_shortcode'].'" readonly>';
	$copy_shortcode .= '<button type="button" class="btn btn-fc copy-btn" data-clipboard-target="#copy_sc_'.$i.'">'.$icon['clipboard'].'</button>';
	$copy_shortcode .= '</div>';
		
	echo '<tr>';
	echo '<td>'.$copy_shortcode.'</td>';
	echo '<td><code>'.$longcode.'</code></td>';
	echo '<td>'.$label.'</td>';
	echo '<td class="text-right">'.$btn_edit.' '.$btn_delete.'</td>';
	echo '</tr>';	
}


echo '</table>';








echo '</div>';
echo '<div class="col-md-3">';

/* sidebar */

echo '<a href="?tn=pages&sub=shortcodes&edit=new" class="btn btn-success w-100">'.$icon['plus'].' '.$lang['new'].'</a><hr>';


echo $label_filter_box;

/* sidebar end */

echo '</div>';
echo '</div>';


?>