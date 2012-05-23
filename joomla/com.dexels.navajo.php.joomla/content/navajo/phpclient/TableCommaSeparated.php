<?php
class TableCommaSeparated extends NavajoLayout {
    var $i;
    var $myprops;

    function __construct($properties) {
        $this->myprops = $properties;
    }

    protected function beforeRendering($params) {
        global $i;
        $i = 1;
    }

    protected function renderHeader($nav, $msg,$params) {    	
    }
    protected function render($nav, $msg,$params) {
	$n = getNavajo($nav);
        global $i;	
        foreach ($this->myprops as $property) {
	    $p = $msg->getProperty($property);
	    if($i != 1) {
		echo ", ";
	    }
	    echo $p->getValue();
        }
        $i++;
    }

    protected function afterRendering($params) {
    }
}
?>
