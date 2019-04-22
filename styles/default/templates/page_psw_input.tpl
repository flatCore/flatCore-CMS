<fieldset>
	<legend>{$label_psw_protected_page}</legend>
	<form class="form" action="{$formaction}" method="POST">
		<div class="row">
			<div class="col-md-9">
				<input type="password" name="page_psw" class="form-control">
			</div>
			<div class="col-md-3">
				<input type="submit" name="send" class="btn btn-success btn-block" value="{$button_send}">
			</div>
		</div>
	</form>
</fieldset>