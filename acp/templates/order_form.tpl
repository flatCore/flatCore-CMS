<form action="{form_action}" method="POST">
	
	<div class="card">
		<div class="card-header position-relative"># {order_nbr} <span class="float-end badge rounded-pill bg-light text-dark">{order_price_total}</span></div>
		<div class="card-body">
			{edit_order_msg}
			<div class="row">
				<div class="col-md-6">
					
					<div class="mb-3">
						<label for="orderStatus" class="form-label">Order Status</label>
						<select name="status_order" class="form-control">
							<option value="1" {sel_so1}>processing</option>
							<option value="2" {sel_so2}>done</option>
							<option value="3" {sel_so3}>canceled</option>
						</select>
					</div>
					
					<div class="mb-3">
						<label for="paymentStatus" class="form-label">Payment Status</label>
						<select name="status_payment" class="form-control">
							<option value="1" {sel_sp1}>open</option>
							<option value="2" {sel_sp2}>paid</option>
						</select>
					</div>
					
					<div class="mb-3">
						<label for="shippingStatus" class="form-label">Shipping Status</label>
						<select name="status_shipping" class="form-control">
							<option value="1" {sel_ss1}>open</option>
							<option value="2" {sel_ss2}>done</option>
						</select>
					</div>
				
				</div>
				<div class="col-md-6">
					<textarea class="form-control" rows="4" name="invoice_address">{invoice_address}</textarea>
				</div>		
			</div>
			
			<div class="card p-2">
				{products_list}
			</div>
		
		</div>
			
		<div class="card-footer">
			<button type="submit" name="update_order" class="btn btn-success">{btn_update}</button>
			<button type="submit" name="send_order_mail" class="btn btn-primary">{send_order_mail}</button>
		</div>
	</div>
	{hidden_csrf}
	<input type="hidden" name="open_order" value="{order_id}">
</form>