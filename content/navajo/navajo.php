<?php
jimport('joomla.event.plugin');

//$joomlaSessionName = session_name();
//session_write_close();
defined("_JEXEC") or die("Restricted access");

//ini_set("session.save_handler", "files");


//require_once "navajo/NavajoJoomla.php";
//require_once "navajo/sportlinkclubsite.class.php";
require_once "navajo/client/NavajoClient.php";
require_once "navajo/document/NavajoDoc.php";
require_once "navajo/phpclient/NavajoPhpClient.php";

//session_name($joomlaSessionName);
//session_start();


function initNavajo() {

    if (isset ($_REQUEST["direction"])) {
        $dir = $_REQUEST["direction"];
        $servCall = $_REQUEST[$dir . ":serverCall"];
    } else {
        if (isset ($_REQUEST["serverCall"])) {
            $servCall = $_REQUEST["serverCall"];
        }
    }

    if (isset ($servCall)) {
        NavajoClient :: updateNavajoFromPost();

        $actions = explode(";", $servCall);
        foreach ($actions as $current) {
            $initscr = explode(":", $current);
            try {
                if (count($initscr) == 2) {
                    # echo "calling service: " . $initscr[0] . " - " . $initscr[1];
                    $nnn = NavajoClient :: callService($initscr[0], $initscr[1]);
                } else {
                    # $nnn = NavajoClient :: callInitService($initscr[0]);
                }
            } catch (Exception $e) {
                $_REQUEST["errormessage"] = $e->getMessage();
                echo $e->getMessage();
            }
        }
    }

//    $session = JFactory :: getSession();
//    if (!$session->has("site", "navajo")) {
//        $site = new ClubSite();
//        $session->set("site", $site, "navajo");
//        $site->onStartSession();
///    }
}

function navajoTags($published, $row, & $params, $page = 0) {
 	 $currentPublished = $published;
   $published->text = replaceTags($published->text);
}

function navajo($text) {
	replaceTags($text);
}

function endnavajo() {
	if (!isset ($_POST['redirect'])) {
	    $_SESSION['site']->echoFooter();
	}
}
function startnavajo() {
	if (!isset ($_POST['redirect'])) {
	    $_SESSION['site']->echoHeader();
	}
	
}


function replaceTags($text) {
     initNavajo();
     
    $regex = "#{(//|errors|back|label|classsuffix|showall|showmessage|showmethod|element|table|submit|service|setvalue|setusername)(.*?)}#s";
	$result = startNavajoInclude() . preg_replace_callback($regex, "navajoTagReplacer",$text) . endNavajoInclude();
	return $result;
}

function screenInclude($params) {
    $page = $params["page"];
    $_SESSION["currentPage"] = $page;
    include JPATH_SITE . "/components/com_navajo/navajo.php";
}

function replaceTag($tag, $paramstring) {
	 $paramstring = "   " . $paramstring;
	//echo $tag;
    $params = explode(" ", trim($paramstring));
    $result = keyValueInterpreter($params);

    if ($tag == "showall") {
        navajoInclude($result);
    }
    if ($tag == "showmessage") {
        messageInclude($result);
    }
    if ($tag == "showmethod") {
        methodInclude($result);
    }        
    if ($tag == "//") {
        echo "<!-- " . $paramstring . "-->";
    }
    if ($tag == "element") {
        propertyInclude($result);
    }
    if ($tag == "label") {
        descriptionInclude($result);
    }
    if ($tag == "errors") {
        errorMessageInclude($result);
    }
    if ($tag == "table") {
        tableInclude($result);
    }
    if ($tag == "submit") {
        submitInclude($result);
    }
    if ($tag == "service") {
        callService($result);
    }
    if ($tag == "setvalue") {
        valueInclude($result);
    }
    if ($tag == "setusername") {
        usernameInclude($result);
    }
    if ($tag == "classsuffix") {
        setClassSuffix($result);
    }
    if ($tag == "back") {
        back($result);
    }

    print "\n";
}

function navajoTagReplacer($matches) {
	//echo count($matches);
   //print_r($matches);
    if (count($matches) == 0) {
        return;
    }
    ob_start();

	 $tagname = $matches[1];
    replaceTag($tagname, $matches[2]);
    $result = ob_get_contents();
    ob_end_clean();
    //echo $result;
    return $result;
}



function errorMessageInclude($result) {
    if (isset ($_REQUEST["errormessage"])) {
        echo $_REQUEST["errormessage"];
    }
}

function startNavajoInclude() {
    $msg = "<!-- Starting navajo form -->\n" .
    "<div class='navajo'><form action='./components/com_navajo/navajo/NavajoHandler.php' method='POST' enctype='multipart/form-data'>\n" .
    "<input type='hidden' name='option' value='com_navajo'/>\n" .
    "<input type='hidden' name='redirect' value='true'/>\n" .
    "<input type='hidden' name='view' value='article'/>\n";
    return $msg;
}

function endNavajoInclude() {
    return "</form></div>\n";
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

function setClassSuffix($matches) {
    global $currentClassSuffix;
    $suffix = $matches["name"];
    $currentClassSuffix = $suffix;
}

function navajoInclude($matches) {
    $currentNavajo = NavajoClient :: getCurrentNavajo();

    if (isset ($matches["service"])) {
        $navajo = $matches["service"];
    } else {
        $navajo = $currentNavajo;
    }
    NavajoPhpClient :: showNavajo(getNavajo($navajo), $matches);
}

function messageInclude($matches) {
   $currentNavajo = NavajoClient :: getCurrentNavajo();
    $navajo = getNavajo($currentNavajo);
    $messages = $navajo->getMessages();
    foreach ($messages as $current) {
    	if ($current->getName() ==  $matches["name"]) {
    		NavajoPhpClient :: showMessage($navajo, $current, $matches);  
    	}   
    }
}

function methodInclude($matches) {
    $currentNavajo = NavajoClient :: getCurrentNavajo();
    $navajo = getNavajo($currentNavajo);
    $methods = $navajo->getAllMethods();
    foreach ($methods as $current) {
    	if ($current->getName() ==  $matches["name"]) {
    		NavajoPhpClient :: showMethod($navajo, $current, $matches);  
    	}   
    }
}

function propertyInclude($matches) {
    global $currentClassSuffix;
    $currentNavajo = NavajoClient :: getCurrentNavajo();

    if (isset ($matches["service"])) {
        $navajo = $matches["service"];
    } else {
        $navajo = $currentNavajo;
    }
    $path = $matches["name"];
    $showdesc = false;
    $forceout = "";
    $suffix = "";
    if (isset ($matches["showlabel"])) {
        $showdesc = $matches["showlabel"];
    }
    if (isset ($matches["readonly"])) {
        $forceout = true;
    }
    if (isset ($matches["classsuffix"])) {
        $suffix = $matches["classsuffix"];
    } else {
        $suffix = $currentClassSuffix;
    }
    NavajoPhpClient :: showAbsoluteProperty($navajo, $path, $matches, $forceout, $showdesc, $suffix);
}

function descriptionInclude($matches) {
    global $currentClassSuffix;
    $currentNavajo = NavajoClient :: getCurrentNavajo();

    if (isset ($matches["service"])) {
        $navajo = $matches["service"];
    } else {
        $navajo = $currentNavajo;
    }

    $path = $matches["element"];
    $forceout = "";
    $suffix = "";

    if (isset ($matches["classsuffix"])) {
        $suffix = $matches["classsuffix"];
    } else {
        $suffix = $currentClassSuffix;
    }
    NavajoPhpClient :: showAbsoluteDescription($navajo, $path, $matches, $suffix);

}

function valueInclude($matches) {
    $currentNavajo = NavajoClient :: getCurrentNavajo();
    if (isset ($matches["service"])) {
        $navajo = $matches["service"];
    } else {
        $navajo = $currentNavajo;
    }
    if (!is_null($navajo)) {
        $path = $matches["element"];
        $value = $matches["value"];
        NavajoPhpClient :: setValue($navajo, $path, $value);
    } else {
        echo "<p class='error'>No navajo found: " . $navajo . "</p>";
    }
}

function usernameInclude($matches) {
    $currentNavajo = NavajoClient :: getCurrentNavajo();
    if (isset ($matches["service"])) {
        $navajo = $matches["service"];
    } else {
        $navajo = $currentNavajo;
    }
    if (!is_null($navajo)) {
        $path = $matches["element"];
        $user = & JFactory :: getUser();
        $value = $user->get("username");
        NavajoPhpClient :: setValue($navajo, $path, $value);
    } else {
        echo "<p class='error'>No navajo found: " . $navajo . "</p>";
    }
}

function back($matches) {
    $label = $matches["label"];
    $back = $_REQUEST["back"];

    echo $back;
    if ($back != null) {
        echo "<a href='index.php?view=article&option=com_content&id=$back'>$label</a>";
    } else {
        echo $label;
    }
}

function tableInclude($matches) {
    $currentNavajo = NavajoClient :: getCurrentNavajo();
    if (isset ($matches["service"])) {
        $navajo = $matches["service"];
    } else {
        $navajo = $currentNavajo;
    }
    $path = $matches["path"];
    $columnString = $matches["columns"];

    $columns = explode(",", $columnString);

    NavajoPhpClient :: showTable($navajo, $path, $columns, $matches);
}

function submitInclude($matches) {

    $services = $matches["action"];
    $label = $matches["label"];
    $config = JFactory :: getConfig();
    
    # "target" attribute is set to the alias of an article; get corresponding articleid from J! database
    
    $alias = $matches["target"];       
    $db =& JFactory::getDBO();
    $query = "SELECT id FROM #__content WHERE title_alias = '".$alias."'";
    $db->setQuery( $query );
    $id = $db->loadResult();          
    
    # Ugly code -> fall back to default "show all" article. Put this in some global later
    if ($id == null) {
        $query = "SELECT id FROM #__content WHERE title_alias = 'show-all'";
    	$db->setQuery( $query );
    	$id = $db->loadResult();                
    }   
    
    if (isset ($_REQUEST["Itemid"])) {
        echo "<input type='hidden' name='" . $label . ":itemid' value='" . $_REQUEST["Itemid"] . "'/>";
    }
    echo "<input type='submit' name='direction' value='" . $label . "'/>";
    echo "<input type='hidden' name='" . $label . "' value='" . $id . "'/>";
    echo "<input type='hidden' name='" . $label . ":serverCall' value='" . $services . "'/>";
    echo "<input type='hidden' name='" . $label . ":id' value='" . $id . "'/>";
    echo "<input type='hidden' name='joomlaSession' value='" . session_name() . "'/>";
    echo "<input type='hidden' name='joomlaPath' value='" . $config->getValue("config.sitename") . "'/>";

}

function propertyReplacer(& $matches) {
    $params = keyValueInterpreter(trim($matches[1]));
}

function tableReplacer(& $matches) {
    $params = keyValueInterpreter(trim($matches[1]));
    return navajoInclude($params);
}

function keyValueInterpreter($rows) {
    $result = array ();
    foreach ($rows as $row) {

        if (trim($row) != "") {
            $row = trim($row);
            $cols = explode("=", $row);
            if (count($cols > 1)) {
                if (!isset ($cols[1])) {
                    $result[$cols[0]] = true;
                } else {
                    $result[$cols[0]] = $cols[1];
                }
            }
        }
    }
    return $result;
}


?>
