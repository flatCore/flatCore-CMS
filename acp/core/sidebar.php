<?php


/**
 * sidebar for pages, snippets
 */

if($maininc == 'inc.dashboard') {

    echo '<ul class="nav">';
    echo '<li><a class="sidebar-sub" href="acp.php?tn=pages&sub=list">'.$icon['sitemap'].' '.$lang['page_list'].'</a></li>';
    echo '<li><a class="sidebar-sub" href="acp.php?tn=pages&sub=new">'.$icon['plus'].' '.$lang['new_page'].'</a></li>';
    echo '<li><a class="sidebar-sub" href="acp.php?tn=posts">'.$icon['file_earmark_post'].' '.$lang['tn_posts'].'</a></li>';
    echo '<li><a class="sidebar-sub" href="acp.php?tn=pages&sub=snippets">'.$icon['clipboard'].' '.$lang['snippets'].'</a></li>';
    echo '<li><a class="sidebar-sub" href="acp.php?tn=pages&sub=shortcodes">'.$icon['code_slash'].' Shortcodes</a></li>';
    echo '<li class="mb-1"><a class="sidebar-sub" href="acp.php?tn=pages&sub=rss">'.$icon['rss'].' RSS</a></li>';
    echo '</ul>';

    echo '<ul class="nav">';
    echo '<li><a class="sidebar-sub" href="acp.php?tn=filebrowser&sub=browse">'.$icon['folder'].' '.$lang['manage_files'].'</a></li>';
    echo '<li class="mb-1"><a class="sidebar-sub" data-bs-toggle="modal" data-bs-target="#uploadModal" href="#">'.$icon['upload'].' '.$lang['go_to_upload'].'</a></li>';
    echo '</ul>';

    echo '<ul class="nav">';
    echo '<li><a class="sidebar-sub" href="acp.php?tn=user&sub=list">'.$icon['people'].' '.$lang['list_user'].'</a></li>';
    echo '<li><a class="sidebar-sub" href="acp.php?tn=user&sub=new">'.$icon['person_plus'].' '.$lang['new_user'].'</a></li>';
    echo '</ul>';
}


/**
 * sidebar for pages, snippets
 */

if($maininc == 'inc.pages') {

    if($sub == '') {
        $sub = 'list';
    }

    echo '<ul class="nav">';
    echo '<li><a class="sidebar-nav '.($sub == "list" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=pages&sub=list">'.$icon['sitemap'].' '.$lang['page_list'].'</a></li>';
    echo '<li><a class="sidebar-nav '.($sub == "snippets" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=pages&sub=snippets">'.$icon['clipboard'].' '.$lang['snippets'].'</a></li>';
    echo '<li><a class="sidebar-nav '.($sub == "shortcodes" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=pages&sub=shortcodes">'.$icon['code'].' Shortcodes</a></li>';
    echo '<li><a class="sidebar-nav '.($sub == "index" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=pages&sub=index">'.$icon['database'].' '.$lang['page_index'].'</a></li>';
    echo '<li><a class="sidebar-nav '.($sub == "rss" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=pages&sub=rss">'.$icon['rss'].' RSS</a></li>';
    echo '</ul>';
}

if($maininc == 'inc.posts') {

    if($sub == '') {
        $sub = 'list';
    }

    echo '<ul class="nav">';
    echo '<li><a class="sidebar-nav '.($sub == "list" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=posts&sub=list">'.$icon['file_earmark_post'].' '.$lang['post_list'].'</a></li>';
    echo '<li><a class="sidebar-nav '.($sub == "edit" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=posts&sub=edit">'.$icon['edit'].' '.$lang['post_new_edit'].'</a></li>';
    echo '</ul>';

}

if($maininc == 'inc.shop') {

    if($sub == '') {
        $sub = 'list';
    }

    echo '<ul class="nav">';
    echo '<li><a class="sidebar-nav '.($sub == "list" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=shop&sub=list">'.$icon['file_earmark_post'].' '.$lang['post_list'].'</a></li>';
    echo '<li><a class="sidebar-nav '.($sub == "features" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=shop&sub=features">'.$icon['list'].' '.$lang['post_features'].'</a></li>';
    echo '<li><a class="sidebar-nav '.($sub == "orders" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=shop&sub=orders">'.$icon['shopping_basket'].' '.$lang['nav_orders'].'</a></li>';
    echo '</ul>';
}

if($maininc == 'inc.events') {
    if($sub == '') {
        $sub = 'list';
    }

    echo '<ul class="nav">';
    echo '<li><a class="sidebar-nav '.($sub == "list" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=events&sub=list">'.$icon['file_earmark_post'].' '.$lang['post_list'].'</a></li>';
    echo '<li><a class="sidebar-nav '.($sub == "bookings" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=events&sub=bookings">'.$icon['calendar_check'].' '.$lang['btn_bookings'].'</a></li>';
    echo '</ul>';
}

if($maininc == 'inc.reactions') {
    if($sub == '') {
        $sub = 'comments';
    }

    echo '<ul class="nav">';
    echo '<li><a class="sidebar-nav '.($sub == "comments" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=reactions&sub=comments">'.$icon['comments'].' '.$lang['reactions_comments'].'</a></li>';
    echo '<li><a class="sidebar-nav '.($sub == "votings" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=reactions&sub=votings">'.$icon['thumbs_up'].' '.$lang['reactions_votings'].'</a></li>';
    echo '</ul>';
}

if($maininc == 'inc.filebrowser') {
    if($sub == '') {
        $sub = 'browse';
    }

    echo '<ul class="nav">';
    echo '<li><a class="sidebar-nav '.($sub == "browse" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=filebrowser&sub=browse">'.$icon['folder_open'].' '.$lang['manage_files'].'</a></li>';
    echo '<li><a class="sidebar-nav" data-bs-toggle="modal" data-bs-target="#uploadModal" href="#">'.$icon['upload'].' '.$lang['go_to_upload'].'</a></li>';
    echo '</ul>';
}


if($maininc == 'inc.user') {
    if($sub == '') {
        $sub = 'list';
    }

    echo '<ul class="nav">';
    echo '<li><a class="sidebar-nav '.($sub == "list" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=user&sub=list">'.$icon['users'].' '.$lang['list_user'].'</a></li>';
    echo '<li><a class="sidebar-nav '.($sub == "new" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=user&sub=new">'.$icon['user_plus'].' '.$lang['new_user'].'</a></li>';
    echo '<li><a class="sidebar-nav '.($sub == "groups" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=user&sub=groups">'.$icon['user_friends'].' '.$lang['edit_groups'].'</a></li>';
    echo '</ul>';
}

if($maininc == 'inc.system') {
    if($sub == '') {
        $sub = 'sys_pref';
    }

    echo '<ul class="nav">';
    echo '<li><a class="sidebar-nav '.($sub == "sys_pref" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=sys_pref">'.$icon['cog'].' '.$lang['system_preferences'].'</a></li>';
    echo '<li><a class="sidebar-nav '.($sub == "mail" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=mail">'.$icon['at'].' '.$lang['system_mail'].'</a></li>';
    echo '<li><a class="sidebar-nav '.($sub == "language" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=language">'.$icon['language'].' '.$lang['system_language'].'</a></li>';
    echo '<li><a class="sidebar-nav '.($sub == "images" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=images">'.$icon['images'].' '.$lang['system_images'].'</a></li>';
    echo '</ul>';
    echo '<ul class="nav">';
    echo '<li><a class="sidebar-nav '.($sub == "labels" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=labels">'.$icon['tags'].' '.$lang['labels'].'</a></li>';
    echo '<li><a class="sidebar-nav '.($sub == "categories" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=categories">'.$icon['bookmark'].' '.$lang['categories'].'</a></li>';
    echo '</ul>';
    echo '<ul class="nav">';
    echo '<li><a class="sidebar-nav '.($sub == "posts" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=posts">'.$icon['file_earmark_post'].' '.$lang['tn_posts'].'</a></li>';
    echo '<li><a class="sidebar-nav '.($sub == "comments" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=comments">'.$icon['comments'].' '.$lang['tn_comments'].'</a></li>';
    echo '</ul>';
    echo '<ul class="nav">';
    echo '<li class="mt-2"><a class="sidebar-nav '.($sub == "shop" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=shop">'.$icon['shopping_basket'].' '.$lang['tn_shop'].'</a></li>';
    echo '</ul>';
    echo '<ul class="nav">';
    echo '<li class="mt-2"><a class="sidebar-nav '.($sub == "customize" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=customize">'.$icon['table'].' '.$lang['customize_database'].'</a></li>';
    echo '</ul>';
    echo '<ul class="nav">';
    echo '<li><a class="sidebar-nav '.($sub == "stats" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=stats">'.$icon['chart_bar'].' '.$lang['system_statistics'].'</a></li>';
    echo '<li><a class="sidebar-nav '.($sub == "backup" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=backup">'.$icon['download'].' '.$lang['system_backup'].'</a></li>';
    echo '<li><a class="sidebar-nav '.($sub == "update" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=update">'.$icon['sync_alt'].' '.$lang['system_update'].'</a></li>';
    echo '</ul>';
}

/**
 * addons can have their own sidebar
 */
if($maininc == 'inc.addons') {

    $a = '';
    if(isset($_GET['a'])) {
        $a = clean_vars($_GET['a']);
    }
    if($sub == '') {
        $sub = 'list';
    }

    $nbrModuls = count($all_mods);

    echo '<ul class="nav">';
    $mod_subnav = '<li><a class="sidebar-nav '.($sub == "list" ? 'sidebar-nav-active' :'').'" href="?tn=moduls&sub=list">'.$icon['plugin'].' '.$lang['tn_moduls'].'</a></li>';

    for($i=0;$i<$nbrModuls;$i++) {

        $modFolder = $all_mods[$i]['folder'];

        unset($modnav);
        include '../modules/'.$modFolder.'/info.inc.php';
        $cnt_modnav = count($modnav);

        $mod_subnav .= '<li><a class="sidebar-nav '.($sub == "$modFolder" ? 'sidebar-nav-active' :'').'" href="?tn=moduls&sub='.$modFolder.'&a=start">'.$icon['caret_right_fill'].' '.$mod['name'].'</a></li>';

        //Show submenu of the current addon
        if($sub == "$modFolder") {

            for($x=0;$x<$cnt_modnav;$x++) {
                $showlink = $modnav[$x]['link'];
                $incpage = $modnav[$x]['file'];

                $sub_link_class = "sidebar-sub";
                if($a == $incpage) {
                    $sub_link_class = "sidebar-sub-active";
                }

                $mod_subnav .= '<li><a class="'.$sub_link_class.'" href="?tn=moduls&sub='.$modFolder.'&a='.$incpage.'">'.$icon['caret_right'].' '.$showlink.'</a></li>';
                if($x==($cnt_modnav-1)) {
                    $mod_subnav .= '<li class="sidebar-sub-end"></li>';
                }
            }

            // get preferences from info.inc.php
            $mod_name = $mod['name'];
            $mod_version = $mod['version'];
            $mod_db = '../'.$mod['database'];
        }
    }

    echo $mod_subnav;
    echo '</ul>';
}


echo '<div class="sidebar-footer">';
echo '<a href="../">'.$icon['home'].' '.$lang['back_to_page'].'</a>';
echo '<a href="../index.php?goto=logout">'.$icon['logout'].' '.$lang['logout'].'</a>';
echo '</div>';