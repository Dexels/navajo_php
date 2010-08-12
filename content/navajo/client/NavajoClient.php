<?php
error_reporting(E_ALL);
ini_set("memory_limit","2G");

class navajoSession {
    function get($name, $default, $namespace) {
        $jSession = JFactory::getSession();
        $myVar = $jSession->get($name,$default,$namespace);
        $value = unserialize($myVar);
        return $value;
    }

    function set($name, $value, $namespace) {
        $jSession = JFactory::getSession();
        $myVar = serialize($value);
        $jSession->set($name,$myVar,$namespace);
    }
}

global $session;
$session = new navajoSession();

class NavajoClient {

    static function getServer() {
        global $session;
        $server = $session->get('navajoServer', '', 'navajo');
        return $server;
    }

    static function setServer($s) {
        global $session;
        $server = $session->set('navajoServer', $s, 'navajo');
    }

    static function getUser() {
        global $session;
        $user = $session->get('navajoUser', '', 'navajo');
        return $user;
    }

    static function setUser($s) {
        global $session;
        $session->set('navajoUser', $s, 'navajo');
    }

    static function getPassword() {
        global $session;
        $password = $session->get('navajoPassword', '', 'navajo');
        return $password;
    }

    static function setPassword($s) {
        global $session;
        $session->set('navajoPassword', $s, 'navajo');
    }

    static function processNavajo($serv, $navajo) {
        global $session;

        if (is_null($navajo)) {
            echo ('<h2>Er is een fout opgetreden</h2><p>U tracht een web service aan te roepen, waarvan de input niet bestaat.</p>');
            return;
        }

        $navajo->setHeaderAttributes(self :: getUser(), self :: getPassword(), $serv);
        # $navajo->printXML();
        $ch = curl_init();
        $contents = $navajo->saveXML();
         
        curl_setopt($ch, CURLOPT_URL, self :: getServer());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $contents);
        
        $body = curl_exec($ch);
        $err = curl_error($ch);
        $result = $body;
        
        if (!is_null($err) && ''!=$err) {
            echo ('<h2>Er is een fout opgetreden</h2><p>Connectiefout bij het opvragen van: ' . $serv.'</p><p>Foutbericht server:<br/>'. $err . '</p>');
            exit;
        }
        
        curl_close($ch);

        $res = new Navajo();

        $res->parseXml($result);
        $accessId = $res->getAccessId();
        $service = $res->getService();

        $session->set('navajoclass@' . $service, $res, 'navajo');
        self :: setCurrentNavajo($service);

        $error = $res->getMessage('error');
        
        if (!is_null($error)) {
            echo ('<h2>Er is een fout opgetreden</h2><p>Aanroep van ' . $service . ' mislukt.<p><p>Foutbericht server:<br/>');
            # $error->printXml();
            echo '</p>';
            exit;
        }
        $conditionErrors = $res->getMessage("ConditionErrors");
        if (!is_null($conditionErrors)) {
            echo "<p class='error'>Ongeldige invoer voor webservice: " . $service . "</p>";
            $errorChildren = $res->getMessage("ConditionErrors")->getSubMessages();
            for ($i = 0; $i < count($errorChildren); $i++) {
                # print_r($errorChildren);
                echo "Fout: " . $errorChildren[$i]->getProperty("FailedExpression")->getValue();
            }
        }
        return $res;
    }

    static function callInitService($service) {
        $nav = new Navajo();
        $nav->setHeaderAttributes(self :: getUser(), self :: getPassword(), $service);
        $res = self :: processNavajo($service, $nav);
        return $res;
    }

    static function doSimpleSend($s, $navajo) {
        return self :: processNavajo($s, $navajo);
    }

    static function callService($source, $service) {
        $n = getNavajo($source, 'callService');
        # $n->printXML();

        $r = self :: doSimpleSend($service, $n);
        return $r;
    }

    static function getCurrentNavajo() {
        global $session;
        if (is_null($session))
            $session = new navajoSession();
        $currentNavajo = $session->get('currentNavajo', '', 'navajo');
        return $currentNavajo;
    }

    static function setCurrentNavajo($service) {
        global $session;
        if (is_null($session))
            $session = new navajoSession();
        $session->set('currentNavajo', $service, 'navajo');
        return $service;
    }

    static function updateNavajoFromPost() {
        global $session;
        foreach (array_keys($_FILES) as $current_var) {

            $explode = explode("|", $current_var);
            $aaa = $explode[0];
            if ($aaa != "navajo") {
                continue;
            }

            $s = $explode[1];
            $propertypath = $explode[2];

            $n = getNavajo($s, 'updateFromPost');
            $property = $n->getAbsoluteProperty($propertypath);
            if ($property == null) {
                echo "<p class='error'>Error retrieving binary property: " . $current_var . "</p>\n";
                break;
            }
            //print_r($_FILES[$current_var]);
            //exit();
            //$property->setLength($_FILES[$current_var]["size"]);
            $filename = $_FILES[$current_var]["tmp_name"];
            $handle = fopen($filename, "r");
            if($handle) {
                $contents = fread($handle, filesize($filename));
                $property->setBinaryValue(base64_encode($contents));
                fclose($handle);
            }
        }

        foreach ($_REQUEST as $current_var => $value) {
            $explode = explode("|", $current_var);
            $aaa = $explode[0];
            if ($aaa != "navajo") {
                continue;
            }

            $s = $explode[1];
            $propertypath = $explode[2];

            $n = getNavajo($s, 'updateFromPost');
            $property = $n->getAbsoluteProperty($propertypath);
            if ($property == null) {
                echo "<p class='error'>Error retrieving property: " . $current_var . "</p>\n";
                break;
            }
            if ($property->getType() == "selection") {
                # set correct option selected
                $property->setSelectedByValue($value);
            } else
                if ($property->getType() == "boolean") {
                    # a checkbox is true if it's there, otherwise it's false
                    if (isset ($_POST[$propertypath])) {
                        $property->setValue("true");
                    } else {
                        $property->setValue("false");
                    }
                } else if ($property->getType() == "memo") {
                    $property->setValue(str_replace(array("\r\n", "\r", "\n"), "<br/>", $value));
                } else {
                    $property->setValue($value);
                }
        }
    }
}

function startupNavajo($server, $username, $password) {
    NavajoClient :: setUser($username);
    NavajoClient :: setPassword($password);
    NavajoClient :: setServer($server);
}

function getNavajo($s, $fromName = 'unknown') {
    global $session;
    echo "<!-- getNavajo(" . $s . ") called from " . $fromName . " -->";
    $navvv = $session->get('navajoclass@' . $s, null, 'navajo');

    if (get_class($navvv) != '__PHP_Incomplete_Class' && $navvv != null) {
        return $navvv;
    } else {
        return null;
    }
}

function getBinary($s, $path) {
    $n = getNavajo($s, 'getBinary');
    $prop = $n->getAbsoluteProperty($path);
    $str = $prop->getBinaryValue();
    return $str;
}
?>
