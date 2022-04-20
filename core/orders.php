<?php
error_reporting(E_ALL ^E_NOTICE);


/* get permalink for orders page */
$order_page = fc_get_type_of_use_pages('orders');
if($order_page['page_permalink'] == '') {
	$order_page_uri = '/orders/';
} else {
	$order_page_uri = '/'.$order_page['page_permalink'];
}
	
$smarty->assign('order_page_uri_uri', $order_page_uri);


/**
 * show orders
 */

/* start purchesed download */
if(isset($_POST['dl_p_file']) OR isset($_POST['dl_p_file_ext'])) {
	
	if(is_numeric($_POST['dl_p_file'])) {
		$post_id = (int) $_POST['dl_p_file'];
		$mode = 'internal_file';
	}
	if(is_numeric($_POST['dl_p_file_ext'])) {
		$post_id = (int) $_POST['dl_p_file_ext'];
		$mode = 'external_file_file';
	}
	
	$this_item = fc_get_post_data($post_id);
	
	if($mode == 'internal_file') {
		$download_file = str_replace('../content/','./content/',$this_item['post_file_attachment']);
		$pathinfo = pathinfo($download_file);
		
		$set_filename = $_POST['order_id'];

		if(is_file($download_file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: ' . mime_content_type($download_file));
			header('Content-Disposition: attachment; filename="'.$set_filename.'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($download_file));
			readfile($download_file);
			exit;
		}	
		
	} else {
		$download = $this_item['post_file_attachment_external'];
		header("Location: $download");
		exit;
	}

}


$user_id = (int) $_SESSION['user_id'];
$order_filter = array();
$order_filter['status_payment'] = [];
$order_filter['status_shipping'] = [];
$order_filter['status_order'] = [];

$order_sort['key'] = '';
$order_sort['direction'] = '';

$get_orders = fc_get_orders($user_id,$order_filter,$order_sort);
$cnt_orders = count($get_orders);

for($i=0;$i<$cnt_orders;$i++) {
	
	$order_item[$i]['nbr'] = $get_orders[$i]['order_nbr'];
	$order_item[$i]['date'] = date("d.m.Y H:i",$get_orders[$i]['order_time']);
	$order_item[$i]['status'] = $get_orders[$i]['order_status'];
	$order_item[$i]['status_payment'] = $get_orders[$i]['order_status_payment'];
    $order_item[$i]['status_shipping'] = $get_orders[$i]['order_status_shipping'];
    $order_item[$i]['currency'] = $get_orders[$i]['order_currency'];

	$order_item[$i]['price'] = fc_post_print_currency($get_orders[$i]['order_price_total']);
	
	$order_products = json_decode($get_orders[$i]['order_products'],true);
    $cnt_order_products = 0;
    if(is_array($order_products)) {
	    $cnt_order_products = count($order_products);
    }
	//print_r($order_products);
	
	$products_str = '';
	
	/* loop through purchased items */
	for($x=0;$x<$cnt_order_products;$x++) {
		unset($this_item);
		$post_id = $order_products[$x]['post_id'];
		$this_item = fc_get_post_data($post_id);
		
		
		$this_item_price_gross = fc_post_print_currency($order_products[$x]['price_gross_raw']);
		
		$products[$x]['title'] = $order_products[$x]['title'];
		$products[$x]['product_nbr'] = $order_products[$x]['product_number'];
        $products[$x]['amount'] = $order_products[$x]['amount'];
		$products[$x]['price_gross'] = $this_item_price_gross;
		$products[$x]['post_id'] = $post_id;
				
		// check if this item has an attachment
		$items_download = $this_item['post_file_attachment'];
		$items_download_external = $this_item['post_file_attachment_external'];
		
		if($items_download != '') {
			$products[$x]['dl_file'] = $items_download;
			
		}
		if($items_download_external != '') {
			$products[$x]['dl_file_ext'] = $items_download_external;
		}

		
	}
	
	$order_item[$i]['products'] = $products;
		
	//$order_item[$i]['products'] = $products_str;
		
}

$smarty->assign('order_page_uri', $order_page_uri);
$smarty->assign('orders', $order_item);


$orders_table = $smarty->fetch("orders.tpl",$cache_id);

$smarty->assign('page_content', $orders_table, true);

?>