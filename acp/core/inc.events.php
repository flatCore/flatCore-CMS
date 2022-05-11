<?php
//prohibit unauthorized access
require 'core/access.php';

switch ($sub) {

    case "edit":
        $subinc = "events.edit";
        break;

    case "bookings":
        $subinc = "events.bookings";
        break;

    case "list":
    default:
        $subinc = "events.list";
        break;
}

include $subinc.'.php';