<?php
//prohibit unauthorized access
require 'core/access.php';


switch ($sub) {

    case "list":
        $subinc = "shop.list";
        break;

    case "edit":
        $subinc = "shop.edit";
        break;

    case "features":
        $subinc = "shop.features";
        break;

    case "orders":
        $subinc = "shop.orders";
        break;

    default:
        $subinc = "shop.list";
        break;

}


include $subinc.'.php';
