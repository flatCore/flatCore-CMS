

<h6>{$legend_mostclicked}</h6>

<ul class="arrow_list">
{foreach item=nav from=$arr_mostclicked}
	<li>
	<a href="{$nav.link}" title="{$nav.pagetitle}">{$nav.linkname}</a>
	</li>
{/foreach}

</ul>

