<?php
class Message extends BaseNode {
    public $messageList = array ();
    public $propertyList = array ();

    public $name;
    public $type;
    public $index = 0;
    public function getAttribute($n) {
        if ($n == 'name') {
            return $this->name;
        }
        if ($n == 'type') {
            return $this->type;
        }
        if ($n == 'index') {
            return $this->index;
        }
    }

    public function getAllMessages() {
        return $this->messageList;
    }

    public function getAllProperties() {
        return $this->propertyList;
    }

    public function getChildren() {
        return array_merge($this->messageList, $this->propertyList);
    }

    public function getSubMessages() {
        return $this->messageList;
    }

    public function getName() {
        return $this->name;
    }
    public function getType() {
        return $this->type;
    }
    
    public function getTextNode() {
        return null;
    }
    
    public function getTagName() {
        return 'message';
    }

    public function getAttributeNames() {
        return array (
            'name',
            'type',
            'index'
        );
    }

    public function __construct($root) {
        parent :: __construct($root);
    }

    function getMessage($name) {
        $pp = strpos($name, '/');
        if ($pp === false) {
            return $this->messageList[$name];
        }
        $pathlist = explode('/', $name);
        return $this->getMessageFromPath($pathlist);
    }

    public function getPropertyNames() {
        $names = array ();
        $integer = 0;
        foreach ($this->propertyList as $p) {
            $names[$integer] = $p->getName();
            $integer++;
        }
        return $names;
    }

    function getArraySize() {
        return count($this->messageList);
    }

    function getArrayMessage($index) {
        $keys = array_keys($this->messageList);
        $value = array_values($this->messageList);
        return $this->messageList[$index];
    }
    function getMessageFromPath($pathlist) {
        if (count($pathlist) == 1) {
            if (isset ($this->messageList[$pathlist[0]])) {
                return $this->messageList[$pathlist[0]];
            } else {
                return null;
            }
        } else {
            $msg = $this-> $pathlist[0];
            array_shift($pathlist);
            $message = $this->messageList[$msg];
            return $message->getMessageFromPath($pathlist);
        }
    }

    function getProperty($name) {
        if ( isset($this->propertyList[$name]) ) {
            $p = $this->propertyList[$name];
        } else {
            $p = null;
        }
        return $p;
    }

    function getPath() {
        $ppath = "";
        if (is_null($this->parentNode)) {
            $ppath = '';
            return $this->name;
        } else {
            if (get_class($this->parentNode) == 'Navajo') {
                $ppath = '';
                return $this->name;
            } else {
                if ($this->type == 'array_element') {
                    $ppath = $this->parentNode->getPath();
                    return $ppath . '@' . $this->index;
                }
                $ppath = $this->parentNode->getPath();
            }
        }
        return $ppath . '/' . $this->name;
    }

    public function getIndex() {
        return $this->index;
    }

    public function parse($domNode) {
        $this->name = $domNode->getAttribute('name');
        $this->type = $domNode->getAttribute('type');
        $propCount = 0;
        if ($this->type == 'array') {
            $isArray = true;
            $count = 0;
        }
        if (is_null($this->getRootDoc())) {
            echo "<p class='error'>No root document found, while parsing message</p>";
        }
        $nodelist = $domNode->childNodes;
        for ($i = 0; $i < $nodelist->length; $i++) {
            $item = $nodelist->item($i);

            if (get_class($item) == 'DOMElement') {
                if ($item->tagName == 'message') {
                    $n = $item->getAttribute('name');
                    if ($item->getAttribute('type') == 'definition') {
                        continue;
                    }
                    $message = new Message($this->getRootDoc());
                    if ($item->getAttribute('type') == 'array_element') {
                        $ind = $message->getIndex();
                        $message->index = $count;
                        $message->parentNode = $this;
                        $message->parse($item);
                        $this->messageList[$count] = $message;
                        $count++;
                    } else {
                        $this->messageList[$n] = $message;
                        $message->parentNode = $this;
                        $message->parse($item);
                    }

                }
                if ($item->tagName == 'property') {
                    $n = $item->getAttribute('name');
                    $property = new Property($this->getRootDoc());
                    $property->parentNode = $this;
                    $property->parse($item);
                    $this->propertyList[$n] = $property;
                    $propCount++;
                }
            }
        }
    }
}
?>
