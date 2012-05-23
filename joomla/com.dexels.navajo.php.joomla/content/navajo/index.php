<?php
defined("_JEXEC") or die("Restricted access");

require_once "document/NavajoDoc.php";
require_once "client/NavajoClient.php";
require_once "phpclient/NavajoPhpClient.php";

$joomlaUser =& JFactory::getUser();
$username = $joomlaUser->get('username');

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
}

function navajoTags($published, $row, & $params, $page = 0) {
    $currentPublished = $published;
    $published->text = replaceTags($published->text);
}

function navajo($text) {
    replaceTags($text);
}

function endnavajo() {
#    if (!isset ($_POST['redirect'])) {
#        $_SESSION['site']->echoFooter();
#    }
}

function startnavajo() {
#    if (!isset ($_POST['redirect'])) {
#        $_SESSION['site']->echoHeader();
#    }
}

function replaceTags($text) {
    initNavajo();
    $regex = "#{(//|errors|label|classsuffix|showall|showmessage|showmethod|element|table|submit|service|setvalue|setusername)(.*?)}#s";
    $result = startNavajoInclude() . preg_replace_callback($regex, "navajoTagReplacer",$text) . endNavajoInclude();
    return $result;
}

function screenInclude($params) {
#     $page = $params["page"];
#    $_SESSION["currentPage"] = $page;
#    include JPATH_SITE . "/components/com_navajo/navajo.php";
}

function replaceTag($tag, $paramstring) {
    # do a little formatting on the paramstring to make the plugin a little more forgiving
    # append a space to possible open tags; trim and strip html tags; 
    # replace multiple spaces by one space; change a space between quotes to a tilde
    $paramstring = str_replace("<", " <", $paramstring);
    $paramstring = str_replace(", ", ",", $paramstring);
    $paramstring = strip_tags(trim($paramstring));
    $paramstring = preg_replace("{[ \t\n\r]+}", ' ', $paramstring );

    # harder find-and-replace: find spaces between quotes: change them to tildes 
    # the regexp can only change one space at a time: do it 10 times to be "sure" 
    $pattern = "/=\"([^\"]*)[ ]/";
    $replacement = "=\"$1~";
    if ( preg_match( $pattern, $paramstring ) > 0 ) {
        for ( $i = 0; $i < 10; $i++ ) {
            $paramstring = preg_replace($pattern, $replacement, $paramstring);  
        }
    }
    # echo "<!-- " . $paramstring . "-->"; 

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
    print "\n";
}

function navajoTagReplacer($matches) {
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
    $msg = "<div class='navajo'>\n" . 
           "<link href='/plugins/content/navajo/css/navajo.css' rel='stylesheet' type='text/css' />\n" .
           "<script src='/plugins/content/navajo/js/sortableTable.js' type='text/javascript'></script>\n" .
           "<form action='index.php' method='POST' enctype='multipart/form-data'>\n" .
           "<input type='hidden' name='option' value='com_navajo'/>\n" .
           "<input type='hidden' name='task' value='storeNavajo'/>\n"; 
    return $msg;
}

function endNavajoInclude() {
    return "</form></div>\n";
}

function callService($matches) {
    if (isset ($matches["name"])) {
        $navajo = $matches["name"];
    } else {
        $navajo = null;
    }

    $refresh = true;
    if (isset ($matches["refresh"])) {
        $refresh = $matches["refresh"];
    }

    if (isset ($matches["input"])) {
        $input = $matches["input"];
    } else {
        $input = null;
    }

    if ($refresh === 'false') {
        $preExistingNavajo = getNavajo($navajo);
        if ($preExistingNavajo != null) {
            return;
        }
    }

    if ($input == null) {
       echo "<!-- calling init : " . $navajo . " -->";
       $res = NavajoClient :: callInitService($navajo);
    } else {
       echo "<!-- calling process : " . $navajo . " with input " . $input . " -->";
       $res = NavajoClient :: callService($input, $navajo);
    }
    # $res->printXml();
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
        // echo "in usernameInclude - setting navajo :" . $navajo . " path: " . $path . " value: " . $value;
        NavajoPhpClient :: setValue($navajo, $path, $value);
    } else {
        echo "<p class='error'>No navajo found: " . $navajo . "</p>";
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
    
    if (isset($matches["columnWidths"])) {
        $columnWidthString = $matches["columnWidths"];
        $columnWidths = explode(",", $columnWidthString);
    } else {
        $columnWidths = "";
    }
    
    if (isset($matches["columnLabels"])) {
        $columnLabelString = $matches["columnLabels"];
        $columnLabels = explode(",", $columnLabelString);
    } else {
        $columnLabels = "";
    }
    if (isset($matches["columnDirections"])) {
        $columnDirectionsString = $matches["columnDirections"];
        $columnDirections = explode(",", $columnDirectionsString);
    } else {
        $columnDirections = "";
    }
    # "target" attribute is set to the alias of an article; get corresponding articleid from J! database
    if (isset($matches["target"])) {
           $alias = $matches["target"];
        $db =& JFactory::getDBO();
        $query = "SELECT id FROM #__content WHERE alias = '".$alias."'";
        $db->setQuery( $query );
        $matches["id"] = $db->loadResult();
    }
    if (isset ($_REQUEST["Itemid"])) {
        $matches["Itemid"] = $_REQUEST["Itemid"];
    }

    NavajoPhpClient :: showTable($navajo, $path, $columns, $matches, $columnWidths, $columnLabels, $columnDirections);
}

function submitInclude($matches) {

    $services = $matches["action"];
    $label = $matches["label"];
    $config = JFactory :: getConfig();
    
    # "target" attribute is set to the alias of an article; get corresponding articleid from J! database
    
    if (isset($matches["target"])) { 
        $alias = $matches["target"];       
        $pos   = strpos($alias, "http://");

        if ($pos === false) {
            $db =& JFactory::getDBO();
            $query = "SELECT id FROM #__content WHERE alias = '".$alias."'";
            $db->setQuery( $query );
            $id = $db->loadResult();          
            echo "<input type='hidden' name='" . $label . "' value='" . $id . "'/>\n";
            echo "<input type='hidden' name='" . $label . ":id' value='" . $id . "'/>\n";
        } else {
            echo "<input type='hidden' name='uri' value='" . $alias . "'/>\n";
        }
    
        if (isset ($_REQUEST["Itemid"])) {
            echo "<input type='hidden' name='" . $label . ":Itemid' value='" . $_REQUEST["Itemid"] . "'/>";
        }
    }
    if (isset($matches["article"])) { 
        echo "<input type='hidden' name='uri' value='" . $matches["article"] . "'/>\n";
    }
    echo "<input type='hidden' name='" . $label . ":serverCall' value='" . $services . "'/>\n";
    echo "<input type='submit' name='direction' value='" . $label . "'/>\n";

    # store latest Navajo in persistent session
    navajoSession :: storeNavajo();
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
            # change tildes back to spaces and remove quotes
            $row = str_replace("~", " ", $row);
            $row = str_replace("\"", "", $row);
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
