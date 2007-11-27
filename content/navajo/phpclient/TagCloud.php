<?php
class TagCloud extends NavajoLayout {
    var $i;
    var $myprops;

    function __construct($properties) {
        $this->myprops = $properties;
    }

    protected function beforeRendering($params) {
       echo "<div class='tagcloud'>\n";
    }

    protected function renderHeader($nav, $msg,$params) {    	
    }
    protected function render($nav, $msg,$params) {
	$n = getNavajo($nav);
        foreach ($this->myprops as $property) {
	 	   $p = $msg->getProperty($property);	 	   
            if( $p->getName() == 'TagName' ) {
                $tagName = $p->getValue();
            } elseif (  $p->getName() == 'TagType' ) {
                $tagType = $p->getValue();                    
            } elseif (  $p->getName() == 'CSSClass' ) {
                $cssClass = $p->getValue();
            }
        }        
        if ($tagType == 'matching') {
       	  echo "\t<a href='#' onclick='javascript:searchMovies(\"".$tagName."\")' class='tag_".$cssClass."'>".$tagName."</a>\n";
        } else {
            echo "<a href='#' class='tag_".$cssClass."'>".$tagName."</a>\n";
        }
    }

    protected function afterRendering($params) {
        echo "</div>\n";
    }
}
?>
