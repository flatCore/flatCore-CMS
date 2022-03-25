<?php
//prohibit unauthorized access
require 'core/access.php';


switch ($sub) {

	case "comments":
		$subinc = "reactions.comments";
		break;
		
	case "votings":
		$subinc = "reactions.votings";
		break;
		
	case "events":
		$subinc = "reactions.events";
		break;
		
	case "orders":
		$subinc = "reactions.orders";
		break;
				
	default:
		$subinc = "reactions.comments";
		break;

}

include $subinc.'.php';

?>