{include file='header.tpl'}

<section id="fc-body">

	<div class="fc-teaser-small"{$teaser_background_image}>
		
		<div class="fc-teaser-caption">
			<div class="container">
				<h4>{$page_title}</h4>
				<p>{$page_meta_description}</p>
			</div>
		</div>
		
	</div>

	<div class="container">
		
		<section id="fc-content">
			{include file='content.tpl'}
		</section> <!-- eo leftBox -->
		
		<aside id="fc-sub-content">
			{include file='toc.tpl'}
			{include file='searchbox.tpl'}
			{include file='extracontent.tpl'}
			{include file='extracontent_global.tpl'}
			{$login_box}
			{$status_box}				
		</aside>

	</div>
		
</section>

{include file='footer.tpl'}