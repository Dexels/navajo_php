<?php
class Selection extends BaseNode {
	public $name;
	public $value;
	public $isSelected;
	
	public function getAttribute($n) {
		if($n=='name') {
			return $this->name;
		}
		if($n=='value') {
			return $this->value;
		}
		if($n=='selected') {
			if($this->isSelected == ""){
				return "0";
			} else 
				return $this->isSelected;
		}
	}
	public function getChildren() {
		return array();
	}
		
	public function __construct($root) {
		parent::__construct($root);
	}
	
	public function getTextNode() {
		return null;
	}
	
	public function getTagName() {
		return 'option';
	}
	
	public function getAttributeNames() {
		return array('name','value','selected');
	}
	
	public function parse($domNode) {
		if (get_class($domNode) == 'DOMElement') {
			$this->name = $domNode->getAttribute('name');
			$this->value = $domNode->getAttribute('value');
			$this->isSelected = $domNode->getAttribute('selected');
		}		
	}
}

?>
