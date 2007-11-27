<?php
require_once "navajo/NavajoPhpClient.php";

global $siteHome, $defaultPage, $siteContent, $siteTitle;

$siteTitle   = 'SeeMe - beheer';
$siteContent = '';
$defaultPage = '';
$siteHome    = 'NavajoPhp/script/';

$n = new Navajo();

include "sportlinkclubsite.class.php";
require_once (JApplicationHelper :: getPath('front_html', 'com_navajo'));

$session = JFactory::getSession();
if($session->has('site','navajo')) {
	$site = new ClubSite();
    $session->set('site',$site,'navajo');
	$site->onStartSession();
 
}

$session->get('site','navajo')->echoHeader();

require_once ( 'navajo/NavajoHandler.php' );

$session->get('site','navajo')->echoFooter();

?>
