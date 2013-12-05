<?php

//prohibit unauthorized access
require("core/access.php");


echo'<div id="topNavBox">';

echo"<a class='$active[0] tooltip_bottom' href='acp.php?tn=dashboard' title='$lang[tn_dashboard_desc]'><span class='mm mm-dashboard'></span> $lang[tn_dashboard]</a>";
echo"<a class='$active[1] tooltip_bottom' href='acp.php?tn=pages' title='$lang[tn_pages_desc]'><span class='mm mm-pages'></span> $lang[tn_pages]</a>";
echo"<a class='$active[2] tooltip_bottom' href='acp.php?tn=moduls' title='$lang[tn_moduls_desc]'><span class='mm mm-modules'></span> $lang[tn_moduls]</a>";
echo"<a class='$active[3] tooltip_bottom' href='acp.php?tn=filebrowser' title='$lang[tn_filebrowser_desc]'><span class='mm mm-files'></span> $lang[tn_filebrowser]</a>";
echo"<a class='$active[4] tooltip_bottom' href='acp.php?tn=user' title='$lang[tn_usermanagement_desc]'><span class='mm mm-user'></span> $lang[tn_usermanagement]</a>";
echo"<a class='$active[5] tooltip_bottom' href='acp.php?tn=system' title='$lang[tn_system_desc]'><span class='mm mm-system'></span> $lang[tn_system]</a>";


echo'</div>';




?>