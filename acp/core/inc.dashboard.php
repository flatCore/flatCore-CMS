<?php

/*
main page | dashboard
*/

//prohibit unauthorized access
require("core/access.php");


echo '<div id="wrapper">';
echo '<div id="contentbox">';

include("dashboard.top.php");
include("dashboard.system.php");

echo '</div>';
echo '</div>';

echo '<div id="subnav">';

echo '<div id="subnav-inner">';
include("dashboard.subnav.php");
echo '</div>';

include("livebox.php");

echo"</div>";

?>


