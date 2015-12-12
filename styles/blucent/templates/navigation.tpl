<nav class="navbar navbar-fade" id="top-navigation" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
      	<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
      </button>
		</div>
			
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
      	<li class="{$homelink_status}"><a href="{$link_home}" title="{$homepage_title}">&nbsp;<span class="glyphicon glyphicon-home"></span>&nbsp;</a></li>	
				{foreach item=nav from=$arr_menue}
				<li class="{$nav.link_status}"><a class="nav-id-{$nav.page_id} {$nav.page_hash}" href="{$nav.link}" title="{$nav.page_title}">{$nav.page_linkname}</a></li>
				{/foreach}
			</ul>
		</div>
	</div>
</nav>
