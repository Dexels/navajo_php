<?php
defined("_JEXEC") or die("Restricted access");

require_once "navajo/index.php";
require_once "navajo/client/NavajoClient.php";
require_once "navajo/document/NavajoDoc.php";
require_once "navajo/phpclient/NavajoPhpClient.php";

$mainframe->registerEvent('onPrepareContent', 'plgContentNavajo');

$user =& JFactory::getUser();

# require_once "navajo/Nav0ajoHandler.php";
jimport('joomla.event.plugin');

class plgContentNavajo extends JPlugin
{
    function plgContentNavajo(&$subject, $params = null)
    {
        parent::__construct( $subject, $params );
    }

    function onPrepareContent(&$article, &$params)
    {
        global $mainframe, $user;
        $authPlugin     = JPluginHelper::getPlugin('content','navajo');
        $pluginParams   = new JParameter( $authPlugin->params );        
        $navajoUsername = '#' . $pluginParams->get('navajoUsername');
        $navajoPassword = '#' . $pluginParams->get('navajoPassword');
        $unionCode      = $pluginParams->get('unionCode');
        $navajoServer   = $pluginParams->get('navajoServer') . '/sportlink/' . strtolower($unionCode) . '/servlet/Postman';

        startupNavajo($navajoServer, $navajoUsername , $navajoPassword);
        initNavajo();
        
        $article->text = replaceTags($article->text);
    }

    function onAfterDisplayTitle(&$article, &$params)
    {
        return '';
    }

    function onBeforeDisplayContent(&$article, &$params)
    {
        return '';
    }

    function onAfterDisplayContent( &$article, &$params )
    {
        return '';
    }
}
?>
