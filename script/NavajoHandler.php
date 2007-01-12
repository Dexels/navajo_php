<?php

require_once "NavajoPhpClient.php";
require_once "NavajoClient.php";

session_start();

NavajoClient :: updateNavajoFromPost();

if (isset ($_REQUEST['serverCall'])) {
    $actions = explode(';', $_REQUEST['serverCall']);
    foreach ($actions as $current) {
        $initscr = explode(':', $current);
        if (count($initscr) == 2) {
            $nnn = NavajoClient :: callService($initscr[0], $initscr[1]);
        } else {
            $nnn = NavajoClient :: callInitService($initscr[0]);
        }
    }
}

if (isset ($_REQUEST['sortKey'])) {
	$nav     = $_REQUEST['nav'];
	$msgName = $_REQUEST['msgName'];	
	$_SESSION['ordering'][$nav][$msgName]["sortKey"] = $_REQUEST['sortKey'];
    $_SESSION['ordering'][$nav][$msgName]["sortDir"] = $_REQUEST['sortDir'];            
}

if (isset ($_REQUEST['action']) && $_REQUEST['action'] == 'exit') {
    session_destroy();
}
if (isset ($_REQUEST['next']) && !is_null($_REQUEST['next'])) {
    $_SESSION['currentPage'] = $_REQUEST['next'];
    $currentPage = $_SESSION['currentPage'];    
}
include $_SESSION['currentPage'];
?>

	
	