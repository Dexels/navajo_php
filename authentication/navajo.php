<?php
defined('_JEXEC') or die();

jimport('joomla.event.plugin');
require_once JPATH_SITE . "/plugins/content/navajo/client/NavajoClient.php";
require_once JPATH_SITE . "/plugins/content/navajo/document/NavajoDoc.php";

class plgAuthenticationNavajo extends JPlugin
{
    function plgAuthenticationNavajo(& $subject) {
        parent::__construct($subject);
    }

    function onAuthenticate( $username, $password, &$response )
    {
        global $mainframe;
        
        $authPlugin     = JPluginHelper::getPlugin('content','navajo');
        $pluginParams   = new JParameter( $authPlugin->params );        
        $navajoUsername = '#' . strtoupper($pluginParams->get('navajoUsername'));
        $navajoPassword = '#' . strtoupper($pluginParams->get('navajoPassword'));
        $unionCode      = $pluginParams->get('unionCode');
        $navajoServer   = $pluginParams->get('navajoServer') . '/sportlink/' . strtolower($unionCode) . '/servlet/Postman';
        
        //echo "Server : " . $navajoServer . ", NavajoUsr : " . $navajoUsername . ", NavajoPwd : " . $navajoPassword;            
        
        startupNavajo($navajoServer, $navajoUsername , $navajoPassword);
        
        $n = NavajoClient::callInitService("vla/sportlinkathlete/InitLoginAthleteUser");
	if(is_null($n)) {
            $response->status   = JAUTHENTICATE_STATUS_FAILURE;
            $response->error_message = 'Could not authenticate';
	    return;
	}
        $usr = $n->getAbsoluteProperty('/UserData/Username');
        if(!isset($usr) || !is_object($usr)) {
            return;
        }

        $usr->setValue(strtoupper($username['username']));
        $n->getAbsoluteProperty('/UserData/Password')->setValue(strtoupper($username['password']));
        $n->getAbsoluteProperty('/Club/ClubIdentifier')->setValue($navajoUsername);
        $n2 = NavajoClient::processNavajo("vla/sportlinkathlete/ProcessLoginAthleteUser",$n);
                
        $resultProp = $n2->getAbsoluteProperty('/Authenticated/Ok');
        $result = 'false';
        if(isset($resultProp)) {
            $result = $resultProp->getValue();
            $role = $n2->getAbsoluteProperty('/Authenticated/UserRole')->getValue();
        }
        if( $result == 'true' && ( $role == 'ATHLETEUSER' OR $role == 'CLUB_ADMIN' ) ) {
            if ($role != 'CLUB_ADMIN') {
                $emm = $n2->getAbsoluteProperty('/Person/EmailAddress')->getValue();
                $nme = $n2->getAbsoluteProperty('/Person/ExternalId')->getValue();
                $fln = $n2->getAbsoluteProperty('/Person/FullName')->getValue();
            } else { 
                $emm = strtolower($username['username']) . '@' . strtolower($n->getAbsoluteProperty('/Club/ClubIdentifier')->getValue()) . '.slclubsite.nl';
                $nme = strtolower($username['username']);
                $fln = strtolower($username['username']); 
            }
            
            # Check for duplicate e-mailaddress
            $db =& JFactory::getDBO();

            $query = 'SELECT COUNT(*) AS cnt FROM `#__users` WHERE email = \'' . $emm . '\'';
            $db->setQuery( $query );
            $result = $db->loadObject();
            $duplEmail = ($result->cnt > 0)?TRUE:FALSE;
            if ($duplEmail) {
                $emm = strtolower($n2->getAbsoluteProperty('/Person/ExternalId')->getValue()) . '@' . strtolower($n2->getAbsoluteProperty('/Club/ClubIdentifier')->getValue()) . '.slclubsite.nl';
            }

            $response->fullname = $fln;
            $response->email    = $emm;
            $response->status   = JAUTHENTICATE_STATUS_SUCCESS;
            $response->error_message = 'Correct';
            $response->name     = $nme;
        } else {                
            $response->status   = JAUTHENTICATE_STATUS_FAILURE;
            $response->error_message = 'Could not authenticate';
        }
    }
}
?>
