<?php
//prohibit unauthorized access
require 'core/access.php';


switch ($sub) {

	case "list":
		$subinc = "posts.list";
		break;
		
	case "edit":
		$subinc = "posts.edit";
		break;
		
	case "features":
		$subinc = "posts.features";
		break;
		
	default:
		$subinc = "pages.list";
		break;

}


include $subinc.'.php';

?>