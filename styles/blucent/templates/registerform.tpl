{nocache}
<h2>{$lang_legend_register}</h2>
<div class="{$msg_status}">
{$register_message}
</div>

<p>{$lang_msg_register_intro}</p>

<form class="form-horizontal" id="pd_form" action="{$form_url}" method="POST">

<fieldset>
	<legend>{$lang_legend_required_fields}</legend>

		<div class="form-group">
		<label class="col-sm-2 control-label">{$lang_label_username}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" value="{$send_username}" name="username">
			</div>
		</div>
		
		<div class="form-group">
			<label class="col-sm-2 control-label">{$lang_label_mail}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" value="{$send_mail}" name="mail">
			</div>
		</div>
		
		<div class="form-group">
			<label class="col-sm-2 control-label">{$lang_label_mailrepeat}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" value="{$send_mailrepeat}" name="mailrepeat">
			</div>
		</div>	

		<div class="form-group">
			<label class="col-sm-2 control-label">{$lang_label_psw}</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" name="psw">
			</div>
		</div>	

		<div class="form-group">
			<label class="col-sm-2 control-label">{$lang_label_psw_repeat}</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" name="psw_repeat">
			</div>
		</div>	


	</fieldset>


<fieldset>

	<legend>{$lang_legend_optional_fields}</legend>

		<div class="form-group">
			<label class="col-sm-2 control-label">{$lang_label_firstname}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" value="{$send_firstname}" name="firstname">
			</div>
		</div>	
		
		<div class="form-group">		
		<label class="col-sm-2 control-label">{$lang_label_lastname}</label>
		<div class="col-sm-10">
		<input type="text" class="form-control" value="{$send_name}" name="name">
			</div>
		</div>	
				
		<div class="form-group">
		<label class="col-sm-2 control-label">{$lang_label_street}</label>
		<div class="col-sm-10">
		<input type="text" class="form-control" value="{$send_street}" name="street">
			</div>
		</div>
				
		<div class="form-group">
		<label class="col-sm-2 control-label">{$lang_label_nr}</label>
		<div class="col-sm-10">
		<input type="text" class="form-control" value="{$send_nr}" name="nr">
			</div>
		</div>
		
		<div class="form-group">
		<label class="col-sm-2 control-label">{$lang_label_zip}</label>
		<div class="col-sm-10">
		<input type="text" class="form-control" value="{$send_zip}" name="zip">
			</div>
		</div>	
			
		<div class="form-group">
		<label class="col-sm-2 control-label">{$lang_label_town}</label>
		<div class="col-sm-10">
		<input type="text" class="form-control" value="{$send_city}" name="city">
			</div>
		</div>	
				
		<div class="form-group">
		<label class="col-sm-2 control-label">{$lang_label_about_you}</label>
		<div class="col-sm-10">
		<textarea name="about_you" class="form-control">{$send_about}</textarea>
			</div>
		</div>
		
	
</fieldset>

	<div class="well well-sm">
		
		<div class="scrollBox">{$agreement_text}</div>
			
			<div class="alert alert-info">
			<input type="checkbox" name="accept_terms" style="float:left;">
			<p style="padding: 0 0 0 25px;margin:0;">{$lang_msg_register_outro}</p>
			</div>
		
		
		<input class="btn btn-success" type="submit" name="send_registerform" value="{$lang_button_send_register}">
		
		</div>

</form>
{/nocache}