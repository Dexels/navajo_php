<?php
/**
 * @package		HelloWorld
 * @license		GNU/GPL, see LICENSE.php
 */
defined("_JEXEC") or die("Restricted access");

jimport('joomla.application.component.controller');

$joomlaSessionName = session_name();
//phpinfo();ABDAY_1
//echo $joomlaSessionName;

JPATH_BASE .DS.'plugins'.DS.'content'.DS.'navajo'.DS.'navajo.php';
;
//echo 'hoei: '.$joomlaSessionName;
session_write_close();

//ini_set("session.save_handler", "files");
//require_once( JPATH_BASE .DS.'includes'.DS.'toolbar.php' );

//echo session_name();

//require_once "navajo/NavajoJoomla.php";
//require_once "navajo/sportlinkclubsite.class.php";
//include_once JPATH_BASE .DS.'plugins'.DS.'content'.DS.'navajo'.DS."navajo.php";

/**
 * Hello World Component Controller
 *
 * @package		HelloWorld
 */
class NavajoController extends JController
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	/*
	 function display()
	 {
		parent::display();
		}


		*/

	function storeNavajo() {
		//print_r( $_REQUEST);
//		$joomlaSessionName = session_name();
	//	session_write_close();
//		require JPATH_BASE .DS.'plugins'.DS.'content'.DS.'navajo'.DS."client/NavajoClient.php";
//		require JPATH_BASE .DS.'plugins'.DS.'content'.DS.'navajo'.DS."document/NavajoDoc.php";
//		require JPATH_BASE .DS.'plugins'.DS.'content'.DS.'navajo'.DS."phpclient/NavajoPhpClient.php";
//		require JPATH_BASE .DS.'plugins'.DS.'content'.DS.'navajo'.DS.'NavajoHandler.php';
		//echo 'Session name: '.$joomlaSessionName;
	//	session_name($joomlaSessionName);
//		session_start();

	require JPATH_BASE .DS.'plugins'.DS.'content'.DS.'navajo'.DS.'NavajoHandler.php';
			jimport('joomla.event.plugin');
		//		NavajoClient::updateNavajoFromPost();
	}

	function redirect() {
//		echo "in redirect";
	}
}
?>
