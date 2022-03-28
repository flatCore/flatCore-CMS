<form action="{form_action}" method="POST" class="d-inline">
	<button class="btn btn-success" name="add_to_cart" value="{post_id}">{btn_add_to_cart}</button>
	<input type="hidden" name="csrf_token" value="{csrf_token}">
</form>

<!--
<button class="btn btn-outline-primary" name="add_to_cart" onclick="add_to_cart(this.value)" value="{post_id}">
	<i class="bi bi-cart"></i> {btn_add_to_cart}
</button>
-->