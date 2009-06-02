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
        $navajoUsername = (isset($_SESSION['navajoUsr']))?('#' . $_SESSION['navajoUsr']):('#' . $pluginParams->get('navajoUsername'));
        $navajoPassword = (isset($_SESSION['navajoPwd']))?$_SESSION['navajoPwd']:('#' . $pluginParams->get('navajoPassword'));
        $unionCode      = (isset($_SESSION['unionCode']))?$_SESSION['unionCode']:$pluginParams->get('unionCode');
        $navajoServer   = $pluginParams->get('navajoServer') . '/sportlink/' . strtolower($unionCode) . '/servlet/Postman';
        
        //echo "Server : " . $navajoServer . ", NavajoUsr : " . $navajoUsername . ", NavajoPwd : " . $navajoPassword;            
        
        startupNavajo($navajoServer, $navajoUsername , $navajoPassword);
        
        $n = NavajoClient::callInitService("vla/sportlinkathlete/InitLoginAthleteUser");
        $usr = $n->getAbsoluteProperty('/UserData/Username');
        if(!isset($usr) || !is_object($usr)) {
            return;
        }

        $usr->setValue($username['username']);
        $n->getAbsoluteProperty('/UserData/Password')->setValue($username['password']);
        $n->getAbsoluteProperty('/Club/ClubIdentifier')->setValue($pluginParams->get('navajoUsername'));
        $n2 = NavajoClient::processNavajo("vla/sportlinkathlete/ProcessLoginAthleteUser",$n);
                
        $resultProp = $n2->getAbsoluteProperty('/Authenticated/Ok');
        $result = 'false';
        if(isset($resultProp)) {
            $result = $resultProp->getValue();
            $role = $n2->getAbsoluteProperty('/Authenticated/UserRole')->getValue();
        }
        if( $result == 'true' ) {
            $emm = $n2->getAbsoluteProperty('/Person/EmailAddress')->getValue();
            $nme = $n2->getAbsoluteProperty('/Person/ExternalId')->getValue();
            $fln = $n2->getAbsoluteProperty('/Person/FullName')->getValue();
            $response->name     = $nme;
            $response->fullname = $fln;
            $response->email    = $emm;
            $response->type     = JAUTHENTICATE_STATUS_SUCCESS;
            $response->status   = JAUTHENTICATE_STATUS_SUCCESS;
        } else {                
            # echo "<html><head><title>Fout opgetreden</title></head><body>" .
            #     "<script type='text/javascript'>alert('Inloggen niet gelukt, probeer het nog eens of neem contact op met uw ledenadministrateur');</script>" .
            #     "<p>Klik <a href='javascript:history.back()'>hier</a> om terug te gaan</p></body></html>";
            # exit();
            $response->type     = JAUTHENTICATE_STATUS_FAILURE;
            $response->status   = JAUTHENTICATE_STATUS_FAILURE;
            $response->error_message        = 'Could not authenticate';
            return false;
        }
        return;
    }
}
?>
