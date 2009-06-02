<?php
defined('_JEXEC') or die();

jimport('joomla.event.plugin');
jimport( 'joomla.event.handler' );

require_once JPATH_SITE . "/plugins/content/navajo/client/NavajoClient.php";
require_once JPATH_SITE . "/plugins/content/navajo/document/NavajoDoc.php";

class plgUserNavajo extends JPlugin {

    function plgUserNavajo(& $subject, $config) {
        parent::__construct($subject, $config);
    }

    function onBeforeStoreUser($user, $isnew)
    {
        global $mainframe;

    }

    function onAfterStoreUser($user, $isnew, $succes, $msg)
    {
        global $mainframe;

        $args = array();
        $args['username']    = $user['username'];
        $args['email']         = $user['email'];
        $args['fullname']    = $user['name'];
        $args['password']    = $user['password'];

        if($isnew) {
            // Call a function in the external app to create the user
            // ThirdPartyApp::createUser($user['id'], $args);
        } else {
            // Call a function in the external app to update the user
            // ThirdPartyApp::updateUser($user['id'], $args);
        }
    }

    function onBeforeDeleteUser($user)
    {
        global $mainframe;
    }

    function onAfterDeleteUser($user, $succes, $msg)
    {
        global $mainframe;
    }

    function onLoginUser($user, $options)
    {
        $success = false;
        jimport('joomla.user.helper');

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
    
        $n2 = NavajoClient::processNavajo("vla/sportlinkathlete/ProcessLoginAthleteUser",$n);
        $fullNameProp = $n2->getAbsoluteProperty('/Person/FullName');

        if(isset($fullNameProp) && is_object($fullNameProp)) {
            $user['email'] = $n2->getAbsoluteProperty('/Person/EmailAddress')->getValue();
            $user['password_clear'] = $user['password'];
            $user['fullname'] = $fullNameProp->getValue();
            $user['postcode'] = $n2->getAbsoluteProperty('/Person/ZipCode')->getValue();
            $user['country'] = 'NL';
            $instance =& $this->_getUser($user, $options);
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
    }

    function onLogoutUser($user)
    {
        $table = & JTable::getInstance('session');
        $table->destroy($user['id'], $options['clientid']);

        $my =& JFactory::getUser();
        if($my->get('id') == $user['id']) 
        {
            $my->setLastVisit();
            $session =& JFactory::getSession();
            $session->destroy();
        }
        return true;
    }

    function &_getUser($user, $options = array())
    {
        $instance = new JUser();
        if($id = intval(JUserHelper::getUserId($user['username'])))  {
            $instance->load($id);
            return $instance;
        }

        jimport('joomla.application.component.helper');
        $config   = &JComponentHelper::getParams( 'com_users' );
        $usertype = $config->get( 'new_usertype', 'Registered' );
        $acl =& JFactory::getACL();

        $instance->set( 'id'            , 0 );
        $instance->set( 'name'            , $user['fullname'] );
        $instance->set( 'username'        , $user['username'] );
        $instance->set( 'password_clear', $user['password_clear'] );
        $instance->set( 'email'            , $user['email'] );    // Result should contain an email (check)
        $instance->set( 'gid'            , $acl->get_group_id( '', $usertype));
        $instance->set( 'usertype'        , $usertype );

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
