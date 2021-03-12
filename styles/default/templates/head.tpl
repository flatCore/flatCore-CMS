<title>{$page_title}</title>
<meta charset="utf-8">

<meta name="robots" content="{$page_meta_robots}" />
<meta name="author" content="{$page_meta_author}" />
{if $page_meta_description != ''}
	<meta name="description" content="{$page_meta_description}" />
{else}
	<meta name="description" content="{$prefs_pagedescription}" />
{/if}
<meta name="keywords" content="{$page_meta_keywords}" />
<meta name="date" content="{$page_meta_date}" />

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">


<link rel="icon" href="{$page_favicon}">
<link rel="alternate" type="application/rss+xml" title="{$prefs_pagetitle} | RSS" href="/rss.php" />

{$page_meta_enhanced}

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:url" content="{$fc_page_url}">
<meta property="og:title" content="{$page_title}">
<meta property="og:site_name" content="{$prefs_pagetitle}">

<meta property="og:image" content="{$page_thumbnail}">
{foreach $page_thumbnails as $thumbs}
<meta property="og:image" content="{$thumbs}">
{/foreach}

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{$fc_page_url}">
<meta property="twitter:title" content="{$page_title}">
<meta property="twitter:description" content="{$page_meta_description}">
<meta property="twitter:image" content="{$page_thumbnail}">

<!-- CSS -->
<link rel="stylesheet" media="screen" href="{$fc_inc_dir}/styles/{$fc_template}/css/styles.min.css" />

<!-- JavaScript -->
<script type="text/javascript" src="{$fc_inc_dir}/styles/{$fc_template}/js/main.min.js"></script>


{$page_head_styles}	
{$page_head_enhanced}
{$modul_head_enhanced}
{$prefs_pagesglobalhead}

<meta name="generator" content="flatCore" />