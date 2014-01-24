<div id="toc">
	<h2>{$legend_toc}</h2>
	<ul id="toclist">
	{foreach item=subnav from=$arr_submenue}
		<li><a class="{$subnav.link_status}" href="{$subnav.sublink}" title="{$subnav.page_title}">{$subnav.page_linkname}</a></li>
	{/foreach}
	</ul>
	<p class="boxShadow"></p>
</div>

