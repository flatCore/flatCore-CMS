<?php

//error_reporting(E_ALL ^E_NOTICE);

/**
 * add a product to cart
 * 
 */

function fc_add_to_cart() {
	
	global $db_content;
	global $fc_prefs;
	
	$cart_product_id = (int) $_POST['add_to_cart'];
	$cart_product_amount = 1;
	$cart_time = time();
	
	/* check if user or visitor */
	if(is_numeric($_SESSION['user_id'])) {
		$cart_user_id = $_SESSION['user_id'];
		$cart_user_hash = '';
	} else {
		$cart_user_id = '';
		$cart_user_hash = $_SESSION['visitor_csrf_token'];
	}
	
	$cart_status = 'progress';
	
	/* we store tax and price_net from item */
	$this_item = fc_get_post_data($cart_product_id);
	$cart_product_price_net = $this_item['post_product_price_net'];
	$cart_product_title = $this_item['post_title'];
	$cart_product_number = $this_item['post_product_number'];
	
	if($this_item['post_product_tax'] == '1') {
		$cart_product_tax = $fc_prefs['prefs_posts_products_default_tax'];
	} else if($this_item['post_product_tax'] == '2') {
		$cart_product_tax = $fc_prefs['prefs_posts_products_tax_alt1'];
	} else {
		$cart_product_tax = $fc_prefs['prefs_posts_products_tax_alt2'];
	}
	
	$db_content->insert("fc_carts", [
		"cart_time" =>  $cart_time,
		"cart_user_hash" =>  $cart_user_hash,
		"cart_user_id" =>  $cart_user_id,
		"cart_product_id" =>  $cart_product_id,
		"cart_product_amount" =>  $cart_product_amount,
		"cart_product_price_net" =>  $cart_product_price_net,
		"cart_product_tax" =>  $cart_product_tax,
		"cart_product_title" =>  $cart_product_title,
		"cart_product_number" =>  $cart_product_number,
		"cart_status" =>  $cart_status
	]);
			
	$insert_id = $db_content->id();
	return $insert_id;
}


function fc_return_cart_amount() {
	
	global $db_content;
	
	/* check if user or visitor */
	if(is_numeric($_SESSION['user_id'])) {
		$cart_user_id = $_SESSION['user_id'];
		
		$items = $db_content->select("fc_carts", ["cart_id"], [
			"AND" => [
				"cart_user_id" => $cart_user_id,
				"cart_status" => "progress"
			]
		]);
		
	} else {
		$cart_user_hash = $_SESSION['visitor_csrf_token'];
		$items = $db_content->select("fc_carts", ["cart_id"], [
			"AND" => [
				"cart_user_hash" => $cart_user_hash,
				"cart_status" => "progress"
			]
		]);
	}
	
	$cnt_items = count($items);
	
	return $cnt_items;
	
}


function fc_return_my_cart() {
	
	global $db_content;
	
	/* check if user or visitor */
	if(is_numeric($_SESSION['user_id'])) {
		$cart_user_id = $_SESSION['user_id'];
		
		$items = $db_content->select("fc_carts", "*", [
			"AND" => [
				"cart_user_id" => $cart_user_id,
				"cart_status" => "progress"
			]
		]);
		
	} else {
		$cart_user_hash = $_SESSION['visitor_csrf_token'];
		$items = $db_content->select("fc_carts", "*", [
			"AND" => [
				"cart_user_hash" => $cart_user_hash,
				"cart_status" => "progress"
			]
		]);
	}
	
	
	return $items;
	
}

/**
 * remove items by id (int)
 */

function fc_remove_from_cart($id) {
	
	global $db_content;
	
	$id = (int) $id;
	
	/* check if user or visitor */
	if(is_numeric($_SESSION['user_id'])) {
		$cart_user_id = $_SESSION['user_id'];
		$data = $db_content->delete("fc_carts", [
			"AND" => [
				"cart_user_id" => $cart_user_id,
				"cart_status" => "progress",
				"cart_id" => $id
			]
		]);
		
	} else {
		$cart_user_hash = $_SESSION['visitor_csrf_token'];
		$data = $db_content->delete("fc_carts", [
			"AND" => [
				"cart_user_hash" => $cart_user_hash,
				"cart_status" => "progress",
				"cart_id" => $id
			]
		]);		
		
	}
}


/**
 * @param $user user id for clients or hash for guest
 * @return void
 */
function fc_clear_cart($user) {

    global $db_content;

    if(is_numeric($user)) {
        $data = $db_content->delete("fc_carts", [
            "AND" => [
                "cart_user_id" => $user,
                "cart_status" => "progress"
            ]
        ]);
    } else {
        $data = $db_content->delete("fc_carts", [
            "AND" => [
                "cart_user_hash" => $user,
                "cart_status" => "progress"
            ]
        ]);
    }
}

/**
 * get payment methods
 * at the moment we have no third party payment methods
 * just check if payment method is active
 */
 
function fc_get_payment_methods() {
	
	global $fc_prefs;
	global $lang;
	$payment_methods = array();
	
	if($fc_prefs['prefs_pm_bank_transfer'] == 1) {
		$payment_methods[0]['key'] = 'prefs_pm_bank_transfer';
		$payment_methods[0]['cost'] = $fc_prefs['prefs_payment_costs_bt'];
		$payment_methods[0]['title'] = $lang['label_pm_bank_transfer'];
		$payment_methods[0]['checked'] = '';
	}
	if($fc_prefs['prefs_pm_invoice'] == 1) {
		$payment_methods[1]['key'] = 'prefs_pm_invoice';
		$payment_methods[1]['cost'] = $fc_prefs['prefs_payment_costs_invoice'];
		$payment_methods[1]['title'] = $lang['label_pm_invoice'];
		$payment_methods[1]['checked'] = '';
	}
	if($fc_prefs['prefs_pm_cash'] == 1) {
		$payment_methods[2]['key'] = 'prefs_pm_cash';
		$payment_methods[2]['cost'] = $fc_prefs['prefs_payment_costs_cash'];
		$payment_methods[2]['title'] = $lang['label_pm_cash'];
		$payment_methods[2]['checked'] = '';
	}
	if($fc_prefs['prefs_pm_paypal'] == 1) {
		$payment_methods[3]['key'] = 'prefs_pm_paypal';
		$payment_methods[3]['cost'] = $fc_prefs['prefs_payment_costs_paypal'];
		$payment_methods[3]['title'] = $lang['label_pm_paypal'];
		$payment_methods[3]['checked'] = '';
	}
	
	return $payment_methods;
}

/**
 * get payment costs from array
 * key (string)
 */
 
function fc_get_payment_costs($key) {
	
	$pm_costs = '0.00';
	
	$payment_methods = fc_get_payment_methods();
	
	$id = array_search($key, array_column($payment_methods, 'key'));
	$pm_costs = $payment_methods[$id]['cost'];
	$pm_costs = str_replace(',', '.', $pm_costs);
	return $pm_costs;
}


/**
 * client send an order
 * $data (array)
 * return row_id
 */

function fc_send_order($data) {
	
	global $db_content;
	global $fc_prefs;
	
	$user_id = $data['user_id'];
	$order_nbr = $user_id.'-'.uniqid();
	$order_time = time();
	$order_status = 1;
	$order_status_shipping = 1;
	$order_status_payment = 1;
	$order_invoice_address = $data['order_invoice_address'];
	$order_products = $data['order_products'];
	$order_price_total = $data['order_price_total'];
	$order_shipping_type = $data['order_shipping_type'];
	$order_shipping_costs = $data['order_shipping_costs'];
	$order_payment_type = $data['order_payment_type'];
	$order_payment_costs = $data['order_payment_costs'];
	
	$db_content->insert("fc_orders", [
		"user_id" => "$user_id",
		"order_nbr" => "$order_nbr",
		"order_time" => "$order_time",
		"order_status" => "$order_status",
		"order_status_shipping" => "$order_status_shipping",
		"order_status_payment" => "$order_status_payment",
		"order_invoice_address" => "$order_invoice_address",
		"order_products" => "$order_products",
		"order_price_total" => $order_price_total,
		"order_shipping_type" => "$order_shipping_type",
		"order_shipping_costs" => "$order_shipping_costs",
		"order_payment_type" => "$order_payment_type",
		"order_payment_costs" => "$order_payment_costs",
		"order_currency" => $fc_prefs['prefs_posts_products_default_currency']
		
	]);

	$order_id = $db_content->id();

	return $order_id;
}

/**
 * get orders
 * $mode - 	if int show orders by user id
 *				if str 'all' show all orders
 * $status - 1 | 2 | 'all'
 */

function fc_get_orders($mode,$status) {
	
	global $db_content;
	/* check if user or visitor */
	if(is_numeric($mode)) {
		$user_id = (int) $mode;
		
		$orders = $db_content->select("fc_orders", "*", [
			"AND" => [
				"user_id" => $user_id,
				"order_status" => "$status"
			],
			"ORDER" => [
				"order_time" => "DESC"
			]
		]);
		
	} else if($mode == 'all') {
		
		if($status == 'all') {
			$status = [1,2,3];
		}

		$orders = $db_content->select("fc_orders", "*", [
			"AND" => [
				"order_status" => $status
			],
			"ORDER" => [
				"order_time" => "DESC"
			]
		]);
	} else {
		return;
	}
		
	return $orders;
}

/**
 * get order details
 * $id (int)
 *	return array
 */
 
function fc_get_order_details($id) {
	
	global $db_content;

	$order = $db_content->get("fc_orders","*", [
		"id" => $id
	]);
	
	return $order;
}


?>