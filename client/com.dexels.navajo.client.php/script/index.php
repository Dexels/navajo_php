<?php

require_once "navajo/NavajoPhpClient.php";

global $siteHome,$defaultPage,$siteContent,$siteTitle;

$siteTitle = 'Sportlink Club Site';
$siteContent = 'Quatsch';
$defaultPage = 'InitGetClubTeams';
$siteHome = 'NavajoPhp/script/';

include "sportlinkclubsite.class.php";
	session_start();	


if(!isset($_SESSION['site'])) {
	$_SESSION['site'] = new ClubSite();
    $_SESSION['site']->onStartSession();
 
}

$_SESSION['site']->echoHeader();

include 'navajo/NavajoHandler.php';

$_SESSION['site']->echoFooter();

?>