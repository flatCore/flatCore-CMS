{nocache}
<div class="{$msg_status,nocache}">
	{$register_message,nocache}
</div>


<h2>{$lang_headline_editprofile} ({$user_nick})</h2>

<form class="form-horizontal" id="pd_form" action="{$form_url}" method="POST">
<fieldset>
	<legend>{$lang_legend_adress_fields}</legend>

	<div class="form-group">
	<label class="col-sm-2 control-label">{$lang_label_firstname}</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" value="{$get_firstname}" name="s_firstname">
		</div>
	</div>

	<div class="form-group">
	<label class="col-sm-2 control-label">{$lang_label_lastname}</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" value="{$get_lastname}" name="s_lastname">
		</div>
	</div>
	
	<div class="form-group">
	<label class="col-sm-2 control-label">{$lang_label_street}</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" value="{$get_street}" name="s_street">
		</div>
	</div>
	
	<div class="form-group">
	<label class="col-sm-2 control-label">{$lang_label_nr}</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" value="{$get_nr}" name="s_nr">
		</div>
	</div>
	
	<div class="form-group">
	<label class="col-sm-2 control-label">{$lang_label_zip}</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" value="{$get_zip}" name="s_zip">
		</div>
	</div>
	
	<div class="form-group">
	<label class="col-sm-2 control-label">{$lang_label_town}</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" value="{$get_city}" name="s_city">
		</div>
	</div>
	
	<div class="form-group">
	<label class="col-sm-2 control-label">{$lang_label_about_you}</label>
		<div class="col-sm-10">
			<textarea class="form-control" rows="4" name="about_you">{$send_about}</textarea>
		</div>
	</div>
		
</fieldset>


<fieldset>
	<legend>{$lang_legend_access_data}</legend>

	<div class="form-group">
		<label class="col-sm-2 control-label">{$lang_label_psw}</label>
			<div class="col-sm-10">
			<input type="password" class="form-control" value="" name="s_psw">
		</div>
	</div>

	<div class="form-group">
	<label class="col-sm-2 control-label">{$lang_label_psw_repeat}</label>
		<div class="col-sm-10">
			<input type="password" class="form-control" value="" name="s_psw_repeat">
			<p class="help-block">{$msg_edit_psw}</p>
		</div>
	</div>
		
	<hr>

	<div class="form-group">
	<label class="col-sm-2 control-label">{$lang_label_mail}</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" value="" name="s_mail">
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label">{$lang_label_mailrepeat}</label>
		<div class="col-sm-10">	
			<input type="text" class="form-control" value="" name="s_mailrepeat">
			<p class="help-block">{$msg_edit_mail}</p>
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-2 control-label"> </label>
			<div class="col-sm-10">	
				<input class="btn btn-success" type="submit" name="update_profile" value="{$lang_button_save}">
			</div>
	</div>
	
</fieldset>
</form>






<!-- Avatar -->

<div class="well">

<form id="profileform" action="{$form_url}" method="post" enctype="multipart/form-data">


<fieldset>
	<legend>{$lang_legend_avatar}</legend>
	<div style="width:100px; height:100px; float:right; background: #FFF url({$avatar_url}) center center no-repeat;">
	</div>

	<p>{$lang_msg_avatar}</p>

	<input name="avatar" type="file" size="50"><br />
	

	<div class="btn-group">
	
	<input class="btn btn-success btn-small" type="submit" name="upload_avatar" value="{$lang_button_save}">
	
	{if isset($link_avatar_delete_url)}
      <a class="btn btn-danger btn-small" href="{$link_avatar_delete_url}">{$link_avatar_delete_text}</a>
  {/if}
	
	
	</div>

</fieldset>

</form>

</div>




<!-- Delete Account -->

<div class="alert alert-danger">

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