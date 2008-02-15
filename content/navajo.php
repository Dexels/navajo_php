<?php
defined("_JEXEC") or die("Restricted access");

//$joomlaSessionName = session_name();
//session_write_close();


//ini_set("session.save_handler", "files");


//require_once "navajo/NavajoJoomla.php";
//require_once "navajo/sportlinkclubsite.class.php";
require_once "navajo/navajo.php";
require_once "navajo/client/NavajoClient.php";
require_once "navajo/document/NavajoDoc.php";
require_once "navajo/phpclient/NavajoPhpClient.php";
include_once 'navajo/NavajoHandler.php';
//echo 'Session name: '.$joomlaSessionName;

//session_name($joomlaSessionName);
//session_start();

require_once "navajo/NavajoHandler.php";
jimport('joomla.event.plugin');

class plgContentNavajo extends JPlugin
{

	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param object $params  The object that holds the plugin parameters
	 * @since 1.5
	 */
	function plgContentNavajo( &$subject, $params )
	{
//		echo 'createPlugin';
		parent::__construct( $subject, $params );
	}

	/**
	 * Example prepare content method
	 *
	 * Method is called by the view
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 */
	function onPrepareContent( &$article, &$params )
	{
//		echo 'onPrepareContent';
		global $mainframe;

	}

	/**
	 * Example after display title method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 * @return	string
	 */
	function onAfterDisplayTitle( &$article, &$params )
	{
//		echo 'onAfterDisplayTitle';
		global $mainframe;
		return '';
	}

	/**
	 * Example before display content method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 * @return	string
	 */
	function onBeforeDisplayContent( &$article, &$params )
	{
//		echo 'onBeforeDisplayContent';
		global $mainframe;
		$authPlugin = JPluginHelper::getPlugin('content','navajo');
		
		$pluginParams = new JParameter( $authPlugin->params );
		$server = $pluginParams->get('navajoServer');
		$clubId = $pluginParams->get('clubId');
		$navajoUsername = $pluginParams->get('navajoUsername');
		$navajoPassword = $pluginParams->get('navajoPassword');
		startupNavajo($server,$navajoUsername ,$navajoPassword);
		
		//function navajoTags($published, $row, & $params, $page = 0) {
	initNavajo();
	//	$article->text = 'APENOOT!';
	$article->text = replaceTags($article->text);
		
		return '';
	}

	/**
	 * Example after display content method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 * @return	string
	 */
	function onAfterDisplayContent( &$article, &$params )
	{
		global $mainframe;
//		echo 'onAfterDisplayContent';
		
		return '';
	}
}

?>
