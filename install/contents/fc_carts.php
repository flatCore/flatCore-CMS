<?php
	
/**
 * database for shopping carts
 *
 * user_id for registered users
 * user_hash for visitors (we use $_SESSION['visitor_csrf_token'])
 *
 */
 
$database = 'content';
$table_name = 'fc_carts';

$cols = array(
  "cart_id"  => 'INTEGER(12) NOT NULL PRIMARY KEY AUTO_INCREMENT',
  "cart_time"  => 'INTEGER(12)',
  "cart_user_hash" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "cart_user_id" => 'INTEGER(12)',
  "cart_product_id"  => 'INTEGER(12)',
  "cart_product_amount"  => 'INTEGER(12)',
  "cart_product_tax"  => 'INTEGER(12)',
  "cart_product_price_net"  => "VARCHAR(100) NOT NULL DEFAULT ''",
  "cart_product_title"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "cart_product_number"  => "VARCHAR(100) NOT NULL DEFAULT ''",
  "cart_status"  => "VARCHAR(50) NOT NULL DEFAULT ''"
  );

?>
