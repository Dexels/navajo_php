<?php
require_once ('BaseNode.php');

require_once ('Header.php');
require_once ('Message.php');
require_once ('Methods.php');
require_once ('Method.php');
require_once ('Property.php');
require_once ('Selection.php');
require_once ('Transaction.php');
require_once "Libraries.php";

function dumpElement($element) {
    $doc = new DOMDocument('1.0', 'iso-8859-1');
    $tempnode = $doc->createElement("temp");
    $doc->appendChild($tempnode);
    $node = $doc->importNode($element, true);

    $tempnode->appendChild($node);
    # echo $node->tagName;
    echo '<br/>starting dump....<br/>';
    echo htmlentities($doc->saveXml());

    # echo $doc->saveXml();
    echo '<br/>ending dump....<br/>';
}

//$a = new Navajo();

class Navajo extends BaseNode {

    public $messages = array ();
    public $header;
    public $methods;

    public function __construct() {
        parent :: __construct($this);
        $this->header = new Header($this);
    }
    
    public function getTagName() {
        return 'tml';
    }

	public function getTextNode() {
		return null;
	}
	
    function getMessage($name) {
        if ($name[0] == '/') {
            $name = substr($name, 1, strlen($name));
        }
        $pp = strpos($name, '/');
        if (!isset ($this->messages[$name])) {
            return null;
        }
        if ($pp === false) {
            return $this->messages[$name];
        }
        $pathlist = explode('/', $name);
        return $this->getMessageFromPath($pathlist);
    }

    function getAbsoluteProperty($name) {
        # print_r($this);
        if ($name[0] == '/') {
            $name = substr($name, 1, strlen($name));
        }
        $pathlist = explode('/', $name);

        if (count($pathlist) == 1) {
            trace('WTF?!');
        } else {
            $propname = array_pop($pathlist);
            $msg = $this->getMessageFromPath($pathlist);
            if ($msg == null) {
                return null;
            }
            return $msg->getProperty($propname);
        }
    }

    function getMessageFromPath($pathlist) {

        if (count($pathlist) == 1) {
            if (isset ($this->messages[$pathlist[0]])) {
                return $this->messages[$pathlist[0]];
            } else
                if (strpos($pathlist[0], '@')) {
                    $m = explode('@', $pathlist[0]);
                    $msgIndx = $m[1];
                    $msgName = $m[0];
					$children = $this->messages[$msgName]->getSubMessages();
					return $children[$msgIndx];                    
                } else {
                    return null;
                }
        } else {
            $msg = $this->messages[0];
            array_shift($pathlist);
            return $msg->getMessageFromPath($pathlist);
        }
    }

    public function getAttributeNames() {
        return array ();
    }

    public function getAttribute($n) {

    }

    function getAccessId() {
        return $this->header->getAccessId();
    }

    public function getService() {
        return $this->header->getCurrentService();
    }

    public function getChildren() {
        return array_merge(array (
            $this->header,
            $this->methods
        ), $this->messages);
    }

    function setHeaderAttributes($user, $password, $service) {
        if ($this->header == null) {
            $this->header = new Header($this);
        }
        $this->header->setHeaderAttributes($user, $password, $service);
    }
    
    public function parseXml($xmlText) {
        if ($xmlText == '') {
            echo('Warning: Empty xml supplied!');
		//	trace('Warning: Empty xml supplied!');
            return;
        }
        $doc = new DOMDocument('1.0', 'iso-8859-1');
        $doc->loadXML($xmlText);
        $this->parse($doc->documentElement);
    }

    public function parse($domNode) {

        $nodelist = $domNode->childNodes;
        for ($i = 0; $i < $nodelist->length; $i++) {
            $item = $nodelist->item($i);
            if (get_class($item) == 'DOMElement') {
                if ($item->tagName == 'header') {
                    $this->header = new Header($this);
                    $this->header->parse($item);
                }
                if ($item->tagName == 'methods') {
                    $methods = new Methods($this->getRootDoc());
                    $methods->parse($item);
                    $this->methods = $methods;
                }
                if ($item->tagName == 'message') {
                    $m = new Message($this);
                    $m->parentNode = $this;
                    $m->parse($item);
                    $msgname = $item->getAttribute('name');
                    $this->messages[$msgname] = $m;
                }
            }
        }
    }

    public function getAllMethods() {
    	if(!isset($this->methods) || !is_object($this->methods)) {
    		return;
    	}
    	
        return $this->methods->getAllMethods();
    }

    public function getMessages() {
        return $this->messages;
    }
}

class NavajoDoc {

    public function parse($domNode) {
    }
    
    public $tmlNode;
    public $headerNode;
    public $transactionNode;
    public $methodsNode;
    public $navajoDoc;
    
    function isValid() {
        if (is_null($this->tmlNode)) {
            return false;
        }
        if (is_null($this->headerNode)) {
            return false;
        }
        if (is_null($this->transactionNode)) {
            return false;
        }
        if (is_null($this->navajoDoc)) {
            return false;
        }
        return true;
    }

    function getSelectedMessage($node, $name) {
        $nodelist = $node->childNodes;
        for ($i = 0; $i < $nodelist->length; $i++) {
            if (get_class($nodelist->item($i)) == 'DOMElement') {
                $currentPrp = $this->getProperty("Update", $nodelist->item($i));
                if ($currentPrp->getAttribute("value") == "true") {
                    return $nodelist->item($i);
                }
            }
        }
        return null;
    }

    function dumpElement($element) {
        $doc = new DOMDocument('1.0', 'iso-8859-1');
        $tempnode = $doc->createElement("temp");
        $doc->appendChild($tempnode);
        $node = $doc->importNode($element, true);
        $tempnode->appendChild($node); # echo $node->tagName;
        echo '<br/>starting dump....<br/>';
        echo htmlentities($doc->saveXml()); # echo $doc->saveXml();
        echo '<br/>ending dump....<br/>';
    }

    function __construct() { # echo('constructing navajo');
        $this->navajoDoc = new DOMDocument('1.0', 'iso-8859-1');
        $this->tmlNode = $this->navajoDoc->createElement("tml");
        $this->navajoDoc->appendChild($this->tmlNode);
        $this->headerNode = $this->navajoDoc->createElement("header");
        $this->tmlNode->appendChild($this->headerNode);
        $this->transactionNode = $this->navajoDoc->createElement("transaction");
        $this->transactionNode->setAttribute("expiration_interval", "-1");
        $this->headerNode->appendChild($this->transactionNode); # echo htmlentities($this->navajoDoc->saveXml());
    }

    function setHeaderAttributes($user, $password, $service) {
        $this->transactionNode->setAttribute("rpc_usr", $user);
        $this->transactionNode->setAttribute("rpc_pwd", $password);
        $this->transactionNode->setAttribute("rpc_name", $service);
    }

    function parseXml($xmlText) { # echo('constructing navajo with content');
        if ($xmlText == '') {
            trace('Warning: Empty xml supplied!');
        }
        $this->navajoDoc = new DOMDocument('1.0', 'iso-8859-1');
        $this->navajoDoc->loadXML($xmlText);
        $nodelist = $this->navajoDoc->getElementsByTagName('tml');
        $this->tmlNode = $nodelist->item(0); #	for ($i = 0; $i < $nodelist->length; $i++) {
        #	   echo $nodelist->item($i)->nodeValue . "\n";
        #	}
        // TODO Fix this, not efficient
        $this->headerNode = $this->tmlNode->getElementsByTagName('header')->item(0);
        $this->transactionNode = $this->headerNode->getElementsByTagName('transaction')->item(0);
        $this->methodsNode = $this->tmlNode->getElementsByTagName('methods')->item(0); //   $this->dumpElement($this->methodsNode);
    }

    function getAccessId() {
        return $this->headerNode->getAttribute('accessId');
    }

    function getService() {
        return $this->transactionNode->getAttribute('rpc_name');
    }

    function printXml() {
        echo "<pre>";
        echo htmlentities($this->navajoDoc->saveXml());
        echo "</pre>";
    }

    function saveXml() {
        return $this->navajoDoc->saveXml();
    }

    function getAbsoluteProperty($name) { # echo '<h3>'.$name.'</h3>';
        if ($name[0] == '/') {
            $name = substr($name, 1, strlen($name));
        }
        $pathlist = explode('/', $name);
        $propname = array_pop($pathlist);
        $messagename = implode("/", $pathlist);
        $msg = $this->getMessage($messagename);
        return $this->getPropertyFromMessage($msg, $propname);
    }

    function getProperty($name, $message) {
        $pathlist = explode('/', $name);
        $propname = array_pop($pathlist);
        if (count($pathlist) == 0) {

            return $this->getPropertyFromMessage($message, $propname);
        } else {
            $messagename = implode("/", $pathlist);
            $message = $this->getMessageFromNode($messagename, $message);
            return $this->getPropertyFromMessage($message, $propname);
        }
    } //
    //    function getPropertyId($p) {
    //        return 'navajo|' . $this->getService() . '|' . $this->getPath($p);
    //
    //    }
    function getAbsolutePath($node) {
        if (is_null($node)) {
            trace('Null node supplied!');
            return;
        }
        if ($node->tagName == 'tml') {
            return '';
        }
        $parent = $node->parentNode;
        $type = $node->getAttribute('type');
        if ($type == 'array_element') {
            $index = $node->getAttribute('index');
            $grandparent = $parent->parentNode;
            return $this->getAbsolutePath($grandparent) . '/' . $node->getAttribute('name') . '@' . $index;
        } else {
            return $this->getAbsolutePath($parent) . '/' . $node->getAttribute('name');
        }
    }

    function getPath($node) {
        return $this->getAbsolutePath($node);
    }

    function getMessage($name) { # echo $name;
        if ($name[0] == '/') {
            $name = substr($name, 1, strlen($name));
        } # echo $name;
        $pathlist = explode('/', $name);
        $m = $this->getMessageFromNode($pathlist, $this->tmlNode);
        if (is_null($m)) { # trace ('Error: Message not found: '.$name.'<br/>');
            # die();
        }
        return $m;
    }

    function getPropertyFromMessage($msg, $propname) {

        $nodelist = $msg->childNodes;
        for ($i = 0; $i < $nodelist->length; $i++) {
            if (get_class($nodelist->item($i)) == 'DOMElement') {
                $currentname = $nodelist->item($i)->getAttribute('name');
                if ('property' == $nodelist->item($i)->tagName and $currentname == $propname) {
                    return $nodelist->item($i);
                }
            }
        }
        echo ('Property not found: ' . $propname);
    }

    function isArrayElement($message) {
        $type = $message->getAttribute('type');
        if (is_null($type)) {
            return false;
        }
        if ($type == 'array_element') {
            return true;
        }
        return false;
    }

    function isArray($message) {
        $type = $message->getAttribute('type');
        if (is_null($type)) {
            return false;
        }
        if ($type == 'array') {
            return true;
        }
        return false;
    }

    function getArrayMessageElement($message, $index) {
        if (is_null($message)) {
            echo 'can not retrieve element from null message!';
            die();
        }
        if (is_null($message)) {
            echo 'can not retrieve array element with null index!';
            die();
        }

        $nodelist = $message->childNodes; # trace("Message: ".$this->getPath($message).' index: '.$index);
        for ($i = 0; $i < $nodelist->length; $i++) {
            if (get_class($nodelist->item($i)) == 'DOMElement') {
                $currentindex = $nodelist->item($i)->getAttribute('index');
                $tagname = $nodelist->item($i)->tagName;
                if ($currentindex == $index and $tagname == 'message') { //	$this->dumpElement($nodelist->item($i));
                    return $nodelist->item($i);
                }
            }
        }

        if (is_null($message)) {
            echo 'element not found: index: ' . $index;
            echo '<br/>messagepat: ' . $this->getPath($message);
            die();
        }
    }

    function debugMessage($msg) {
        echo '>>debugging message<<<br/>';
        echo $this->getPath();
        echo '>>end debugging message<<<br/>';
    }

    function has_index($name) {
        $pos = strpos($name, '@');
        if ($pos === false) {
            return false;
        }
        return true;
    }

    function setSelectedByValue($property, $value) {
        $nodelist = $property->childNodes;
        for ($i = 0; $i < $nodelist->length; $i++) {
            if (get_class($nodelist->item($i)) == 'DOMElement') {
                $currentvalue = $nodelist->item($i)->getAttribute('value');
                if ($currentvalue == $value) {
                    $nodelist->item($i)->setAttribute('selected', '1');
                } else {
                    $nodelist->item($i)->setAttribute('selected', '0');
                }
            }
        }
    }

    function getMessageFromNode($pathlist, $node) {
        $nodelist = $node->childNodes;
        if (count($pathlist) < 2) {
            $name = $pathlist[0];
            for ($i = 0; $i < $nodelist->length; $i++) {
                if (get_class($nodelist->item($i)) == 'DOMElement') {
                    $currentname = $nodelist->item($i)->getAttribute('name');
                    $tagname = $nodelist->item($i)->tagName;
                    if ($tagname == 'message' && $this->isArray($nodelist->item($i)) && $this->has_index($name)) {
                        $nn = explode('@', $name);
                        $actualname = $nn[0];
                        if ($actualname == $currentname and 'message' == $tagname) {
                            $index = $nn[1];
                            $arraymessage = $nodelist->item($i);
                            $element = $this->getArrayMessageElement($arraymessage, $index);
                            if (is_null($element)) {
                            }
                            return $element;
                        }
                    } else {
                        if ($currentname == $name and 'message' == $tagname) {
                            $element = $nodelist->item($i);
                            if (is_null($element)) {
                            }
                            return $nodelist->item($i);
                        }
                    }
                }
            }

        } else {
            $name = $pathlist[0];
            for ($i = 0; $i < $nodelist->length; $i++) {
                if (get_class($nodelist->item($i)) == 'DOMElement') {
                    $currentname = $nodelist->item($i)->getAttribute('name');
                    $tagname = $nodelist->item($i)->tagName;
                    if ('message' == $tagname) {
                        if ($this->isArrayElement($nodelist->item($i))) {
                        } else {
                            if ($currentname == $name) {
                                $element = array_shift($pathlist);
                                $result = $this->getMessageFromNode($pathlist, $nodelist->item($i));
                                if (is_null($result)) {
                                }
                                return $result;
                            }
                        }
                    }
                }
                $element = $nodelist->item($i);
                if (is_null($element)) {
                }
            }
        }
    }
}
?>
