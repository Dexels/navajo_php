<?php

NavajoClient :: updateNavajoFromPost();

# based on the label of the submit, get correct id
if (isset ($_REQUEST['formsubmit'])) {
    $formsubmit = $_REQUEST['formsubmit'];
    if (isset ($_REQUEST[$formsubmit . ':target'])) {
        $target = $_REQUEST[$formsubmit . ':target'];
    }
    if (isset($_REQUEST[$formsubmit . ':serverCall'])) {
        $_REQUEST['serverCall'] = $_REQUEST[$formsubmit . ':serverCall'];
    }
}

# call webservice if the formSecret session variable matches the POST variable (to prevent duplicates)
if (isset ($_REQUEST['serverCall']) && isset($_SESSION['formId'])) {
    $actions = explode(';', $_REQUEST['serverCall']);
    foreach ($actions as $current) {
        $initscr = explode(':', $current);
        try {
            if (count($initscr) == 2) {
                # if an access_id POST variable is set, match it with the accessId of the input WS, a reload (e.g. on a different tab), could otherwise cause severe problems!
                if (isset($_POST['access_id'])) {
                    $navajo_access_id = getNavajo($initscr[0])->getAccessId();
                    if ($navajo_access_id != $_POST['access_id']) {
                        echo "<p class='error'>U kunt deze lijst niet meer opslaan omdat u inmiddels een andere lijst van het zelfde soort heeft geopend. Probeer het nog eens.</p>";
                    } else {
                        $nnn = NavajoClient :: callService($initscr[0], $initscr[1]);
                    }
                } else {
                    $nnn = NavajoClient :: callService($initscr[0], $initscr[1]);
                }
            } else {
                $nnn = NavajoClient :: callInitService($initscr[0]);
            }
        } catch (Exception $e) {
            $_REQUEST['errormessage'] = $e->getMessage();
            echo $e->getMessage();
        }
    }
    unset($_SESSION['formId']);
}

if (isset($target) || isset ($_POST['action']) || isset ($_REQUEST['action'])) {
    if (isset($_GET['action']) && $_GET['action'] == 'exit') {
        if (isset ($_SESSION['site'])) {
            $_SESSION['site']->onDestroySession();
        }
        if (!isset ($_SESSION['site'])) {
            $_SESSION['site'] = new WebSite();
            $_SESSION['site']->onStartSession();
        }
        include $_SERVER['DOCUMENT_ROOT'] . $siteHome . $defaultPage;
    } else {
        if (isset($_POST['action'])) {
            $_SESSION['currentPage'] = (isset($target))?$target:$_POST['action'];
        } else {
            $_SESSION['currentPage'] = (isset($target))?$target:$_GET['action'];
        }
        include  $_SESSION['currentPage'] . '.php';
    }
} else {
        include $defaultPage;
}

# set a new form secret for the next form
if(!isset($_SESSION['formId'])) {
    $secret = md5(uniqid(rand(), true));
    $_SESSION['formId'] = $secret;
}

?>
