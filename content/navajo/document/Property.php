<?php
require_once ('BaseNode.php');
require_once ('NavajoDoc.php');
require_once ('Selection.php');

class Property extends BaseNode {
    public $type;
    public $value;
    public $cardinality;
    public $description;
    public $direction;
    public $name;
    public $length;
    public $subtype;
    public $textNode;

    public $selectionList = array ();

    public function getAttribute($n) {
        if ($n == 'name') {
            return $this->name;
        }
        if ($n == 'type') {
            return $this->type;
        }
        if ($n == 'cardinality') {
            return $this->cardinality;
        }
        if ($n == 'direction') {
            return $this->direction;
        }
        if ($n == 'value') {
            return $this->value;
        }
        if ($n == 'description') {
            return $this->description;
        }
        if ($n == 'length') {
            return $this->length;
        }
        if ($n == 'subtype') {
            return $this->subtype;
        }
    }

    function getAllSelections() {
        return $this->selectionList;
    }

    public function getPath() {
        return $this->parentNode->getPath() . '/' . $this->name;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function getSubType() {
        return $this->subtype;
    }
    
    public function getValue() {
        return $this->value;
    }
    
    public function getPropertyId() {
        if (is_null($this->getRootDoc())) {
            echo "<p class='error'>No root document found, parsing propertyid</p>";
        }
        return 'navajo|' . $this->getRootDoc()->getService() . '|' . $this->getPath();
    }

    public function getChildren() {
        return $this->selectionList;
    }

    public function setValue($v) {
        if ($this->type == 'selection') {
        	$this->setSelectedByValue($v);
        } else {
        	$this->value = stripslashes($v);
        } 
    }

    public function getTagName() {
        return 'property';
    }

    public function getAttributeNames() {
        return array (
            'name',
            'type',
            'value',
            'length',
            'cardinality',
            'description',
            'direction',
            'subtype'
        );
    }
    
    public function getTextNode() {
        return $this->textNode;
    }

    public function parse($domNode) {
        $this->type = $domNode->getAttribute('type');      
        $this->name = $domNode->getAttribute('name');
        $this->cardinality = $domNode->getAttribute('cardinality');
        if (!is_null($this->cardinality)) {
            $this->value = $domNode->getAttribute('value');
        }
        $this->direction = $domNode->getAttribute('direction');
        $this->description = $domNode->getAttribute('description');
        $this->length = $domNode->getAttribute('length');
        $this->subtype = $domNode->getAttribute('subtype');
        $nodelist = $domNode->childNodes;
        for ($i = 0; $i < $nodelist->length; $i++) {
            $item = $nodelist->item($i);
            if (get_class($item) == 'DOMElement') {
                $selectionName = $item->getAttribute('name');
                $s = new Selection($this->getRootDoc());
                $s->parse($item);
                $this->selectionList[$selectionName] = $s;
            } else {
                $this->textNode = $item->data;
            }
        }
    }

    function getBinaryValue() {
        return $this->textNode;
    }
        
    function setBinaryValue($v) {
        $this->textNode = $v;        
    }
    
    function setSelectedByValue($value) {

        $selectionList = $this->getAllSelections();

        foreach ($selectionList as $currentSelection) {
            $currentvalue = $currentSelection->value;
            if ($currentvalue == $value) {
                $currentSelection->isSelected = "1";
            } else {
                $currentSelection->isSelected = "0";
            }
        }
    }

}
?>
