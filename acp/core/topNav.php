<?php

//prohibit unauthorized access
require 'core/access.php';

echo '<div id="topNavBox">';

echo '<a href="#" id="toggleNav" class="me-auto" title="Dashboard"><span class="caret_left">'.$icon['caret_left'].'</span> '.$icon['bars'].' <span class="caret_right">'.$icon['caret_right'].'</span></a>';

echo '<a class="'.$active[0].'" href="acp.php?tn=dashboard" title="'.$lang['tn_dashboard_desc'].'"><span class="mm"></span>'.$lang['tn_dashboard'].'</a>';
echo '<a class="'.$active[1].'" href="acp.php?tn=pages" title="'.$lang['tn_contents_desc'].'"><span class="mm"></span>'.$lang['tn_contents'].'</a>';
echo '<a class="'.$active[6].'" href="acp.php?tn=posts" title="'.$lang['tn_posts_desc'].'"><span class="mm"></span>'.$lang['tn_posts'].'</a>';
echo '<a class="'.$active[7].'" href="acp.php?tn=reactions" title="'.$lang['tn_reactions_desc'].'"><span class="mm"></span>'.$lang['tn_reactions'].'</a>';
echo '<a class="'.$active[2].'" href="acp.php?tn=addons" title="'.$lang['tn_moduls_desc'].'"><span class="mm"></span>'.$lang['tn_moduls'].'</a>';
echo '<a class="'.$active[3].'" href="acp.php?tn=filebrowser" title="'.$lang['tn_filebrowser_desc'].'"><span class="mm"></span>'.$lang['tn_filebrowser'].'</a>';
echo '<a class="'.$active[4].'" href="acp.php?tn=user" title="'.$lang['tn_usermanagement'].'"><span class="mm"></span>'.$lang['tn_usermanagement'].'</a>';
echo '<a class="'.$active[5].'" href="acp.php?tn=system" title="'.$lang['tn_system'].'"><span class="mm"></span>'.$lang['tn_system'].'</a>';
echo '<a class="topnav toggle_sb_help" href="#" title="help">'.$icon['question'].'</a>';
echo '</div>';


?>