

<h6>{$lang_legend_mostclicked}</h6>

<ul class="list-unstyled">
{foreach item=nav from=$arr_mostclicked}
	<li><span class="glyphicon glyphicon-chevron-right"></span> <a href="{$nav.link}" title="{$nav.pagetitle}">{$nav.linkname}</a></li>
{/foreach}
</ul>

