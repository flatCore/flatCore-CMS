{if is_array($arr_submenue) }
<h2>{$legend_toc}</h2>
<div class="list-group">
	{foreach item=subnav from=$arr_submenue}
		<a class="list-group-item list-group-item-action {$subnav.link_status} {$subnav.page_hash}" href="{$subnav.sublink}" target="{$subnav.page_target}" title="{$subnav.page_title}">{$subnav.page_linkname}</a>
	{/foreach}
	<hr class="shadow">
</div>
{/if}