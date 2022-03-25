<header id="pageHeader" class="mb-3">

	<div class="container pt-3 mb-3">
		
		{if $cnt_shopping_cart_items != ''}
			<div class="shopping-cart-container">
				<a href="{$shopping_cart_uri}" title="{$lang_label_shopping_cart}"><i class="bi bi-cart-fill"></i> {$cnt_shopping_cart_items}</a>
			</div>
		{/if}
		
		{if is_array($legal_pages) }
		<div class="legal-pages-container">
			<ul>
			{foreach item=pages from=$legal_pages}
				<li><a class="" href="{$prefs_cms_base}{$pages.page_permalink}" title="{$pages.page_title}">{$pages.page_linkname}</a></li>
			{/foreach}
			</ul>
		</div>
		{/if}
		
		{if $social_media_block != ''}
			{include file='socialmedia.tpl'}
		{/if}	
		
		<p class="h1">{$prefs_pagetitle}</p>
		<p class="h2">{$prefs_pagesubtitle}</p>
	</div>
	
	{include file='navigation.tpl'}
		
</header>