<?php

/*
main page | dashboard
*/

//prohibit unauthorized access
require("core/access.php");




echo"<div id='wrapper'> ";


echo"<div id='contentbox'> ";


echo"<h2>Dashboard</h2>";

echo '<hr class="spacer">';

include("dashboard.top.php");

include("dashboard.system.php");

echo '<hr class="spacer">';

echo"</div>"; // eol div contentbox

echo"</div>"; // eol div wrapper

echo"<div id='subnav'>";
// sub navigation
echo"<div id='subnav-inner'>";
	include("dashboard.subnav.php");
echo"</div>"; // sub navigation EOL

// liveBox
include("livebox.php");


echo"</div>";

?>


