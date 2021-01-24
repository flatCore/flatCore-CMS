{include file='header.tpl'}
<section id="fc-body">
	<div class="container">
		<div class="row">
			<div id="fc-content" class="col-lg-9">
				{include file='content.tpl'}
			</div> <!-- eo leftBox -->
			<div id="fc-sub-content" class="col">
				{$nav_categories}
				{include file='toc.tpl'}
				{include file='extracontent.tpl'}
				{include file='extracontent_global.tpl'}
				{nocache}
				{$login_box nocache}
				{$status_box nocache}
				{/nocache}			
			</div>
		</div>
	</div>
</section>
{include file='footer.tpl'}