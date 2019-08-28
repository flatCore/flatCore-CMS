{nocache}
<h3>{$headline_searchresults} <small>({$search_string})</small></h3>
<p>{$msg_searchresults}</p>

<ol id="searchlist">
{foreach item=link from=$arr_results}
	<li><a href="{$link.set_link}" title="{$link.page_title}">{$link.page_title}</a><br>
	<span>{$link.page_meta_description}</span></li>
{/foreach}
</ol>
{/nocache}