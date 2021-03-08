<header id="pageHeader" class="mb-3">

	<div class="container pt-3 mb-3">
		
		<div class="d-flex flex-row-reverse social-media">
		{if $fb_link != ''} {$fb_link} {/if}
		{if $insta_link != ''} {$insta_link} {/if}
		{if $tw_link != ''} {$tw_link} {/if}
		</div>
		
			<p class="h1">{$prefs_pagetitle}</p>
			<p class="h2">{$prefs_pagesubtitle}</p>
	</div>
	
	{include file='navigation.tpl'}
		
</header>