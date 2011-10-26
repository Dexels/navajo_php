<?php
defined("_JEXEC") or die("Restricted access");
jimport('joomla.application.component.controller');

class NavajoController extends JController
{
    function storeNavajo() {

        require_once JPATH_BASE . "/plugins/content/navajo/document/NavajoDoc.php";
        require_once JPATH_BASE . "/plugins/content/navajo/client/NavajoClient.php";
        require_once JPATH_BASE . "/plugins/content/navajo/phpclient/NavajoPhpClient.php";

        NavajoClient :: updateNavajoFromPost();

        # based on the label of the submit, get correct id
        if (isset ($_REQUEST['direction'])) {
            $submit = $_REQUEST['direction'];
            if (isset ($_REQUEST[$submit . ':target'])) {
                $target = $_REQUEST[$submit . ':target'];
            }
            if (isset($_REQUEST[$submit . ':serverCall'])) {
                $_REQUEST['serverCall'] = $_REQUEST[$submit . ':serverCall'];
            }
        }
        
        # call webservice if the formSecret session variable matches the POST variable (to prevent duplicates)
        
        if (isset ($_REQUEST['serverCall'])) {
            $actions = explode(';', $_REQUEST['serverCall']);
            foreach ($actions as $current) {
                $initscr = explode(':', $current);
                try {
                    if (count($initscr) == 2) {
                        $nnn = NavajoClient :: callService($initscr[0], $initscr[1]);
                    } else {
                        $nnn = NavajoClient :: callInitService($initscr[0]);
                    }
                } catch (Exception $e) {
                    $_REQUEST['errormessage'] = $e->getMessage();
                    echo $e->getMessage();
                }
                # store the latest navajo in a session, because we are going to redirect
                navajoSession :: storeNavajo();
            }
        }
        
        if (isset($_REQUEST[$submit . ':id']) || isset($_REQUEST['uri'])) {
            if (isset($_REQUEST['uri'])) {
                header("Location: /index.php/" . $_REQUEST['uri']);
                exit;
            } else if (isset($_REQUEST[$submit . ':id'])) {
                if (isset($_REQUEST[$submit . ':Itemid'])) {
                    header("Location: /index.php" . '?option=com_content&view=article&id=' . $_REQUEST[$submit . ':id'] . '&Itemid=' . $_REQUEST[$submit . ':Itemid']);
                } else { 
                    header("Location: /index.php" . '?option=com_content&view=article&id=' . $_REQUEST[$submit . ':id']);
                }
                exit;
            } else {
               header("Location: /index.php"); 
               exit;
            }
        }

        # require JPATH_BASE .DS.'plugins'.DS.'content'.DS.'navajo'.DS.'NavajoHandler.php';
    }

    function redirect() {
    }
}
?>
