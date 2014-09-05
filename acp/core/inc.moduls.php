<?php

//prohibit unauthorized access
require("core/access.php");

if(!isset($_GET['a'])) {
	include("mods.list.php");
} else {
	unset($mod);
	include("../modules/$sub/info.inc.php");
	include("../modules/$sub/backend/$a.php");
}

?>