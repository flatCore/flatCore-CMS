<div id="cart-container" class="carty-hide">
	<div class="row">
		<div class="col-1">
			<a href="javascript:;" class="toggleCart btn btn-light btn-block">X</a>
		</div>
		<div class="col-11">
			{cart_message}
		</div>
	</div>
	
	<script type="text/javascript">
   	show_cart();
	</script>
	
	<div id="cart-container-inner">
		<div class="cart-list">
			{cart_list}			
		</div>
		<div class="cart-form">
			{form_message}
			{form}
		</div>
	</div>
</div>
<div id="cart-buttons">
	<a href="javascript:;" class="btn btn-primary toggleCart">Merkliste anzeigen <span class="badge bg-secondary">{cart_list_cnt}</span></a>
</div>