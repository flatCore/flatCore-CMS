<?php

//error_reporting(E_ALL ^E_NOTICE);


/**
 * @param int $start
 * @param int $limit
 * @param array $filter
 * @return array
 */

function fc_get_products($start,$limit,$filter) {

    global $db_posts;
    global $db_type;
    global $time_string_start;
    global $time_string_end;
    global $time_string_now;
    global $fc_preferences;
    global $fc_labels;

    if(FC_SOURCE == 'frontend') {
        global $fc_prefs;
    }

    if(empty($start)) {
        $start = 0;
    }
    if(empty($limit)) {
        $limit = 10;
    }


    $limit_str = 'LIMIT '. (int) $start;

    if($limit == 'all') {
        $limit_str = '';
    } else {
        $limit_str .= ', '. (int) $limit;
    }


    /**
     * default order and direction
     */

    $order = "ORDER BY post_fixed ASC, post_priority DESC, post_id DESC";

    if($direction == 'ASC') {
        $direction = 'ASC';
    } else {
        $direction = 'DESC';
    }

    /* we have a custom order rule */
    if($filter['sort_by'] != '') {
        if($filter['sort_by'] == 'name') {
            $order = "ORDER BY post_fixed ASC, post_title ASC, post_priority DESC";
        }
        if($filter['sort_by'] == 'pasc') {
            $order = "ORDER BY post_fixed ASC, post_product_price_net*1 ASC, post_priority DESC";
        }
        if($filter['sort_by'] == 'pdesc') {
            $order = "ORDER BY post_fixed ASC, post_product_price_net*1 DESC, post_priority DESC";
        }
        if($filter['sort_by'] == 'ts') {
            $order = "ORDER BY post_fixed ASC, post_product_cnt_sales DESC, post_priority DESC";
        }

    }


    /* set filters */
    $sql_filter_start = "WHERE post_type LIKE '%p%' ";

    /* language filter */
    $sql_lang_filter = "post_lang IS NULL OR ";
    $lang = explode('-', $filter['languages']);
    foreach($lang as $l) {
        if($l != '') {
            $sql_lang_filter .= "(post_lang LIKE '%$l%') OR ";
        }
    }
    $sql_lang_filter = substr("$sql_lang_filter", 0, -3); // cut the last ' OR'


    /* status filter */
    $sql_status_filter = "post_status IS NULL OR ";
    $status = explode('-', $filter['status']);
    foreach($status as $s) {
        if($s != '') {
            $sql_status_filter .= "(post_status LIKE '%$s%') OR ";
        }
    }
    $sql_status_filter = substr("$sql_status_filter", 0, -3); // cut the last ' OR'


    /* category filter */
    if($filter['categories'] == 'all' OR $filter['categories'] == '') {
        $sql_cat_filter = '';
    } else {

        $cats = explode(',', $filter['categories']);
        foreach($cats as $c) {
            if($c != '') {
                $sql_cat_filter .= "(post_categories LIKE '%$c%') OR ";
            }
        }
        $sql_cat_filter = substr("$sql_cat_filter", 0, -3); // cut the last ' OR'
    }

    /* label filter */
    if($filter['labels'] == 'all' OR $filter['labels'] == '') {
        $sql_label_filter = '';
    } else {

        $checked_labels_array = explode('-', $filter['labels']);

        for($i=0;$i<count($fc_labels);$i++) {
            $label = $fc_labels[$i]['label_id'];
            if(in_array($label, $checked_labels_array)) {
                $sql_label_filter .= "post_labels LIKE '%,$label,%' OR post_labels LIKE '%,$label' OR post_labels LIKE '$label,%' OR post_labels = '$label' OR ";
            }
        }
        $sql_label_filter = substr("$sql_label_filter", 0, -3); // cut the last ' OR'
    }

    $sql_filter = $sql_filter_start;

    if($sql_lang_filter != "") {
        $sql_filter .= " AND ($sql_lang_filter) ";
    }

    if($sql_status_filter != "") {
        $sql_filter .= " AND ($sql_status_filter) ";
    }
    if($sql_cat_filter != "") {
        $sql_filter .= " AND ($sql_cat_filter) ";
    }
    if($sql_label_filter != "") {
        $sql_filter .= " AND ($sql_label_filter) ";
    }

    if(FC_SOURCE == 'frontend') {
        $sql_filter .= "AND post_releasedate <= '$time_string_now' ";
    }

    if($time_string_start != '') {
        $sql_filter .= "AND post_releasedate >= '$time_string_start' AND post_releasedate <= '$time_string_end' AND post_releasedate < '$time_string_now' ";
    }

    $sql = "SELECT * FROM fc_posts $sql_filter $order $limit_str";

    $entries = $db_posts->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $sql_cnt = "SELECT count(*) AS 'P', (SELECT count(*) FROM fc_posts WHERE post_type LIKE '%p%') AS 'A' ,(SELECT count(*) FROM fc_posts $sql_filter) AS 'F' ";
    $stat = $db_posts->query("$sql_cnt")->fetch(PDO::FETCH_ASSOC);

    /* number of posts that match the filter */
    $entries[0]['cnt_products_match'] = $stat['F'];
    $entries[0]['cnt_products_all'] = $stat['A'];
    return $entries;
}



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


/**
 * @param $item
 * @param $amount
 * @return void
 */

function fc_update_cart_item_amount($item,$amount){
    global $db_content;

    $item = (int) $item;
    $amount = (int) $amount;

    /* check if user or visitor */
    if(is_numeric($_SESSION['user_id'])) {
        $cart_user_id = $_SESSION['user_id'];

        $db_content->update("fc_carts", [
            "cart_product_amount" => $amount
        ], [
            "AND" => [
                "cart_id" => $item,
                "cart_user_id" => $cart_user_id,
                "cart_status" => "progress"
            ]
        ]);

    } else {

        $cart_user_hash = $_SESSION['visitor_csrf_token'];
        $db_content->update("fc_carts", [
            "cart_product_amount" => $amount
        ], [
            "AND" => [
                "cart_id" => $item,
                "cart_user_hash" => $cart_user_hash,
                "cart_status" => "progress"
            ]
        ]);

    }
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
    $order_invoice_mail = $data['user_mail'];
	$order_products = $data['order_products'];
	$order_price_total = $data['order_price_total'];
	$order_shipping_type = $data['order_shipping_type'];
	$order_shipping_costs = $data['order_shipping_costs'];
	$order_payment_type = $data['order_payment_type'];
	$order_payment_costs = $data['order_payment_costs'];
    $order_comment = clean_visitors_input($data['order_comment']);
	
	$db_content->insert("fc_orders", [
		"user_id" => "$user_id",
		"order_nbr" => "$order_nbr",
		"order_time" => "$order_time",
		"order_status" => "$order_status",
		"order_status_shipping" => "$order_status_shipping",
		"order_status_payment" => "$order_status_payment",
		"order_invoice_address" => "$order_invoice_address",
        "order_invoice_mail" => "$order_invoice_mail",
		"order_products" => "$order_products",
		"order_price_total" => $order_price_total,
		"order_shipping_type" => "$order_shipping_type",
		"order_shipping_costs" => "$order_shipping_costs",
		"order_payment_type" => "$order_payment_type",
		"order_payment_costs" => "$order_payment_costs",
		"order_currency" => $fc_prefs['prefs_posts_products_default_currency'],
        "order_user_comment" => "$order_comment"
		
	]);

	$order_id = $db_content->id();

	return $order_id;
}


/**
 * @param mixed $user if is numeric get orders by user id
 * @param array $filter status_payment, status_shipping, status_order
 * @param array $sort key and direction
 * @param integer $start start for pagination
 * @param integer $limit number of entries
 * @return void
 */

function fc_get_orders($user, $filter, $sort, $start=0, $limit=10) {
	
	global $db_content;

    if(isset($filter['status_shipping'])) {
        $set_filter_status_shipping = $filter['status_shipping'];
    }
    if(isset($filter['status_payment'])) {
        $set_filter_status_payment = $filter['status_payment'];
    }
    if(isset($filter['status_order'])) {
        $set_filter_status_order = $filter['status_order'];
    }

    if(empty($set_filter_status_payment)) {
        $set_filter_status_payment = [1,2,3];
    }
    if(empty($set_filter_status_shipping)) {
        $set_filter_status_shipping = [1,2,3];
    }
    if(empty($set_filter_status_order)) {
        $set_filter_status_order = [1,2,3];
    }

    if(empty($sort['key'])) {
        $sort['key'] = 'order_time';
    }
    if(empty($sort['direction'])) {
        $sort['direction'] = 'DESC';
    }

	/* check if user or visitor */
	if(is_numeric($user)) {
		$user_id = (int) $user;
		
		$orders = $db_content->select("fc_orders", "*", [
			"AND" => [
				"user_id" => $user_id,
				"order_status" => $set_filter_status_order,
                "order_status_shipping" => $set_filter_status_shipping,
                "order_status_payment" => $set_filter_status_payment
			],
			"ORDER" => [
                $sort['key'] => $sort['direction']
			]
		]);
		
	} else if($user == 'all') {

		$orders = $db_content->select("fc_orders", "*", [
			"AND" => [
                "order_status" => $set_filter_status_order,
                "order_status_shipping" => $set_filter_status_shipping,
                "order_status_payment" => $set_filter_status_payment
			],
			"ORDER" => [
                $sort['key'] => $sort['direction']
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