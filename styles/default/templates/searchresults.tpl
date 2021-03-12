{nocache}
<p>{$msg_searchresults}</p>

<div id="searchlist">
{foreach item=link from=$arr_results}
	<div class="card mb-3 border-0">
		<div class="row">
			<div class="col-md-3">
				{if $link.page_thumb != ''}
					<img src="{$link.page_thumb}" class="img-fluid rounded">
				{/if}
		</div>
		<div class="col-md-9">
	
			<a href="{$link.set_link}" class="stretched-link" title="{$link.page_title}">{$link.page_title}</a><br>
			<p>{$link.page_meta_description}<br><small class="text-success">{$link.set_link}</small></p>
	</div>
	</div>
	</div>
{/foreach}
</div>
{/nocache}