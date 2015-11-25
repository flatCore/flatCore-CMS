<!DOCTYPE html>
<html lang="de">
	<head>

	{include file='head.tpl'}
	
	</head>
	
	<body>
	
	{include file="$body_template"}
	
	{if $fc_snippet_privacy_policy != ''}
		<div class="privacy_policy">
			<div class="container">
				<div class="row">
					<div class="col-md-10">
						{$fc_snippet_privacy_policy}
					</div>
					<div class="col-md-2">
						<a href="#NULL" class="btn btn-default" id="permit_cookie">Okay</a>
					</div>
				</div>
			</div>
		</div>
	{/if}
	
	<script>
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
