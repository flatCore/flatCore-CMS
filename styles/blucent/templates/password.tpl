<div class="{$msg_status}">
	{$psw_message}
</div>

<h3>{$forgotten_psw}</h3>

<p>{$forgotten_psw_intro}</p>

<form class="form-horizontal" action="{$form_url}" method="POST">
<fieldset>
	<legend>{$legend_ask_for_psw}</legend>
	
		<div class="control-group">
			<label class="control-label">{$label_mail}</label>
			<div class="controls">
				<input type="text" class="span5" name="mail">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label"></label>
			<div class="controls">
				<input class="btn btn-success" type="submit" name="ask_for_psw" value="{$button_send}">
			</div>
		</div>

</fieldset>
</form>