<footer id="pageFooter">

	{if is_array($arr_bcmenue) }
	<ol class="breadcrumb">
		{foreach item=bc from=$arr_bcmenue}
		<li><a href="{$bc.link}" title="{$bc.page_title}">{$bc.page_linkname}</a></li>
		{/foreach}
	</ol>
	{/if}
	
	<div class="container clearfix">
		<div class="row">
			<div class="col-md-3 first"> {include file='lastedit.tpl'} </div>
			<div class="col-md-3"> {include file='mostclicked.tpl'} </div>
			<div class="col-md-6 tags"> {include file='tags.tpl'} </div>
		</div>
	</div>
	
	<div class="container" style="margin-top:25px;">
		{$textlib_footer}
	</div>
	
	<hr>
	
	<p class="text-center">
		<a href="http://www.flatcore.de/" title="Open Source Content Management System">powered by flatCore</a><br>
		Designed and built with <a href="http://getbootstrap.com">Bootstrap, from Twitter</a><br>
		Icons from <a href="http://glyphicons.com/">Glyphicons Free</a>, licensed under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a>
	</p>

	<p class="text-center hide">{$fc_pageload_time} Sekunden</p>
</footer>