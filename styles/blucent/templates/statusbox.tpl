<div class="brightBox">

<p>
{$status_msg} <b>{$smarty.session.user_nick}</b>
</p>

<ul class="nav nav-tabs nav-stacked">
	<li><a href="{$link_profile}"><i class="icon-user"></i> {$lang_button_profile}</a></li>
	<li><a href="{$link_logout}"><i class="icon-remove"></i> {$lang_button_logout}</a></li>



{if $smarty.session.user_class == "administrator"}
	<li><a href="{$link_acp}"><i class="icon-cog"></i>  {$lang_button_acp}</a></li>
	<li><a href="{$link_edit_page}"><i class="icon-edit"></i>  {$lang_button_edit_page}</a></li>
{/if}

</ul>

</div>

