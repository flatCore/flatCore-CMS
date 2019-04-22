<h6>{$lang_legend_lastedit}</h6>

<ul class="list-unstyled">
{foreach item=nav from=$arr_lastedit}
	<li><span class="glyphicon glyphicon-chevron-right"></span> <a href="{$nav.link}" title="{$nav.page_title}">{$nav.page_linkname}</a></li>
{/foreach}
</ul>

