<title>{$page_title}</title>
<meta charset="utf-8">

<meta name="robots" content="{$page_meta_robots}" />
<meta name="author" content="{$page_meta_author}" />
<meta name="description" content="{$page_meta_description}" />
<meta name="keywords" content="{$page_meta_keywords}" />
<meta name="date" content="{$page_meta_date}" />

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">


<link rel="icon" href="/content/images/{$page_favicon}">
<link rel="alternate" type="application/rss+xml" title="{$prefs_pagetitle} | RSS" href="/rss.php" />

{$page_meta_enhanced}

<!-- OpenGraph Meta Tags -->
<meta property="og:title" content="{$page_title}">
<meta property="og:image" content="/content/images/{$page_thumbnail}">
<meta property="og:site_name" content="{$prefs_pagetitle}">

<!-- CSS -->
<link rel="stylesheet" media="screen" href="{$fc_inc_dir}/styles/{$fc_template}/css/styles.min.css" />

<!-- jQuery -->
<script type="text/javascript" src="{$fc_inc_dir}/lib/js/jquery/jquery.min.js"></script>
<script type="text/javascript" src="{$fc_inc_dir}/styles/blucent/js/bootstrap.min.js"></script>

<script type="text/javascript" src="{$fc_inc_dir}/styles/blucent/js/js.cookie.js"></script>
	
<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

{$page_head_styles}	
{$page_head_enhanced}
{$modul_head_enhanced}
{$prefs_pagesglobalhead}

<meta name="generator" content="flatCore" />