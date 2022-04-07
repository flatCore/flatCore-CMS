<?php

//prohibit unauthorized access
require 'core/access.php';

echo '<div id="topNavBox">';

echo '<a href="#" id="toggleNav" class="me-auto" title="Dashboard"><span class="caret_left">'.$icon['caret_left'].'</span> '.$icon['bars'].' <span class="caret_right">'.$icon['caret_right'].'</span></a>';

echo '<a class="'.$active[0].'" href="acp.php?tn=dashboard" title="'.$lang['tn_dashboard_desc'].'"><span class="top-nav-icon">'.$icon['speedometer'].'</span><span class="top-nav-text">'.$lang['tn_dashboard'].'</span></a>';
echo '<a class="'.$active[1].'" href="acp.php?tn=pages" title="'.$lang['tn_contents_desc'].'"><span class="top-nav-icon">'.$icon['diagram_3'].'</span><span class="top-nav-text">'.$lang['tn_contents'].'</span></a>';
echo '<a class="'.$active[6].'" href="acp.php?tn=posts" title="'.$lang['tn_posts_desc'].'"><span class="top-nav-icon">'.$icon['file_earmark_post'].'</span><span class="top-nav-text">'.$lang['tn_posts'].'</span></a>';
echo '<a class="'.$active[7].'" href="acp.php?tn=reactions" title="'.$lang['tn_reactions_desc'].'"><span class="top-nav-icon">'.$icon['chat_square_dots'].'</span><span class="top-nav-text">'.$lang['tn_reactions'].'</span></a>';
echo '<a class="'.$active[2].'" href="acp.php?tn=addons" title="'.$lang['tn_moduls_desc'].'"><span class="top-nav-icon">'.$icon['plugin'].'</span><span class="top-nav-text">'.$lang['tn_moduls'].'</span></a>';
echo '<a class="'.$active[3].'" href="acp.php?tn=filebrowser" title="'.$lang['tn_filebrowser_desc'].'"><span class="top-nav-icon">'.$icon['folder'].'</span><span class="top-nav-text">'.$lang['tn_filebrowser'].'</span></a>';
echo '<a class="'.$active[4].'" href="acp.php?tn=user" title="'.$lang['tn_usermanagement'].'"><span class="top-nav-icon">'.$icon['people'].'</span><span class="top-nav-text">'.$lang['tn_usermanagement'].'</span></a>';
echo '<a class="'.$active[5].'" href="acp.php?tn=system" title="'.$lang['tn_system'].'"><span class="top-nav-icon">'.$icon['gear'].'</span><span class="top-nav-text">'.$lang['tn_system'].'</span></a>';
echo '<a class="topnav toggle_sb_help" href="#" title="help"><span class="top-nav-icon">'.$icon['question'].'</span></a>';
echo '</div>';


?>