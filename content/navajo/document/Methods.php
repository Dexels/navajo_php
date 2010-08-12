<?php
class Methods extends BaseNode {
    public $methodList;

    public function getTagName() {
        return 'methods';
    }

    public function getChildren() {
        return $this->methodList;
    }
    
    public function getTextNode() {
        return null;
    }
    
    public function getAllMethods() {
        return $this->methodList;
    }
    
    public function getAttributeNames() {
        return array();
    }
    
    public function getAttribute($n) {
    }
    
    public function parse($domNode) {
        $nodelist = $domNode->childNodes;
        for ($i = 0; $i < $nodelist->length; $i++) {
            $item = $nodelist->item($i);
            if (get_class($item) == 'DOMElement') {
                $methodName = $item->getAttribute('name');
                $s = new Method($this->getRootDoc());
                $s->parse($item);
                $this->methodList[$methodName] = $s;
            }
        }    
    }
    
    public function __construct($root) {
        parent::__construct($root);
    }
}
?>
