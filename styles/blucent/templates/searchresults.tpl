<h3>{$headline_searchresults}</h3>

<p>
{$msg_searchresults}
</p>



<ol id="searchlist">
{foreach item=link from=$arr_results}


	<li>
	<a href="{$link.set_link}" title="{$link.page_title}">{$link.page_title}</a><br />
	{$link.page_meta_description}<br />
	</li>

{/foreach}

</ul>