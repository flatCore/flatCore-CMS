<div class="{$msg_status}">
	{$psw_message}
</div>

<h3>{$forgotten_psw}</h3>

<p class="lead">{$forgotten_psw_intro}</p>

<form class="form" action="{$form_url}" method="POST">
	<fieldset>
		<legend>{$legend_ask_for_psw}</legend>
		
			<div class="well">
				<div class="form-group">
					<label>{$label_mail}</label>
					<input type="text" class="form-control" name="mail">
				</div>	
		
				<input class="btn btn-success" type="submit" name="ask_for_psw" value="{$button_send}">
			</div>
	
	</fieldset>
</form>