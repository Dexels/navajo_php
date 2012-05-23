<?php
class Method extends BaseNode {
    public $name;

    public function getTagName() {
        return 'method';
    }

    public function getChildren() {
        return null;
    }
    
    public function getTextNode() {
        return null;
    }
            
    public function getAttributeNames() {
        return array('name');
    }
    
    public function getAttribute($n) {
        if($n=='name') {
            return $this->name;
        }
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function parse($domNode) {
        $this->name = $domNode->getAttribute('name');
    }
    
    public function __construct($root) {
        parent::__construct($root);
    }
}
?>
