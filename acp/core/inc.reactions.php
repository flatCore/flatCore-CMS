<?php
//prohibit unauthorized access
require 'core/access.php';

switch ($sub) {

    case "votings":
		$subinc = "reactions.votings";
		break;

    case "comments":
    default:
		$subinc = "reactions.comments";
		break;
}

include $subinc.'.php';

?>