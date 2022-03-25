<?php
	
/**
 * database for orders
 *
 * user_id - user id of the client
 * order_time timestring
 * order_status [1 = order is processing] [2 = order is completed] [3 = order is canceled]
 * order_status_payment [1 = payment is not done] [2 = payment is done]
 * order_status_shipping [1 = shipping is not done] [2 = shipping is done]
 *
 * order_products JSON String from products id, title, amount, price_net, tax
 *
 */
 
$database = 'content';
$table_name = 'fc_orders';

$cols = array(
  "id"  => 'INTEGER(12) NOT NULL PRIMARY KEY AUTO_INCREMENT',
  "user_id"  => 'INTEGER(12)',
  "order_nbr"  => "VARCHAR(25) NOT NULL DEFAULT ''",
  "order_time"  => 'INTEGER(12)',
  "order_invoice_address"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "order_status"  => 'INTEGER(12)',
  "order_status_shipping"  => 'INTEGER(12)',
  "order_status_payment"  => 'INTEGER(12)',
  "order_currency"  => "VARCHAR(100) NOT NULL DEFAULT ''",
  "order_price_total"  => "VARCHAR(100) NOT NULL DEFAULT ''",
  "order_products" => "LONGTEXT NOT NULL DEFAULT ''",
  "order_shipping_type"  => "VARCHAR(100) NOT NULL DEFAULT ''",
  "order_shipping_costs"  => "VARCHAR(100) NOT NULL DEFAULT ''",
  "order_payment_type"  => "VARCHAR(100) NOT NULL DEFAULT ''",
  "order_payment_costs"  => "VARCHAR(100) NOT NULL DEFAULT ''"
  );

?>
