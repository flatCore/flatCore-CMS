<div id="navPosition">

<div class="container">
<nav id="top-nav">
	<ul id="navlist" class="clearfix">
		<li><a class="{$homelink_status}" href="{$link_home}" title="{$nav.page_title}"><img src="{$fc_inc_dir}/styles/blucent/images/home.png"></a></li>	
		{foreach item=nav from=$arr_menue}
			<li><a class="{$nav.link_status} nav-id-{$nav.page_id}" href="{$nav.link}" title="{$nav.page_title}">{$nav.page_linkname}</a></li>
		{/foreach}
	</ul>
</nav>
</div>

</div>