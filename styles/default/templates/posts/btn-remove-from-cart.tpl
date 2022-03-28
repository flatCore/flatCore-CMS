<!--
	<form action="{form_action_remove}" class="d-inline carty-form" method="POST">
	<button type="submit" name="carty_remove" value="{remove_id}" class="btn btn-light btn-sm carty-remove">X</button>
</form>
-->

<button class="btn btn-outline-primary" name="remove_from_cart" onclick="remove_from_cart(this.value)" value="{post_id}">
	<i class="bi bi-remove"></i>
</button>