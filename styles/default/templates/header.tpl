<header id="pageHeader" class="mb-3">

	<div class="container pt-3 mb-3">
		
		{if $social_media_block != ''}
			{include file='socialmedia.tpl'}
		{/if}
		
		
		<p class="h1">{$prefs_pagetitle}</p>
		<p class="h2">{$prefs_pagesubtitle}</p>
	</div>
	
	{include file='navigation.tpl'}
		
</header>