<?php

require_once "navajo/NavajoPhpClient.php";

global $siteHome, $defaultPage, $siteContent, $siteTitle;

$siteTitle   = 'Sportlink Club - iDEAL';
$siteContent = 'iDEAL betalingen in de Sportlink Club';
$defaultPage = 'ideal_betaalpagina';
$siteHome    = '/NavajoPhp/iDEAL/';

include "website.class.php";
session_start();

if (!isset ($_SESSION['site'])) {
    $_SESSION['site'] = new WebSite();
    $_SESSION['site']->onStartSession();
}

if (!isset ($_POST['redirect'])) {
    $_SESSION['site']->echoPageHeader();
}

include "navajo/NavajoHandler.php";

if (!isset ($_POST['redirect'])) {
    $_SESSION['site']->echoPageFooter();
}
?>