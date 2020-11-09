<?php
//prohibit unauthorized access
require 'core/access.php';


switch ($sub) {

	case "list":
		$subinc = "comments.list";
		break;
		
	case "edit":
		$subinc = "comments.edit";
		break;
		
	default:
		$subinc = "comments.list";
		break;

}


include $subinc.'.php';

?>