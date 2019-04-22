<nav class="navbar navbar-expand-lg navbar-light bg-light">
	<div class="container">
  	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
    	<span class="navbar-toggler-icon"></span>
		</button>
			
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mr-auto">
      	<li class="nav-item {$homelink_status}"><a class="nav-link" href="{$link_home}" title="{$homepage_title}">{$homepage_linkname}</a></li>	
				{foreach item=nav from=$arr_menue}
				<li class="nav-item {$nav.link_status}">
					<a class="nav-link nav-id-{$nav.page_id} {$nav.page_hash}" href="{$nav.link}" title="{$nav.page_title}">{$nav.page_linkname}</a>
				</li>
				{/foreach}
			</ul>
		</div>
		
		<form class="form-inline" action="/search/" method="POST">
    	<input class="form-control mr-sm-2 searchbox" name="s" type="search" aria-label="Search">
  	</form>
		
	</div>
</nav>