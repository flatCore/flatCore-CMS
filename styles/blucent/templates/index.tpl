<!DOCTYPE html>
<html lang="de">
	<head>

	{include file='head.tpl'}
	
	</head>
	
	<body class="{$page_hash}">
	
	{include file="$body_template"}
	
	{if $fc_snippet_privacy_policy != ''}
		<div class="privacy_policy">
			<div style="float:right;padding-left:10px;">
				<a href="#NULL" class="btn btn-default" id="permit_cookie">Okay</a>
			</div>
			<div class="privacy_policy_text"></div>
		</div>
	{/if}
	
	<script>
		
	var permit_cookies_str = '{$fc_snippet_privacy_policy}';
	$( ".privacy_policy_text" ).html( permit_cookies_str );
		
	$( "#permit_cookie" ).click(function() {
  	Cookies.set('permit_cookies', 'true', { expires: 7 });
  	$( "div.privacy_policy" ).addClass( "hidden" );
	});
	
	if(Cookies.get('permit_cookies') == 'true') {
		$( "div.privacy_policy" ).addClass( "hidden" );
	}
	
	</script>
	
	</body>
</html>
