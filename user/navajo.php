<?php
/**
 * @version		$Id$
 * @package		Joomla
 * @subpackage	JFramework
 * @copyright	Copyright (C) 2005 - 2007 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.event.plugin');
require_once JPATH_SITE . "/plugins/content/navajo/client/NavajoClient.php";
require_once JPATH_SITE . "/plugins/content/navajo/document/NavajoDoc.php";

/**
 * Example User Plugin
 *
 * @author		Johan Janssens  <johan.janssens@joomla.org>
 * @package		Joomla
 * @subpackage	JFramework
 * @since 		1.5
 */
class plgUserNavajo extends JPlugin {

	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function plgUserNavajo(& $subject, $config) {
		parent::__construct($subject, $config);
	}

	/**
	 * Example store user method
	 *
	 * Method is called before user data is stored in the database
	 *
	 * @param 	array		holds the old user data
	 * @param 	boolean		true if a new user is stored
	 */
	function onBeforeStoreUser($user, $isnew)
	{
		global $mainframe;

	}

	/**
	 * Example store user method
	 *
	 * Method is called after user data is stored in the database
	 *
	 * @param 	array		holds the new user data
	 * @param 	boolean		true if a new user is stored
	 * @param	boolean		true if user was succesfully stored in the database
	 * @param	string		message
	 */
	function onAfterStoreUser($user, $isnew, $succes, $msg)
	{
		global $mainframe;

		/*
		 * convert the user parameters passed to the event to a format the
		 * external appliction
		 */

		$args = array();
		$args['username']	= $user['username'];
		$args['email'] 		= $user['email'];
		$args['fullname']	= $user['name'];
		$args['password']	= $user['password'];

		if($isnew) {
			// Call a function in the external app to create the user
			// ThirdPartyApp::createUser($user['id'], $args);
		} else {
			// Call a function in the external app to update the user
			// ThirdPartyApp::updateUser($user['id'], $args);
		}
	}

	/**
	 * Example store user method
	 *
	 * Method is called before user data is deleted from the database
	 *
	 * @param 	array		holds the user data
	 */
	function onBeforeDeleteUser($user)
	{
		global $mainframe;

	}

	/**
	 * Example store user method
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param 	array		holds the user data
	 * @param	boolean		true if user was succesfully stored in the database
	 * @param	string		message
	 */
	function onAfterDeleteUser($user, $succes, $msg)
	{
		global $mainframe;

		/*
		 * only the $user['id'] exists and carries valid information
		 */

		// Call a function in the external app to delete the user
		// ThirdPartyApp::deleteUser($user['id']);

	}

	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @access	public
	 * @param 	array 	holds the user data
	 * @param 	array    extra options
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function onLoginUser($user, $options)
	{
		// Initialize variables
		$success = false;
		jimport('joomla.user.helper');

		//		, 0 );
		//		$instance->set( 'name'			, $user['fullname'] );
		//		$instance->set( 'username'		, $user['username'] );
		//		$instance->set( 'password_clear', $user['password_clear'] );
		//		$instance->set( 'email'			, $user['email'] );	// Result should contain an email (check)
		//		$instance->set( 'gid'			, $acl->get_group_id( '', $usertype));
		//		$instance->set( 'usertype'		, $usertype );
		//

		$authPlugin = JPluginHelper::getPlugin('content','navajo');
		if(!isset($authPlugin)||$authPlugin==null) {
			error_log('Navajo Authorization plugin not found. Install that plugin first!');
			return;
		}

		$pluginParams = new JParameter( $authPlugin->params );
		$server = $pluginParams->get('navajoServer');
		$clubId = $pluginParams->get('clubId');
		$navajoUsername = $pluginParams->get('navajoUsername');
		$navajoPassword = $pluginParams->get('navajoPassword');
		startupNavajo($server,$navajoUsername ,$navajoPassword);
		$n = NavajoClient::callInitService("vla/sportlinkathlete/InitLoginAthleteUser");
		$usr = $n->getAbsoluteProperty('/UserData/Username');
		if(!isset($usr) || !is_object($usr)) {
			echo('Error property not found');
			return;
		}
		$usr->setValue($user['username']);
		$n->getAbsoluteProperty('/UserData/Password')->setValue($user['password']);
		$n->getAbsoluteProperty('/Club/ClubIdentifier')->setValue($clubId);
		$n2 = NavajoClient::processNavajo("vla/sportlinkathlete/ProcessLoginAthleteUser",$n);
		file_put_contents("c:/wamp/www/gelul.txt",$n2->saveXml(),FILE_APPEND);
		$fullNameProp = $n2->getAbsoluteProperty('/Person/FullName');

		if(isset($fullNameProp) && is_object($fullNameProp)) {
			$user['email'] = $n2->getAbsoluteProperty('/Person/EmailAddress')->getValue();
			$user['password_clear'] = $user['password'];
			$user['fullname'] = $fullNameProp->getValue();
			$user['postcode'] = $n2->getAbsoluteProperty('/Person/ZipCode')->getValue();
			$user['country'] = 'NL';
			$instance =& $this->_getUser($user, $options);
			file_put_contents("c:/wamp/www/gezwam.txt",'user login ok',FILE_APPEND);
			return $instance;
		} else {
			$user['email'] = 'albertus@aep.net';
			$user['password_clear'] = 'oooe';
			$user['fullname'] = 'Albertus den Aep';
			$user['postcode'] = '6953RP';
			$user['country'] = 'NL';
			$instance =& $this->_getUser($user, $options);
			return $instance;
		}
		//	$user['gender'] = $n2->getAbsoluteProperty('/Person/FullName')->getValue();

			


		//$instance->
		//		echo 'UUUUUUUUUUSSSSSEEEEEEEEEEERRR';


		//ThirdPartyApp::loginUser($user['username'], $user['password']);

	}

	/**
	 * This method should handle any logout logic and report back to the subject
	 *
	 * @access public
	 * @param array holds the user data
	 * @return boolean True on success
	 * @since 1.5
	 */
	function onLogoutUser($user)
	{
		// Initialize variables
		$success = false;

		/*
		 * Here you would do whatever you need for a logout routine with the credentials
		 *
		 * In this example the boolean variable $success would be set to true
		 * if the logout routine succeeds
		 */

		//ThirdPartyApp::loginUser($user['username'], $user['password']);

		return $success;
	}

	/**
	 * Copied from joomla.php (user plugin)
	 *
	 * @param unknown_type $user
	 * @param unknown_type $options
	 * @return unknown
	 */
	function &_getUser($user, $options = array())
	{
		$instance = new JUser();
		if($id = intval(JUserHelper::getUserId($user['username'])))  {
			$instance->load($id);
			return $instance;
		}

		//TODO : move this out of the plugin
		jimport('joomla.application.component.helper');
		$config   = &JComponentHelper::getParams( 'com_users' );
		$usertype = $config->get( 'new_usertype', 'Registered' );
		$acl =& JFactory::getACL();

		$instance->set( 'id'			, 0 );
		$instance->set( 'name'			, $user['fullname'] );
		$instance->set( 'username'		, $user['username'] );
		$instance->set( 'password_clear', $user['password_clear'] );
		$instance->set( 'email'			, $user['email'] );	// Result should contain an email (check)
		$instance->set( 'gid'			, $acl->get_group_id( '', $usertype));
		$instance->set( 'usertype'		, $usertype );

		//If autoregister is set let's register the user
		$autoregister = isset($options['autoregister']) ? $options['autoregister'] :  $this->params->get('autoregister', 1);

		if($autoregister)
		{
			if(!$instance->save()) {
				return JError::raiseWarning('SOME_ERROR_CODE', $instance->getError());
			}
		}

		return $instance;
	}
}

?>