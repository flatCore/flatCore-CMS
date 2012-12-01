<h2>{$legend_register}</h2>

<div class="{$msg_status}">
{$register_message}
</div>

<p>{$msg_register_intro}</p>

<form class="form-horizontal" id="pd_form" action="{$form_url}" method="POST">

<fieldset>
	<legend>{$legend_required_fields}</legend>

		<div class="control-group">
			<label class="control-label">{$label_username}</label>
			<div class="controls">
				<input type="text" class="span5" value="{$send_username}" name="username">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label">{$label_mail}</label>
			<div class="controls">
				<input type="text" class="span5" value="{$send_mail}" name="mail">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label">{$label_mailrepeat}</label>
			<div class="controls">
				<input type="text" class="span5" value="{$send_mailrepeat}" name="mailrepeat">
			</div>
		</div>	

		<div class="control-group">
			<label class="control-label">{$label_psw}</label>
			<div class="controls">
				<input type="password" class="span5" name="psw">
			</div>
		</div>	

		<div class="control-group">
			<label class="control-label">{$label_psw_repeat}</label>
			<div class="controls">
				<input type="password" class="span5" name="psw_repeat">
			</div>
		</div>	


	</fieldset>


<fieldset>

	<legend>{$legend_optional_fields}</legend>

		<div class="control-group">
			<label class="control-label">{$label_firstname}</label>
			<div class="controls">
				<input type="text" class="span5" value="{$send_firstname}" name="firstname">
			</div>
		</div>	
		
		<div class="control-group">		
		<label class="control-label">{$label_lastname}</label>
		<div class="controls">
		<input type="text" class="span5" value="{$send_name}" name="name">
			</div>
		</div>	
				
		<div class="control-group">
		<label class="control-label">{$label_street}</label>
		<div class="controls">
		<input type="text" class="span5" value="{$send_street}" name="street">
			</div>
		</div>
				
		<div class="control-group">
		<label class="control-label">{$label_nr}</label>
		<div class="controls">
		<input type="text" class="span5" value="{$send_nr}" name="nr">
			</div>
		</div>
		
		<div class="control-group">
		<label class="control-label">{$label_zip}</label>
		<div class="controls">
		<input type="text" class="span5" value="{$send_zip}" name="zip">
			</div>
		</div>	
			
		<div class="control-group">
		<label class="control-label">{$label_town}</label>
		<div class="controls">
		<input type="text" class="span5" value="{$send_city}" name="city">
			</div>
		</div>	
				
		<div class="control-group">
		<label class="control-label">{$label_about_you}</label>
		<div class="controls">
		<textarea name="about_you" class="span5">{$send_about}</textarea>
			</div>
		</div>
		
	
</fieldset>

	<div class="form-actions">
		
		<div class="scrollBox">{$agreement_text}</div>
			
			<div class="alert alert-info">
			<input type="checkbox" name="accept_terms" style="float:left;">
			<p style="padding: 0 0 0 25px;margin:0;">{$msg_register_outro}</p>
			</div>
		
		
		<input class="btn btn-success" type="submit" name="send_registerform" value="{$button_send_register}">
		
		</div>




</form>