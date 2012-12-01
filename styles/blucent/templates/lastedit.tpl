

<h6>{$legend_lastedit}</h6>

<ul class="arrow_list">
{foreach item=nav from=$arr_lastedit}

	<li>
	<a href="{$nav.link}" title="{$nav.page_title}">{$nav.page_linkname}</a>
	</li>
	
	
	
{/foreach}

</ul>

