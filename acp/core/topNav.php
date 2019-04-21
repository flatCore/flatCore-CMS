<?php

//prohibit unauthorized access
require 'core/access.php';

echo '<div id="topNavBox">';

echo '<a href="#" id="toggleNav" title="Dashboard"><span class="caret_left">'.$icon['caret_left'].'</span> '.$icon['bars'].' <span class="caret_right">'.$icon['caret_right'].'</span></a>';

echo '<div class="float-right">';

echo '<a class="'.$active[0].' tooltip_bottom" href="acp.php?tn=dashboard" title="'.$lang['tn_dashboard_desc'].'"><span class="mm"></span>'.$lang['tn_dashboard'].'</a>';
echo '<a class="'.$active[1].' tooltip_bottom" href="acp.php?tn=pages" title="'.$lang['tn_contents_desc'].'"><span class="mm"></span>'.$lang['tn_contents'].'</a>';
echo '<a class="'.$active[2].' tooltip_bottom" href="acp.php?tn=moduls" title="'.$lang['tn_moduls_desc'].'"><span class="mm"></span>'.$lang['tn_moduls'].'</a>';
echo '<a class="'.$active[3].' tooltip_bottom" href="acp.php?tn=filebrowser" title="'.$lang['tn_filebrowser_desc'].'"><span class="mm"></span>'.$lang['tn_filebrowser'].'</a>';
echo '<a class="'.$active[4].' tooltip_bottom" href="acp.php?tn=user" title="'.$lang['tn_usermanagement'].'"><span class="mm"></span>'.$lang['tn_usermanagement'].'</a>';
echo '<a class="'.$active[5].' tooltip_bottom" href="acp.php?tn=system" title="'.$lang['tn_system'].'"><span class="mm"></span>'.$lang['tn_system'].'</a>';
echo '<a data-fancybox data-type="ajax" data-src="core/ajax.chat.php" href="javascript:;" class="topnav">'.$icon['comments'].' Chat</a>';
echo '</div>';

echo '</div>';

?>