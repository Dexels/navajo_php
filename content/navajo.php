<?php
defined("_JEXEC") or die("Restricted access");

require_once "navajo/index.php";
require_once "navajo/client/NavajoClient.php";
require_once "navajo/document/NavajoDoc.php";
require_once "navajo/phpclient/NavajoPhpClient.php";
require_once "navajo/NavajoHandler.php";
jimport('joomla.event.plugin');

class plgContentNavajo extends JPlugin
{
	function plgContentNavajo( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}

	function onPrepareContent( &$article, &$params )
	{
		global $mainframe;

	}

	function onAfterDisplayTitle( &$article, &$params )
	{
		global $mainframe;
		return '';
	}

	function onBeforeDisplayContent( &$article, &$params )
	{
		global $mainframe;
		
		$authPlugin = JPluginHelper::getPlugin('content','navajo');
		$pluginParams = new JParameter( $authPlugin->params );		
		$navajoUsername = (isset($_SESSION['navajoUsr']))?('#' . $_SESSION['navajoUsr']):('#' . $pluginParams->get('navajoUsername'));
		$navajoPassword = (isset($_SESSION['navajoPwd']))?$_SESSION['navajoPwd']:('#' . $pluginParams->get('navajoPassword'));
		$unionCode      = (isset($_SESSION['unionCode']))?$_SESSION['unionCode']:$pluginParams->get('unionCode');
                if ( isset($_SESSION['navajoServer']) ) {
                    $navajoServer = $_SESSION['navajoServer'];
                } else {
                    if ( isset($pluginParams->get('navajoPostman') ) {
                        $navajoServer = $pluginParams->get('navajoPostman');
                    } else {
                        $navajoServer = $pluginParams->get('navajoServer') . '/sportlink/' . strtolower($unionCode) . '/servlet/Postman';
                    }
                }

		startupNavajo($navajoServer, $navajoUsername , $navajoPassword);
		
		initNavajo();
		
		$article->text = replaceTags($article->text);
		
		return '';
	}

	function onAfterDisplayContent( &$article, &$params )
	{
		global $mainframe;
		return '';
	}
}

?>
