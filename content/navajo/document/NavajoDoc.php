<?php
require_once ('BaseNode.php');
require_once ('Header.php');
require_once ('Message.php');
require_once ('Methods.php');
require_once ('Method.php');
require_once ('Property.php');
require_once ('Selection.php');
require_once ('Transaction.php');
require_once ('Libraries.php');

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
            trace('error..');
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
        error_reporting(E_ERROR);
        if (trim($xmlText) == '') {
            echo('Warning: Empty xml supplied!');
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
                if (false AND $item->tagName == 'methods') {
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
?>
