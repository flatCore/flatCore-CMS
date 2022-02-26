<?php
error_reporting(E_ALL ^E_NOTICE);

if(isset($_POST['remove_from_cart'])) {
	$id = (int) $_POST['remove_from_cart'];
	fc_remove_from_cart($id);
}

$get_cart_items = fc_return_my_cart();
$cnt_cart_items = count($get_cart_items);

$cart_str = 'IM KORB '.$cnt_cart_items;

$price_all_net = 0; // reset price net
$price_all_gross = 0; // reset price gross
$shipping_costs = 0; // reset shipping costs
$shipping_products = 0; // number of products which will be shipped


for($i=0;$i<$cnt_cart_items;$i++) {
	
	$this_item = fc_get_post_data($get_cart_items[$i]['cart_product_id']);

	$cart_item[$i]['nbr'] = $i+1;
	$cart_item[$i]['title'] = $this_item['post_title'];
	$cart_item[$i]['amount'] = $get_cart_items[$i]['cart_product_amount'];
	$cart_item[$i]['cart_id'] = $get_cart_items[$i]['cart_id'];
	
	/* will the product be delivered? */
	if($this_item['post_product_shipping_mode'] == 2) {
		$shipping_products++;
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
	$cart_item[$i]['price_net'] = $post_prices['net'];
	$cart_item[$i]['price_gross'] = $post_prices['gross'];
	
	//$post_price_net = str_replace('.', '', $post_prices['net_raw']);
	//$post_price_net = str_replace(',', '.', $post_price_net);
	$price_all_net = $price_all_net+round($post_prices['net_raw'],2);
	
	$post_price_gross = $post_prices['net_raw']*($tax+100)/100;
	$price_all_gross = $price_all_gross+round($post_price_gross,2);
		
	
	$smarty->assign('cart_items', $cart_item);
	
	//echo $this_item['post_title'];
	
	//print_r($this_item);
	
}

if($shipping_products > 0) {
	/* we have products for shipping */
	if($fc_prefs['prefs_shipping_costs_mode'] == 1) {
		$shipping_costs = $fc_prefs['prefs_shipping_costs_flat'];
	}
}


$cart_price_end = $price_all_gross+$shipping_costs;

$smarty->assign("cnt_items",$cnt_cart_items,true);
$smarty->assign('cart_price_net', fc_post_print_currency($price_all_net), true);
$smarty->assign('cart_price_gross', fc_post_print_currency($price_all_gross), true);
$smarty->assign('cart_shipping_costs', fc_post_print_currency($shipping_costs), true);
$smarty->assign('cart_price_end', fc_post_print_currency($cart_price_end), true);

$cart_table = $smarty->fetch("cart_table.tpl",$cache_id);

$smarty->assign('page_content', $cart_table, true);

?>