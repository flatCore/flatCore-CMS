<div id="navPosition">
	<div class="container">
		<div id="header-nav" class="navbar navbar-inverse navbar-clear">
		  <div class="navbar-inner">
		    <div class="container">
		      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		      </a>
		      <div class="nav-collapse collapse">
		      	<ul id="navlist" class="clearfix">
		      		<li><a class="{$homelink_status}" href="{$link_home}" title="{$homepage_title}"><img src="{$fc_inc_dir}/styles/blucent/images/home.png"></a></li>	
							{foreach item=nav from=$arr_menue}
								<li><a class="{$nav.link_status} nav-id-{$nav.page_id}" href="{$nav.link}" title="{$nav.page_title}">{$nav.page_linkname}<span class="tri-active"></span></a></li>
							{/foreach}
						</ul>
		      </div>
		    </div>
		  </div>
	  </div>
	</div>
</div>