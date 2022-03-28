<p>{$cnt_items} {$lang_label_cnt_sc_items}</p>

{if $cart_alert_error != ''}
	<div class="alert alert-danger">
		{$cart_alert_error}
	</div>
{/if}

{if $cart_alert_success != ''}
	<div class="alert alert-success">
		{$cart_alert_success}
	</div>
{/if}



<table class="table">
	<tr>
		<td>#</td>
		<td>{$lang_label_product_info}</td>
		<td>{$lang_label_product_amount}</td>
		<td class="text-end">{$lang_label_price} <small class="text-muted">{$lang_label_net}</small></td>
		<td class="text-center">{$lang_label_tax}</td>
		<td class="text-end">{$lang_label_price} <small class="text-muted">{$lang_label_gross}</small></td>
		<td></td>
	</tr>
	
	
	{foreach $cart_items as $item}
	<tr>
		<td>{$item.nbr}</td>
		<td><small class="text-muted">{$item.product_number}</small><br>{$item.title}</td>
		<td>{$item.amount}</td>
		<td class="text-end">{$currency} {$item.price_net_format}</td>
		<td class="text-center">{$item.tax} %</td>
		<td class="text-end">{$currency} {$item.price_gross_format}</td>
		<td class="text-center">
			<form action="{$shopping_cart_uri}" method="POST">
				<button type="submit" class="btn btn-link link-danger" name="remove_from_cart" value="{$item.cart_id}"><i class="bi bi-trash"></i></button>
			</form>
		</td>
	</tr>
	{/foreach}

	<tr>
		<td colspan="5" class="text-end">{$lang_price_subtotal}</td>
		<td class="text-end">{$currency} {$cart_price_subtotal}</td>
		<td></td>
	</tr>	
	
	<tr>
		<td colspan="5" class="text-end">{$lang_shipping_costs}</td>
		<td class="text-end">{$currency} {$cart_shipping_costs}</td>
		<td></td>
	</tr>
	
	<tr>
		<td colspan="5" class="">
			<form action="{$shopping_cart_uri}" method="POST" id="set_payment">
				{foreach $payment_methods as $pm}
					<div class="form-check">
						<input type="radio" class="form-check-input" name="set_payment" value="{$pm.key}" id="id_{$pm.key}" autocomplete="off" {$checked_{$pm.key}}>
						<label class="form-check-label" for="id_{$pm.key}">{$pm.title} ({$currency} {$pm.cost})</label>
					</div>
				{/foreach}
			</form>
		</td>
		<td class="text-end">{$currency} {$cart_payment_costs}</td>
		<td></td>
	</tr>
	
	<tr>
		<td colspan="5" class="text-end">{$lang_price_total}</td>
		<td class="text-end">{$currency} {$cart_price_total}</td>
		<td></td>
	</tr>
	
</table>

<div class="row">
	<div class="col-lg-6">

		<div class="card h-100">
			<div class="card-header">{$lang_label_payment_method}</div>
			<div class="card-body">{$payment_message}</div>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="card h-100">
			<div class="card-header">{$lang_label_invoice_address}</div>
			<div class="card-body">{$client_data}</div>
		</div>		
	</div>
</div>

<hr>

<form action="{$shopping_cart_uri}" method="POST">
	<div class="card p-2 mb-4">
		<div class="row">
			<div class="col-md-8">
				<div class="form-check">
					<input class="form-check-input" type="checkbox" name="check_cart_terms" value="check" id="cartTerms">
					<label class="form-check-label" for="cartTerms">
						{$cart_agree_term}
					</label>
				</div>
			</div>
			<div class="col-md-2">
				{$lang_price_total}<br>
				{$currency} {$cart_price_total}
			</div>
			<div class="col-md-2">
				
					<button type="submit" class="btn btn-success w-100" name="order" value="send">KAUFEN</button>
				
			</div>
		</div>
	</div>
</form>

<script type='text/javascript'>

 $(document).ready(function() { 
   $('input[name^=set_payment]').change(function(){
        $('form#set_payment').submit();
   });
  });

</script>
