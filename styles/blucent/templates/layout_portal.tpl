{include file='header.tpl'}

<section id="fc-body">

	<div class="container">
		
		<section id="fc-content">
			{include file='content.tpl'}
		</section> <!-- eo leftBox -->
			
		<aside id="fc-sub-content">
			{$toc_submenu}		
			{include file='searchbox.tpl'}	
			{include file='extracontent.tpl'}
			{include file='extracontent_global.tpl'}
			{$login_box}
			{$status_box}
		</aside>
		
	</div>

</section>

{include file='footer.tpl'}




