
<h2>{$lang_label_orders}</h2>


<table class="table">
	<tr>
		<td>#</td>
		<td>{$lang_label_date}</td>
		<td>{$lang_order_status}</td>
		<td>{$lang_label_price}</td>
	</tr>
	
	{foreach $orders as $order}
		<tr>
			<td><a data-bs-toggle="collapse" href="#show{$order.nbr}">{$order.nbr}</a></td>
			<td>{$order.date}</td>
			<td>
				{if $order.status_payment == '1'}
					{$lang_status_payment_open}<br>
					{else}
					<span class="text-success">{$lang_status_payment_paid}</span><br>
				{/if}
				{if $order.status_shipping == '1'}
					{$lang_status_shipping_open}
				{else}
					<span class="text-success">{$lang_status_shipping_done}</span>
				{/if}
			</td>
			<td>{$order.price} {$order.currency}</td>
		</tr>
		<tr>
			<td colspan="4">
				<div class="collapse" id="show{$order.nbr}">
					<div class="card bg-light p-3">
						<table class="table table-sm">
							
							{foreach $order.products as $product}
								<tr>
									<td>{$product.product_nbr}</td>
									<td>{$product.title}</td>
									<td>{$product.amount}</td>
									<td>{$product.price_gross} {$order.currency}</td>
									<td class="text-end">
										
										{if $product.dl_file != '' AND $order.status_payment == '2'}
											<form action="{$order_page_uri}" method="POST" class="d-inline">
												<button class="btn btn-primary" type="submit" name="dl_p_file" value="{$product.post_id}"><i class="bi bi-download"></i> DOWNLOAD</button>
												<input type="hidden" name="order_id" value="{$order.nbr}">
											</form>
										{/if}
										{if $product.dl_file_ext != '' AND $order.status_payment == '2'}
											<form action="{$order_page_uri}" method="POST" class="d-inline">
												<button class="btn btn-primary" type="submit" name="dl_p_file_ext" value="{$product.post_id}"><i class="bi bi-cloud-download"></i> DOWNLOAD</button>
												<input type="hidden" name="order_id" value="{$order.nbr}">
											</form>
										{/if}
										
									</td>
								</tr>
							{/foreach}
						</table>
					</div>
				</div>
			</td>
		</tr>
	{/foreach}
</table>