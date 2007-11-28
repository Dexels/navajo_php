<?php

NavajoClient :: updateNavajoFromPost();

# based on the label of the submit, get correct id
if (isset ($_REQUEST['submit'])) {
    $submit = $_REQUEST['submit'];
    if (isset ($_REQUEST[$submit . ':target'])) {
        $target = $_REQUEST[$submit . ':target'];
    }
    if (isset($_REQUEST[$submit . ':serverCall'])) {
        $_REQUEST['serverCall'] = $_REQUEST[$submit . ':serverCall'];
    }
}

# call webservice if the formSecret session variable matches the POST variable (to prevent duplicates)

if (isset ($_REQUEST['serverCall']) && isset($_SESSION['formId'])) {
    if (strcasecmp($_POST['form_id'], $_SESSION['formId']) === 0) {
        $actions = explode(';', $_REQUEST['serverCall']);
        foreach ($actions as $current) {
            $initscr = explode(':', $current);
            try {
                if (count($initscr) == 2) {
                    $nnn = NavajoClient :: callService($initscr[0], $initscr[1]);
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
}

/*
if (isset($target) || isset ($_REQUEST['action'])) {
    if ($_REQUEST['action'] == 'exit') {
        if (isset ($_SESSION['site'])) {
            $_SESSION['site']->onDestroySession();
        }
        session_destroy();
        unset ($_REQUEST['action']);
        if (!isset ($_SESSION['site'])) {
            $_SESSION['site'] = new WebSite();
            $_SESSION['site']->onStartSession();
        }
        include $siteHome . $defaultPage . '.php';
    } else {
        $_SESSION['currentPage'] = (isset($target))?$target:$_REQUEST['action'];
        include $_SERVER['DOCUMENT_ROOT'] . $siteHome . $_SESSION['currentPage'] . '.php';
    }
}
*/
# set a new form secret for the next form
if(!isset($_SESSION['formId'])) {
    $secret = md5(uniqid(rand(), true));
    $_SESSION['formId'] = $secret;
}

?>