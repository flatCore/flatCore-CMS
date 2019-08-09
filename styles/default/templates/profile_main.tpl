{nocache}
<div class="{$msg_status}">
	{$register_message}
</div>


<h2>{$lang_headline_editprofile} ({$user_nick})</h2>

<form class="form-horizontal" id="pd_form" action="{$form_url}" method="POST">
<fieldset>
	<legend>{$lang_legend_adress_fields}</legend>

	<div class="form-group">
		<label for="firstname">{$lang_label_firstname}</label>
		<input type="text" class="form-control" id="firstname" value="{$get_firstname}" name="s_firstname">
	</div>

	<div class="form-group">
		<label for="lastname">{$lang_label_lastname}</label>
		<input type="text" class="form-control" id="lastname" value="{$get_lastname}" name="s_lastname">
	</div>
	
	<div class="form-group">
		<label for="street">{$lang_label_street}</label>
		<input type="text" class="form-control" id="street" value="{$get_street}" name="s_street">
	</div>
	
	<div class="form-group">
		<label for="streetnbr">{$lang_label_nr}</label>
		<input type="text" class="form-control" id="streetnbr" value="{$get_nr}" name="s_nr">
	</div>
	
	<div class="form-group">
		<label for="zip">{$lang_label_zip}</label>
		<input type="text" class="form-control" id="zip" value="{$get_zip}" name="s_zip">
	</div>
	
	<div class="form-group">
		<label for="city">{$lang_label_town}</label>
		<input type="text" class="form-control" id="city" value="{$get_city}" name="s_city">
	</div>
	
	<div class="form-group">
		<label for="about">{$lang_label_about_you}</label>
		<textarea class="form-control" id="about" rows="4" name="about_you">{$send_about}</textarea>
	</div>
		
</fieldset>


<fieldset>
	<legend>{$lang_legend_access_data}</legend>

	<div class="form-group">
		<label for="psw">{$lang_label_psw}</label>
		<input type="password" class="form-control" id="psw" value="" name="s_psw">
	</div>

	<div class="form-group">
		<label for="psw_repeat">{$lang_label_psw_repeat}</label>
		<input type="password" class="form-control" id="psw_repeat" value="" name="s_psw_repeat">
		<p class="help-block">{$msg_edit_psw}</p>
	</div>
		
	<hr>

	<div class="form-group">
		<label for="mail">{$lang_label_mail}</label>
		<input type="text" class="form-control" id="mail" value="" name="s_mail">
	</div>

	<div class="form-group">
		<label for="mail_repeat">{$lang_label_mailrepeat}</label>
		<input type="text" class="form-control" id="mail_repeat" value="" name="s_mailrepeat">
		<p class="help-block">{$msg_edit_mail}</p>
	</div>
	
	<input class="btn btn-success" type="submit" name="update_profile" value="{$lang_button_save}">
	
</fieldset>
</form>


<hr>



<!-- Avatar -->

<div class="well">

<form id="profileform" action="{$form_url}" method="post" enctype="multipart/form-data">


<fieldset>
	<legend>{$lang_legend_avatar}</legend>
	
	<div class="row">
		<div class="col-8">
			<p>{$lang_msg_avatar}</p>
			<input name="avatar" type="file" size="50">
			<hr>
			<div class="btn-group">
				<input class="btn btn-success btn-small" type="submit" name="upload_avatar" value="{$lang_button_save}">
				{if isset($link_avatar_delete_url)}
					<a class="btn btn-danger btn-small" href="{$link_avatar_delete_url}">{$link_avatar_delete_text}</a>
				{/if}
			</div>
			
		</div>
		<div class="col-4">
			<img src="{$avatar_url}" alt="" title="">
		</div>
	</div>
	

</fieldset>

</form>

</div>


<hr>

<!-- Delete Account -->

<div class="alert alert-danger mt-3">

<form id="profileform" action="{$form_url}" method="POST">
<fieldset>
<legend>{$lang_legend_delete_account}</legend>
<p>
{$lang_msg_delete_account}
</p>

<input class="btn btn-danger btn-small" type="submit" onclick="return confirm('{$msg_confirm_delete_account}')" name="delete_my_account" value="{$lang_button_delete}">
</fieldset>
</form>

</div>
{/nocache}