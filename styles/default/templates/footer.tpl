<footer id="pageFooter" class="pt-3">

	{if is_array($arr_bcmenue) }
	<div class="container">
		<nav aria-label="breadcrumb" class="mt-3">
			<ol class="breadcrumb">
				{foreach item=bc from=$arr_bcmenue}
				<li class="breadcrumb-item"><a href="{$bc.link}" title="{$bc.page_title}">{$bc.page_linkname}</a></li>
				{/foreach}
			</ol>
		</nav>
	</div>
	{/if}
	
	<div class="container">
		<div class="row">
			<div class="col-md-3"> {include file='lastedit.tpl'} </div>
			<div class="col-md-3"> {include file='mostclicked.tpl'} </div>
			<div class="col-md-6 tags"> {include file='tags.tpl'} </div>
		</div>
	</div>
	
	<div class="container" style="margin-top:25px;">
		{$textlib_footer}
	</div>
	
	<hr class="shadow">
	
	<p class="text-center">
		<strong>{$prefs_pagename}</strong> · <a href="https://www.flatcore.org/" title="Open Source Content Management System">powered by flatCore</a>
	</p>

	<p class="text-center d-none">{$fc_pageload_time} Sekunden</p>
</footer>