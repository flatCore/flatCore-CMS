<?php

//prohibit unauthorized access
require("core/access.php");

foreach($_POST as $key => $val) {
	$$key = @htmlspecialchars($val, ENT_QUOTES); 
}


/* save upload preferences */
if(isset($_POST['update_shop'])) {

	foreach($_POST as $key => $val) {
		$data[htmlentities($key)] = htmlentities($val);
	}
	
	if($_POST['prefs_pm_bank_transfer'] != 1) {
		$data['prefs_pm_bank_transfer'] = 0;
	}
	if($_POST['prefs_pm_invoice'] != 1) {
		$data['prefs_pm_invoice'] = 0;
	}
	
	fc_write_option($data,'fc');
}

if(isset($_POST)) {
	/* read the preferences again */
	$fc_get_preferences = fc_get_preferences();
	
	foreach($fc_get_preferences as $k => $v) {
		$key = $fc_get_preferences[$k]['option_key'];
		$value = $fc_get_preferences[$k]['option_value'];
		$fc_preferences[$key] = $value;
	}
	
	foreach($fc_preferences as $k => $v) {
	   $$k = stripslashes($v);
	}
}


echo '<form action="?tn=system&sub=shop" method="POST">';


/* products */

echo '<fieldset>';
echo '<legend>'.$lang['post_type_product'].'</legend>';

echo '<div class="row">';
echo '<div class="col">';
echo '<div class="form-group">
				<label>' . $lang['products_default_tax'] . '</label>
				<input type="text" class="form-control" name="prefs_posts_products_default_tax" value="'.$prefs_posts_products_default_tax.'">
			</div>';
echo '</div>';
echo '<div class="col">';
echo '<div class="form-group">
				<label>' . $lang['label_product_tax_alt1'] . '</label>
				<input type="text" class="form-control" name="prefs_posts_products_tax_alt1" value="'.$prefs_posts_products_tax_alt1.'">
			</div>';
echo '</div>';
echo '<div class="col">';
echo '<div class="form-group">
				<label>' . $lang['label_product_tax_alt2'] . '</label>
				<input type="text" class="form-control" name="prefs_posts_products_tax_alt2" value="'.$prefs_posts_products_tax_alt2.'">
			</div>';
echo '</div>';
echo '</div>';
			

echo '<div class="form-group">
				<label>' . $lang['products_default_currency'] . '</label>
				<input type="text" class="form-control" name="prefs_posts_products_default_currency" value="'.$prefs_posts_products_default_currency.'">
			</div>';

echo'</fieldset>';

echo '<div class="alert alert-danger">';
echo 'This area is still in the development phase and should not be used productively';
echo '</div>';

echo '<fieldset class="mt-4">';
echo '<legend>'.$lang['label_carts'].'</legend>';

$sel_carts1 = '';
$sel_carts2 = '';
$sel_carts3 = '';

if($prefs_posts_products_cart == 1 OR $prefs_posts_products_cart == '') {
	$sel_carts1 = 'selected';
} else if($prefs_posts_products_cart == 2) {
	$sel_carts2 = 'selected';
} else if($prefs_posts_products_cart == 3) {
	$sel_carts3 = 'selected';
}

echo '<div class="form-group">';
echo '<label>' . $lang['label_carts'] . '</label>';
echo '<select class="form-control custom-select" name="prefs_posts_products_cart">';
echo '<option value="1" '.$sel_carts1.'>'.$lang['carts_deactivated'].'</option>';
echo '<option value="2" '.$sel_carts2.'>'.$lang['carts_for_registered'].'</option>';
echo '<option value="3" '.$sel_carts3.'>'.$lang['carts_for_all'].'</option>';
echo '</select>';
echo '</div>';

echo'</fieldset>';


echo '<fieldset>';
echo '<legend>'.$lang['label_shipping'].'</legend>';


if($prefs_shipping_costs_mode == 1 OR $prefs_shipping_costs_mode == '') {
	$sel_shipping_costs_mode1 = 'selected';
} else if($prefs_shipping_costs_mode == 2) {
	$sel_shipping_costs_mode2 = 'selected';
}

echo '<div class="form-group">';
echo '<label>' . $lang['label_shipping_mode'] . '</label>';
echo '<select class="form-control custom-select" name="prefs_shipping_costs_mode">';
echo '<option value="1" '.$sel_shipping_costs_mode1.'>'.$lang['label_shipping_mode_flat'].'</option>';
echo '<option value="2" '.$sel_shipping_costs_mode2.'>'.$lang['label_shipping_mode_cats'].'</option>';
echo '</select>';
echo '</div>';

echo '<div class="form-group">
				<label>' . $lang['label_shipping_costs_flat'] . '</label>
				<input type="text" class="form-control" name="prefs_shipping_costs_flat" value="'.$prefs_shipping_costs_flat.'">
			</div>';
			
echo '<div class="row">';
echo '<div class="col">';
echo '<div class="form-group">
				<label>' . $lang['label_shipping_costs_cat1'] . '</label>
				<input type="text" class="form-control" name="prefs_shipping_costs_cat1" value="'.$prefs_shipping_costs_cat1.'">
			</div>';
echo '</div>';
echo '<div class="col">';
echo '<div class="form-group">
				<label>' . $lang['label_shipping_costs_cat2'] . '</label>
				<input type="text" class="form-control" name="prefs_shipping_costs_cat2" value="'.$prefs_shipping_costs_cat2.'">
			</div>';
echo '</div>';
echo '<div class="col">';
echo '<div class="form-group">
				<label>' . $lang['label_shipping_costs_cat3'] . '</label>
				<input type="text" class="form-control" name="prefs_shipping_costs_cat3" value="'.$prefs_shipping_costs_cat3.'">
			</div>';
echo '</div>';
echo '</div>';


echo'</fieldset>';

/**
 * payment methods
 */

echo '<fieldset>';
echo '<legend>'.$lang['label_payment_methods'].'</legend>';

echo '<table class="table">';
echo '<tr>';
echo '<td>Active</td>';
echo '<td>Type</td>';
echo '<td>'.$lang['label_payment_costs'].'</td>';
echo '</tr>';

echo '<tr>';
echo '<td>';
$check_bt = ($prefs_pm_bank_transfer == 1) ? 'checked' : '';
echo '<input class="form-check-input" type="checkbox" name="prefs_pm_bank_transfer" value="1" id="checkBankTransfer" '.$check_bt.'>';
echo '</td>';
echo '<td>';
echo '<label class="form-check-label" for="checkBankTransfer">'.$lang['label_payment_bank_transfer'].'</label>';
echo '</td>';
echo '<td>';
echo '<input type="text" class="form-control" name="prefs_payment_costs_bt" value="'.$prefs_payment_costs_bt.'">';
echo '</td>';
echo '</tr>';

echo '<tr>';
echo '<td>';
$check_paypal = ($prefs_pm_paypal == 1) ? 'checked' : '';
echo '<input class="form-check-input" type="checkbox" name="prefs_pm_paypal" value="1" id="checkPayPal" '.$check_paypal.'>';
echo '</td>';
echo '<td>';
echo '<label class="form-check-label" for="checkPayPal">'.$lang['label_payment_paypal'].'</label>';
echo '</td>';
echo '<td>';
echo '<input type="text" class="form-control" name="prefs_payment_costs_paypal" value="'.$prefs_payment_costs_paypal.'">';
echo '</td>';
echo '</tr>';

echo '<tr>';
echo '<td>';
$check_invoice = ($prefs_pm_invoice == 1) ? 'checked' : '';
echo '<input class="form-check-input" type="checkbox" name="prefs_pm_invoice" value="1" id="checkInvoice" '.$check_invoice.'>';
echo '</td>';
echo '<td>';
echo '<label class="form-check-label" for="checkInvoice">'.$lang['label_payment_invoice'].'</label>';
echo '</td>';
echo '<td>';
echo '<input type="text" class="form-control" name="prefs_payment_costs_invoice" value="'.$prefs_payment_costs_invoice.'">';
echo '</td>';
echo '</tr>';

echo '<tr>';
echo '<td>';
$check_cash = ($prefs_pm_cash == 1) ? 'checked' : '';
echo '<input class="form-check-input" type="checkbox" name="prefs_pm_cash" value="1" id="checkCash" '.$check_cash.'>';
echo '</td>';
echo '<td>';
echo '<label class="form-check-label" for="checkCash">'.$lang['label_payment_cash'].'</label>';
echo '</td>';
echo '<td>';
echo '<input type="text" class="form-control" name="prefs_payment_costs_cash" value="'.$prefs_payment_costs_cash.'">';
echo '</td>';
echo '</tr>';

echo '</table>';

echo'</fieldset>';


echo '<input type="submit" class="btn btn-save" name="update_shop" value="'.$lang['update'].'">';
echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';

echo '</form>';


?>