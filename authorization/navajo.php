<?php

?><?php
/**
 * @version    $Id$
 * @package    Joomla.Tutorials
 * @subpackage Plugins
 * @license    GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.event.plugin');
require_once JPATH_SITE . "/plugins/content/navajo/client/NavajoClient.php";
require_once JPATH_SITE . "/plugins/content/navajo/document/NavajoDoc.php";

/**
 * Example Authentication Plugin.  Based on the example.php plugin in the Joomla! Core installation
 *
 * @package    Joomla.Tutorials
 * @subpackage Plugins
 * @license    GNU/GPL
 */
class plgAuthenticationNavajo extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @since 1.5
	 */
	function plgAuthenticationNavajo(& $subject) {
		//echo 'SHIIIIIIIIIIIt';
		//$acl			=& JFactory::getACL();
		parent::__construct($subject);
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 * This example uses simple authentication - it checks if the password is the reverse
	 * of the username (and the user exists in the database).
	 *
	 * @access    public
	 * @param    string    $username    Username for authentication
	 * @param    string    $password    Password for authentication
	 * @param    object    $response    Authentication response object
	 * @return    boolean
	 * @since 1.5
	 */
	function onAuthenticate( $username, $password, &$response )
	{
	
	  /*
		 * Here you would do whatever you need for an authentication routine with the credentials
		 *
		 * In this example the mixed variable $return would be set to false
		 * if the authentication routine fails or an integer userid of the authenticated
		 * user if the routine passes
		 */
		//        $db =& JFactory::getDBO();
		//        $query = 'SELECT `id`'
		//            . ' FROM #__users'
		//            . ' WHERE username=' . $db->quote( $username['username'] );
		//        $db->setQuery( $query );
		//        $result = $db->loadResult();
		//        print_r($result);
	//	$pluginParams = new JParameter( $this->params );
//'distel:8080/NavajoStandardEdition/Postman'
//'demo'
		$authPlugin = JPluginHelper::getPlugin('content','navajo');
		$pluginParams = new JParameter( $authPlugin->params );
		$server = $pluginParams->get('navajoServer');
		$clubId = $pluginParams->get('clubId');
		$navajoUsername = $pluginParams->get('navajoUsername');
		$navajoPassword = $pluginParams->get('navajoPassword');
		startupNavajo($server,$navajoUsername ,$navajoPassword);
		$n = NavajoClient::callInitService("vla/sportlinkathlete/InitLoginAthleteUser");
		$usr = $n->getAbsoluteProperty('/UserData/Username');
		if(!isset($usr) || !is_object($usr)) {
			echo 'Error: No username found.';
			return;			
		}
		$usr->setValue($username['username']);
		$n->getAbsoluteProperty('/UserData/Password')->setValue($username['password']);
		$n->getAbsoluteProperty('/Club/ClubIdentifier')->setValue($clubId);
		$n2 = NavajoClient::processNavajo("vla/sportlinkathlete/ProcessLoginAthleteUser",$n);
				//$username['username']== $username['password']
		$resultProp = $n2->getAbsoluteProperty('/Authenticated/Ok');
		$result = 'false';
		if(isset($resultProp)) {
			$result = $resultProp->getValue();
			$role = $n2->getAbsoluteProperty('/Authenticated/UserRole')->getValue();
		}
		if( $result == 'true' ) {
		    $emm = $n2->getAbsoluteProperty('/Person/EmailAddress')->getValue();
			$response->email = $emm;
			$response->status = JAUTHENTICATE_STATUS_SUCCESS;
		} else {
				
		   $response->status = JAUTHENTICATE_STATUS_FAILURE;
		//	$response->status = JAUTHENTICATE_STATUS_SUCCESS;
			//$response->error_message = 'Invalid username and password';

		}
		return;
	}

	function callService($matches) {

		$currentNavajo = NavajoClient :: getCurrentNavajo();

		if (isset ($matches["name"])) {
			$navajo = $matches["name"];
		} else {
			$navajo = null;
		}
		$refresh = null;
		if (isset ($matches["refresh"])) {
			$refresh = $matches["refresh"];
		}
		if (isset ($matches["input"])) {
			$input = $matches["input"];
		} else {
			$input = $currentNavajo;
		}
		print "<!-- Calling service: $navajo.  -->";
		$n = getNavajo($navajo);
		if ($n != null) {
			// navajo present;
			if ($refresh == null || $refresh == false) {
				// no forced refresh
				if ($input == null) {
					print "<!-- Require service: $navajo. Already present, so no service has been called -->";
					return;
				}
			}
		}

		if ($input == null) {
			$res = NavajoClient :: callInitService($navajo);
		} else {
			$res = NavajoClient :: callService($input, $navajo);
		}
		//$res->printXml();
	}
}

?>