{nocache}
<h3>{$headline_searchresults} <small>({$search_string})</small></h3>
<p>{$msg_searchresults}</p>

<ol id="searchlist">
{foreach item=link from=$arr_results}
	<li>
	<a href="{$link.set_link}" title="{$link.page_title}">{$link.page_title}</a><br>
	<p>{$link.page_meta_description}<br><small class="text-success">{$link.set_link}</small></p>
	</li>
{/foreach}
</ol>
{/nocache}