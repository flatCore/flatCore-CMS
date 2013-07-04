<?php

/**
 * ACP Templates
 * 
 */


$bs_form_control_group = file_get_contents('templates/bs-form-control-group.tpl');


function tpl_form_control_group($labelFor,$labelText,$formControls) {
	global $bs_form_control_group;
	$tpl = str_replace('{labelText}', $labelText, $bs_form_control_group);
	$tpl = str_replace('{labelFor}', $labelText, $tpl);
	$tpl = str_replace('{formControls}', $formControls, $tpl);
	return $tpl;
}

?>