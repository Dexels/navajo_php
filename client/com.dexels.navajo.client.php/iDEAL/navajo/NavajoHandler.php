<?php
require_once "NavajoPhpClient.php";
require_once "NavajoClient.php";

NavajoClient :: updateNavajoFromPost();

if (!isset ($_REQUEST['action'])) {
    $_REQUEST['action'] = $defaultPage;
}

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
    $nav = $_REQUEST['nav'];
    $msgName = $_REQUEST['msgName'];
    $_SESSION['ordering'][$nav][$msgName]["sortKey"] = $_REQUEST['sortKey'];
    $_SESSION['ordering'][$nav][$msgName]["sortDir"] = $_REQUEST['sortDir'];
}

if (isset ($_REQUEST['action'])) {
    if ($_REQUEST['action'] == 'exit') {
        //session_start();
        if (isset ($_SESSION['site'])) {
            $_SESSION['site']->onDestroySession();
        }
        session_destroy();
        unset ($_REQUEST['action']);
        //session_start();
        if (!isset ($_SESSION['site'])) {
            $_SESSION['site'] = new WebSite();
            $_SESSION['site']->onStartSession();
        }

        include $siteHome . $defaultPage . '.php';

    } else {
        $_SESSION['currentPage'] = $_REQUEST['action'];
        //		echo 'including: '.$siteHome.$_SESSION['currentPage'].'.php';
        include $_SERVER['DOCUMENT_ROOT'] . $siteHome . $_SESSION['currentPage'] . '.php';
    }

}

if (!isset ($_REQUEST['action'])) {
    $_REQUEST['action'] = $defaultPage;
}

if (isset ($_REQUEST['next']) && !is_null($_REQUEST['next'])) {
    trace("DEPRECATED!");
    $_SESSION['currentPage'] = $_REQUEST['next'];
    $currentPage = $_SESSION['currentPage'];
}
?>

