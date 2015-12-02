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
		{include file='content.tpl'}
	</div>
</section>

{include file='footer.tpl'}