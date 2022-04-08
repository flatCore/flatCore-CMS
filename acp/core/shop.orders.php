<?php
error_reporting(E_ALL ^E_NOTICE);
//prohibit unauthorized access
require 'core/access.php';

if(isset($_POST['change_order_status'])) {
    $order_id = (int) $_POST['id'];
    $order_status = (int) $_POST['change_order_status'];
    $update = $db_content->update("fc_orders", [
        "order_status" => $order_status
    ],[
        "id" => $order_id
    ]);
}

if(isset($_POST['change_status_payment'])) {
    $order_id = (int) $_POST['id'];
    $payment_status = (int) $_POST['change_status_payment'];
    $update = $db_content->update("fc_orders", [
        "order_status_payment" => $payment_status
    ],[
        "id" => $order_id
    ]);
}

if(isset($_POST['send_order_mail'])) {


    $mail_data['tpl'] = 'send-order-status.tpl';

    $build_html_mail = fc_build_html_file($mail_data);

    $recipient['name'] = $fc_preferences['prefs_mailer_name'];
    $recipient['mail'] = $fc_preferences['prefs_mailer_adr'];
    $subject = "Sending order status";

    //echo '<textarea class="form-control" row="8">'.$build_html_mail.'</textarea>';


    $send_mail = fc_send_mail($recipient,$subject,$build_html_mail);

    if($send_mail == 1) {
        echo '<div class="text-success">GESENDET</div>';
    } else {
        echo '<div class="text-danger">'.$send_mail.'</div>';
    }


}


$orders = fc_get_orders('all','all');
$cnt_orders = count($orders);

echo '<div class="subHeader">';
echo $lang['nav_orders'] .' '. $cnt_orders;
echo '</div>';

// reset message
$edit_order_msg = '';

if(isset($_POST['update_order'])) {

    $order_data = $_POST;

    $update_order = fc_update_order($order_data);

    if($update_order == 1) {
        $edit_order_msg = '<div class="text-success">'.$lang['db_changed'].'</div>';
    }

}


echo '<div class="app-container">';
echo '<div class="max-height-container">';

echo '<div class="row">';
echo '<div class="col-md-9">';

echo '<div class="card p-3">';

echo '<div class="scroll-box">';

/* open/edit order */

if(is_numeric($_POST['open_order'])) {
    $get_order_id = (int) $_POST['open_order'];
    $get_order = fc_get_order_details($get_order_id);

    $order_form_tpl = file_get_contents('templates/order_form.tpl');

    $order_invoice_address = html_entity_decode($get_order['order_invoice_address']);
    $order_invoice_address = str_replace("<br>", "\n", $order_invoice_address);

    /* order status */
    $sel_so = array();
    $sel_so[1] = '';
    $sel_so[2] = '';
    $sel_so[3] = '';
    $selected_status_order = $get_order['order_status'];
    $sel_so[$selected_status_order] = 'selected';
    $order_form_tpl = str_replace('{sel_so1}', $sel_so[1], $order_form_tpl);
    $order_form_tpl = str_replace('{sel_so2}', $sel_so[2], $order_form_tpl);
    $order_form_tpl = str_replace('{sel_so3}', $sel_so[3], $order_form_tpl);

    /* payment status */
    $sel_sp = array();
    $sel_sp[1] = '';
    $sel_sp[2] = '';
    $selected_payment = $get_order['order_status_payment'];
    $sel_sp[$selected_payment] = 'selected';
    $order_form_tpl = str_replace('{sel_sp1}', $sel_sp[1], $order_form_tpl);
    $order_form_tpl = str_replace('{sel_sp2}', $sel_sp[2], $order_form_tpl);

    /* shipping status */
    $sel_ss = array();
    $sel_ss[1] = '';
    $sel_ss[2] = '';
    $selected_shipping = $get_order['order_status_shipping'];
    $sel_ss[$selected_shipping] = 'selected';
    $order_form_tpl = str_replace('{sel_ss1}', $sel_ss[1], $order_form_tpl);
    $order_form_tpl = str_replace('{sel_ss2}', $sel_ss[2], $order_form_tpl);


    $order_products = json_decode($get_order['order_products'],true);
    $cnt_order_products = count($order_products);

    $products_str = '<table class="table table-sm">';
    for($i=0;$i<$cnt_order_products;$i++) {
        $products_str .= '<tr>';

        $products_str .= '<td>'.$order_products[$i]['product_number'].'</td>';
        $products_str .= '<td>'.$order_products[$i]['title'].'</td>';
        $products_str .= '<td>'.fc_post_print_currency($order_products[$i]['price_net_raw']).'</td>';
        $products_str .= '<td>'.$order_products[$i]['tax'].'</td>';
        $products_str .= '<td>'.fc_post_print_currency($order_products[$i]['price_gross_raw']).'</td>';

        $products_str .= '</tr>';
    }
    $products_str .= '</table>';

    $order_form_tpl = str_replace('{order_nbr}', $get_order['order_nbr'], $order_form_tpl);
    $order_form_tpl = str_replace('{invoice_address}', $order_invoice_address, $order_form_tpl);
    $order_form_tpl = str_replace('{hidden_csrf}', $hidden_csrf_token, $order_form_tpl);
    $order_form_tpl = str_replace('{order_id}', $get_order_id, $order_form_tpl);
    $order_form_tpl = str_replace('{select_payment_status}', $select_status_payment, $order_form_tpl);
    $order_form_tpl = str_replace('{select_order_status}', $select_status_order, $order_form_tpl);
    $order_form_tpl = str_replace('{products_list}', $products_str, $order_form_tpl);
    $order_form_tpl = str_replace('{order_price_total}', fc_post_print_currency($get_order['order_price_total']), $order_form_tpl);
    $order_form_tpl = str_replace('{form_action}', "?tn=shop&sub=orders", $order_form_tpl);
    $order_form_tpl = str_replace('{btn_update}', $lang['update'], $order_form_tpl);
    $order_form_tpl = str_replace('{edit_order_msg}', $edit_order_msg, $order_form_tpl);

    echo $order_form_tpl;

}


/* list orders */

echo '<table class="table table-striped table-hover">';
echo '<tr>';
echo '<td>#</td>';
echo '<td>'.$lang['label_order_nbr'].'</td>';
echo '<td>'.$lang['label_order_date'].'</td>';
echo '<td class="text-end">'.$lang['price_total'].'</td>';
echo '<td>'.$lang['label_status_payment'].'</td>';
echo '<td>'.$lang['label_status_shipping'].'</td>';
echo '<td>'.$lang['label_status_order'].'</td>';
echo '<td></td>';
echo '</tr>';
for($i=0;$i<$cnt_orders;$i++) {

    $order_time = date('d.m.Y H:i',$orders[$i]['order_time']);

    $order_status = $orders[$i]['order_status'];
    $order_status_payment = $orders[$i]['order_status_payment'];
    $order_status_shipping = $orders[$i]['order_status_shipping'];

    /* order status */
    $sel_so = array_fill(0, 3, '');
    $sel_so[$order_status] = 'selected';

    $select_status_order  = '<form action="?tn=shop&sub=orders" method="POST">';
    $select_status_order .= '<select name="change_order_status" class="form-control" onchange="this.form.submit()">';
    $select_status_order .= '<option value="1" '.$sel_so[1].'>'.$lang['status_order_received'].'</option>';
    $select_status_order .= '<option value="2" '.$sel_so[2].'>'.$lang['status_order_completed'].'</option>';
    $select_status_order .= '<option value="3" '.$sel_so[3].'>'.$lang['status_order_canceled'].'</option>';
    $select_status_order .= '</select>';
    $select_status_order .= '<input type="hidden" name="id" value="'.$orders[$i]['id'].'">';
    $select_status_order .= $hidden_csrf_token;
    $select_status_order .= '</form>';

    /* payment status */
    $sel_sp = array_fill(0, 3, '');
    $sel_sp[$order_status_payment] = 'selected';

    $select_status_payment  = '<form action="?tn=shop&sub=orders" method="POST">';
    $select_status_payment .= '<select name="change_status_payment" class="form-control" onchange="this.form.submit()">';
    $select_status_payment .= '<option value="1" '.$sel_sp[1].'>'.$lang['status_payment_open'].'</option>';
    $select_status_payment .= '<option value="2" '.$sel_sp[2].'>'.$lang['status_payment_paid'].'</option>';
    $select_status_payment .= '</select>';
    $select_status_payment .= '<input type="hidden" name="id" value="'.$orders[$i]['id'].'">';
    $select_status_payment .= $hidden_csrf_token;
    $select_status_payment .= '</form>';

    /* shipping status */
    $sel_ss = array_fill(0, 3, '');
    $sel_ss[$order_status_shipping] = 'selected';

    $select_status_shipping  = '<form action="?tn=shop&sub=orders" method="POST">';
    $select_status_shipping .= '<select name="change_status_shipping" class="form-control" onchange="this.form.submit()">';
    $select_status_shipping .= '<option value="1" '.$sel_ss[1].'>'.$lang['status_shipping_prepared'].'</option>';
    $select_status_shipping .= '<option value="2" '.$sel_ss[2].'>'.$lang['status_shipping_shipped'].'</option>';
    $select_status_shipping .= '</select>';
    $select_status_shipping .= '<input type="hidden" name="id" value="'.$orders[$i]['id'].'">';
    $select_status_shipping .= $hidden_csrf_token;
    $select_status_shipping .= '</form>';


    $btn_open_order  = '<form action="?tn=shop&sub=orders" method="POST">';
    $btn_open_order .= '<button type="submit" class="btn btn-fc w-100" name="open_order" value="'.$orders[$i]['id'].'">'.$icon['edit'].'</button>';
    $btn_open_order .= $hidden_csrf_token;
    $btn_open_order .= '</form>';

    echo '<tr>';
    echo '<td>'.$orders[$i]['id'].'</td>';
    echo '<td>'.$orders[$i]['order_nbr'].'</td>';
    echo '<td>'.$order_time.'</td>';
    echo '<td class="text-end">'.fc_post_print_currency($orders[$i]['order_price_total']).'</td>';
    echo '<td>'.$select_status_payment.'</td>';
    echo '<td>'.$select_status_shipping.'</td>';
    echo '<td>'.$select_status_order.'</td>';
    echo '<td>'.$btn_open_order.'</td>';
    echo '</tr>';

}
echo '</table>';


echo '</div>'; // scroll-box
echo '</div>'; // card

echo '</div>'; // col
echo '<div class="col-md-3">';

echo '<div class="card p-3">';

echo 'filter';

echo '</div>'; // card

echo '</div>'; // col
echo '</div>'; // row

echo '</div>'; // max-height-container
echo '</div>'; // app-container