{nocache}
<div class="well well-sm">
	<p>{$status_msg} <b>{$smarty.session.user_nick}</b></p>
	
	<div class="list-group" style="margin-bottom:0;">
		<a href="{$link_profile}" class="list-group-item">{$lang_button_profile}</a>
		
		{if orders_uri != ''}
			<a href="{$orders_uri}" class="list-group-item">{$lang_button_orders}</a>
		{/if}
		
		<a href="{$link_logout}" class="list-group-item">{$lang_button_logout}</a>
	
		{if $smarty.session.user_class == "administrator"}
		<a href="{$link_acp}" class="list-group-item">{$lang_button_acp}</a>
		<hr>
		<form action="/acp/acp.php?tn=pages&sub=edit" method="POST">
			<div class="d-grid gap-2">
				<button name="editpage" value="{$page_id}" class="btn btn-secondary btn-block">{$lang_button_edit_page}</button>
				<input type="hidden" name="csrf_token" value="{$smarty.session.token}">
			</div>
		</form>
		{/if}
		
	</div>
</div>
{/nocache}