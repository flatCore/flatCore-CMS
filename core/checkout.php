<?php
error_reporting(E_ALL ^E_NOTICE);

/**
 * show shopping cart
 * send order
 */

if(isset($_POST['remove_from_cart'])) {
	$id = (int) $_POST['remove_from_cart'];
	fc_remove_from_cart($id);
}


$get_cart_items = fc_return_my_cart();
$cnt_cart_items = count($get_cart_items);

$payment_methods = fc_get_payment_methods();

if($_SESSION['set_payment'] == '') {
	$_SESSION['set_payment'] = $payment_methods[0]['key'];
}

if(isset($_POST['set_payment'])) {
	$_SESSION['set_payment'] = clean_filename($_POST['set_payment']);
}

if($_SESSION['set_payment'] == 'prefs_pm_bank_transfer') {
	$payment_message = get_textlib('cart_pm_bank_transfer',$languagePack);
}

if($_SESSION['set_payment'] == 'prefs_pm_invoice') {
	$payment_message = get_textlib('cart_pm_invoice',$languagePack);
}

if($_SESSION['set_payment'] == 'prefs_pm_cash') {
	$payment_message = get_textlib('cart_pm_cash',$languagePack);
}

if($_SESSION['set_payment'] == 'prefs_pm_paypal') {
	$payment_message = get_textlib('cart_pm_paypal',$languagePack);
}


$payment_costs = fc_get_payment_costs($_SESSION['set_payment']);

// check the radio for payment
// example $checked_prefs_pm_invoice
$check_pm_radio = 'checked_'.$_SESSION['set_payment'];
$smarty->assign("$check_pm_radio", 'checked');

$get_cd = get_my_userdata();
$client_data = '';
if($get_cd['user_company'] != '') {
	$client_data .= $get_cd['user_company'].'<br>';
}
$client_data .= $get_cd['user_firstname']. ' '.$get_cd['user_lastname'].'<br>';
$client_data .= $get_cd['user_street']. ' '.$get_cd['user_street_nbr'].'<br>';
$client_data .= $get_cd['user_zipcode']. ' '.$get_cd['user_city'].'<br>';


$price_all_net = 0; // reset price net
$price_all_gross = 0; // reset price gross
$shipping_costs = 0; // reset shipping costs
$shipping_products = 0; // number of products which will be shipped
$store_shipping_cat = 0; // reset shipping category

for($i=0;$i<$cnt_cart_items;$i++) {
	
	$this_item = fc_get_post_data($get_cart_items[$i]['cart_product_id']);

	$cart_item[$i]['nbr'] = $i+1;
	$cart_item[$i]['title'] = $this_item['post_title'];
	$cart_item[$i]['product_number'] = $this_item['post_product_number'];
	$cart_item[$i]['amount'] = $get_cart_items[$i]['cart_product_amount'];
	$cart_item[$i]['cart_id'] = $get_cart_items[$i]['cart_id'];
	$cart_item[$i]['post_id'] = $get_cart_items[$i]['cart_product_id'];
	
	/* will the product be delivered? */
	if($this_item['post_product_shipping_mode'] == 2) {
		$shipping_products++;
		
		if($this_item['post_product_shipping_cat'] > $store_shipping_cat) {
			$store_shipping_cat = $this_item['post_product_shipping_cat'];
		}
		
	}
	

	if($this_item['post_product_tax'] == '1') {
		$tax = $fc_prefs['prefs_posts_products_default_tax'];
	} else if($this_item['post_product_tax'] == '2') {
		$tax = $fc_prefs['prefs_posts_products_tax_alt1'];
	} else {
		$tax = $fc_prefs['prefs_posts_products_tax_alt2'];
	}
	
	$cart_item[$i]['tax'] = $tax;
	
	$post_product_price_addition = $this_item['post_product_price_addition'];
	if($post_product_price_addition == '') {
		$post_product_price_addition = 0;
	}

	$post_prices = fc_posts_calc_price($this_item['post_product_price_net'],$post_product_price_addition,$tax);
	$cart_item[$i]['price_net_format'] = $post_prices['net'];
	$cart_item[$i]['price_gross_format'] = $post_prices['gross'];
	$cart_item[$i]['price_net_raw'] = $post_prices['net_raw'];
	$cart_item[$i]['price_gross_raw'] = $post_prices['gross_raw'];
	$cart_item[$i]['price_net'] = $this_item['post_product_price_net'];
	
	$price_all_net = $price_all_net+round($post_prices['net_raw'],2);
	$post_price_gross = $post_prices['net_raw']*($tax+100)/100;
	$price_all_gross = $price_all_gross+round($post_price_gross,2);

}


$smarty->assign('cart_items', $cart_item);

/* check if we have products for shipping */
if($shipping_products > 0) {
	
	if($fc_prefs['prefs_shipping_costs_mode'] == 1) {
		/* flatrate shipping */
		$shipping_type = '';
		$shipping_costs = str_replace(',','.',$fc_prefs['prefs_shipping_costs_flat']);
	}
	
	
	if($fc_prefs['prefs_shipping_costs_mode'] == 2) {
		/* we need to determine the highest shipping category */
		/* it's stored in $store_shipping_cat */
		if($store_shipping_cat == 1) {
			$shipping_costs = str_replace(',','.',$fc_prefs['prefs_shipping_costs_cat1']);
		} else if($store_shipping_cat == 2) {
			$shipping_costs = str_replace(',','.',$fc_prefs['prefs_shipping_costs_cat2']);
		} else {
			$shipping_costs = str_replace(',','.',$fc_prefs['prefs_shipping_costs_cat3']);
		}
		
	}
	
}

$smarty->assign('payment_methods', $payment_methods);
$smarty->assign('payment_message', $payment_message);
$smarty->assign('client_data', $client_data);

$cart_agree_term = get_textlib('cart_agree_term',$languagePack);
$smarty->assign('cart_agree_term', $cart_agree_term);


/* calculate subtotal and total */
$cart_price_subtotal = $price_all_gross;
$cart_price_total = $cart_price_subtotal+$payment_costs+$shipping_costs;


/**
 * client has send the order
 * store data in fc_orders
 * reset shopping cart if data is saved
 */
 
if($_POST['order'] == 'send') {
	
	$send_order = true;
	
	if($_POST['check_cart_terms'] != 'check') {
		$send_order = false;
		$smarty->assign("cart_alert_error",$lang['msg_accept_terms'],true);
	}
	
	foreach ($cart_item as $key => $array) {
	    unset($array['price_net_format'],$array['price_gross_format'],$array['price_net']);
	    $cart_items[$key] = $array;  
	}

	
	/* store the order */
	if($send_order == true) {
		
		$cart_items_str = json_encode($cart_items, JSON_FORCE_OBJECT);
		
		$order_data['user_id'] = $get_cd['user_id'];
        $order_data['user_mail'] = $get_cd['user_mail'];
		$order_data['order_invoice_address'] = $client_data;
		$order_data['order_products'] = $cart_items_str;
		$order_data['order_price_total'] = $cart_price_total;
		$order_data['order_shipping_type'] = $shipping_type;
		$order_data['order_shipping_costs'] = $shipping_costs;
		$order_data['order_payment_type'] = $_SESSION['set_payment'];
		$order_data['order_payment_costs'] = $payment_costs;
        $order_data['order_comment'] = $_POST['cart_comment'];
		
		$order_id = fc_send_order($order_data);
		
		if($order_id > 0) {
			$smarty->assign("cart_alert_success",$lang['msg_order_send'],true);
            /* remove items from fc_carts */
            fc_clear_cart($order_data['user_id']);
            $cnt_cart_items = 0;
		}
	}
	
}

$smarty->assign("cnt_items",$cnt_cart_items,true);
$smarty->assign('cart_price_net', fc_post_print_currency($price_all_net), true);
$smarty->assign('cart_price_gross', fc_post_print_currency($price_all_gross), true);
$smarty->assign('cart_shipping_costs', fc_post_print_currency($shipping_costs), true);
$smarty->assign('cart_payment_costs', fc_post_print_currency($payment_costs), true);
$smarty->assign('cart_price_subtotal', fc_post_print_currency($cart_price_subtotal), true);
$smarty->assign('cart_price_total', fc_post_print_currency($cart_price_total), true);
$smarty->assign('currency', $fc_prefs['prefs_posts_products_default_currency'], true);


$cart_table = $smarty->fetch("shopping_cart.tpl",$cache_id);

$smarty->assign('page_content', $cart_table, true);

?>