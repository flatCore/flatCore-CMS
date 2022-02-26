<?php

error_reporting(E_ALL ^E_NOTICE);

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



?>