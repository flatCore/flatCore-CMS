<div class="{$msg_status}">
	{$psw_message}
</div>

<h3>{$forgotten_psw}</h3>

<p class="lead">{$forgotten_psw_intro}</p>

<form class="form-horizontal" action="{$form_url}" method="POST">
	<fieldset>
		<legend>{$legend_ask_for_psw}</legend>
		
			<div class="well">
				<div class="form-group">
					<label class="col-sm-3 control-label">{$label_mail}</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="mail">
					</div>
				</div>	
		
				<div class="form-group">
					<label class="col-sm-3 control-label"></label>
					<div class="col-sm-9">
						<input class="btn btn-success" type="submit" name="ask_for_psw" value="{$button_send}">
					</div>
				</div>
			</div>
	
	</fieldset>
</form>