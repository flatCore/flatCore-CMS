<?php

//prohibit unauthorized access
require("core/access.php");








//include("$subinc.php");


if($_GET[a] == "") {
	include("mods.list.php");
} else {

	unset($mod);
	include("../modules/$sub/info.inc.php");
	include("../modules/$sub/backend/$a.php");

}



?>