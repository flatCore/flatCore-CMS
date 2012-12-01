
{include file='header.tpl'}


<section id="fc-body">

	<div class="container">
		
		<section id="fc-content">
			{include file='content.tpl'}
		</section> <!-- eo leftBox -->
		
		
		<aside id="fc-sub-content">
			{$toc_submenu}
			{include file='searchbox.tpl'}
			{$extra_content}
			{$extra_global_content}
			{$login_box}
			{$status_box}				
		</aside>
		
	
	<div id="breadcrumbs">
		{foreach item=bc from=$arr_bcmenue}
			<i class="icon-chevron-right icon-white"></i> <a href="{$bc.link}" title="{$bc.page_title}">{$bc.page_linkname}</a>
		{/foreach}
	</div>
	
	
	</div>


</section>

{include file='footer.tpl'}