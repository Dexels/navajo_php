<?php
# the index.php script can be used to echo a default header and footer and to include the NavajoHandler
# typically scripts are called like this: index.php?action=example2

require_once('./navajo/website.class.php');
@session_start();
require_once('./navajo/StartUpServer.php');

if (!isset ($_SESSION['site'])) {
    $_SESSION['site'] = new WebSite();
}

$_SESSION['site']->echoHeader();

# the NavajoHandler includes the correct PHP file (e.g. example2.php), deals with updates
include "./navajo/NavajoHandler.php";

$_SESSION['site']->echoFooter();
?>
