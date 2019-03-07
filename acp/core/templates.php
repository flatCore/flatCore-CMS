<?php

/**
 * ACP Templates
 * 
 */


$bs_form_control_group = file_get_contents('templates/bs-form-control-group.tpl');
$bs_form_checkbox = file_get_contents('templates/bs-form-checkbox.tpl');
$bs_form_radio = file_get_contents('templates/bs-form-radio.tpl');


function tpl_form_control_group($labelFor,$labelText,$formControls) {
	global $bs_form_control_group;
	$tpl = str_replace('{labelText}', $labelText, $bs_form_control_group);
	$tpl = str_replace('{labelFor}', $labelText, $tpl);
	$tpl = str_replace('{formControls}', $formControls, $tpl);
	return $tpl;
}


function tpl_checkbox($checkbox_name,$checkbox_value,$checkbox_id,$checkbox_label,$checkbox_checked) {
	global $bs_form_checkbox;
	
	$tpl = str_replace('{checkbox_name}', $checkbox_name, $bs_form_checkbox);
	$tpl = str_replace('{checkbox_value}', $checkbox_value, $tpl);
	$tpl = str_replace('{checkbox_id}', $checkbox_id, $tpl);
	$tpl = str_replace('{checkbox_label}', $checkbox_label, $tpl);
	
	if($checkbox_checked == '') {
		$tpl = str_replace('{checked}', '', $tpl);
	} else {
		$tpl = str_replace('{checked}', 'checked', $tpl);
	}
	
	return $tpl;
}


function tpl_radio($radio_name,$radio_value,$radio_id,$radio_label,$radio_checked) {
	global $bs_form_radio;
	
	$tpl = str_replace('{radio_name}', $radio_name, $bs_form_radio);
	$tpl = str_replace('{radio_value}', $radio_value, $tpl);
	$tpl = str_replace('{radio_id}', $radio_id, $tpl);
	$tpl = str_replace('{radio_label}', $radio_label, $tpl);
	
	if($radio_checked == '') {
		$tpl = str_replace('{checked}', '', $tpl);
	} else {
		$tpl = str_replace('{checked}', 'checked', $tpl);
	}
	
	return $tpl;
}

?>