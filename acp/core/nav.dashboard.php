<?php

require 'core/access.php';


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



?>