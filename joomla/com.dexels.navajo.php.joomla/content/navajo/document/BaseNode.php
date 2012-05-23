<?php
abstract class BaseNode {
    public abstract function parse($domNode);
    public abstract function getTagName();
    public abstract function getAttributeNames();
    public abstract function getAttribute($name);
    public abstract function getChildren();
    public abstract function getTextNode();
        
    private $rootDoc;
    public $parentNode;
    
    public function __construct($root) {
        if(is_null($root)){
            echo "<p class='error'>Error constructing basenode:".get_class($this)."</p>\n";
        }
        $this->rootDoc = $root;
    }    

    public function getRootDoc() {
        return $this->rootDoc;
    }
    
    function printXml() {
        echo "<pre>";
        echo htmlentities($this->saveXML());
        echo "</pre>";
    }
    
    public function saveXML() {
        $c = $this->getChildren();
        $t = $this->getTextNode();
        
        ob_start();
        echo "<".$this->getTagName();
        $attr = $this->getAttributeNames();
        foreach ($attr as $a) {
            $val = $this->getAttribute($a);    
            echo " $a=\"$val\"";
        }
        if (is_null($t) && ( is_null($c) || count($c)<1 ) ) {
             echo '/>';
        } else {
            echo ">\n";
         
            print("\n");
            if(!is_null($t) && ( is_null($c) || count($c)<1 ) ) {
                echo $t;
            } else {
                foreach ($c as $a) {
                    if($a ==null) {
                    } else {
                        echo $a->saveXml();
                    }
                }
            }
            echo "</".$this->getTagName().">\n";            
        }
        $ress = ob_get_contents();
        ob_end_clean();        
        return $ress;
    }

    public function dump($indent, $stream) {
    }
}

?>
