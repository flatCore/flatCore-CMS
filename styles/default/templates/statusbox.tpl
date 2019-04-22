{nocache}
<div class="well well-sm">
	<p>{$status_msg} <b>{$smarty.session.user_nick}</b></p>
	
	<div class="list-group" style="margin-bottom:0;">
		<a href="{$link_profile}" class="list-group-item">{$lang_button_profile}</a>
		<a href="{$link_logout}" class="list-group-item">{$lang_button_logout}</a>
	
		{if $smarty.session.user_class == "administrator"}
		<a href="{$link_acp}" class="list-group-item">{$lang_button_acp}</a>
		<a href="{$link_edit_page}" class="list-group-item">{$lang_button_edit_page}</a>
		{/if}
		
	</div>
</div>
{/nocache}