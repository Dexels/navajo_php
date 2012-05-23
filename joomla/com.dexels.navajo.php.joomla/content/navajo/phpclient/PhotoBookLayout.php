<?php
class PhotoBookLayout extends NavajoLayout {
    var $i;
    var $myprops;
    var $columnCount;

    function __construct($properties) {
        $this->myprops = $properties;
    }

    protected function beforeRendering($params) {
        global $i, $columnCount;
        $i = 1;
        $j = 1;
        $columnCount = 5;
        echo "<table class='navajo' cellpadding='5' cellspacing='0' border='0'>\n";
    }

    protected function renderHeader($nav, $msg,$params) {    	
       
    }
    protected function render($nav, $msg,$params) {
        global $i, $j, $columnCount;        
        if (($i - 1) % $columnCount == 0) {
        	$sfx = (($j % 2) == 0) ? "odd" : "even";
        	echo "<tr class='row_" . $sfx . "'>\n";
        	$j++;        	
        }        

        foreach ($this->myprops as $property) {
            
            switch ($property) {
                case "Photo" :
                	echo "<td align='center' style='vertical-align:bottom;width:160px;height:160px'>";
                    NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, "_checkbox");
                    break;
                default :
                    NavajoPhpClient :: showProperty($nav, $property, $msg, $params, true);
                    echo "</td>";
            }
            
        }        
        if ($i % $columnCount == 0) {
        		echo "</tr>\n";
        }
        $i++;
    }

    protected function afterRendering($params) {
    	global $i, $columnCount;       	
    	$missingCellCount = $columnCount - (($i - 1) % $columnCount);           	
    	# add missing cells to have nicely squared out rows
    	while($missingCellCount > 0) {
    	    echo "<td>&nbsp;</td>";
    	    $missingCellCount--;
    	    if ($missingCellCount == 0) {
    	        echo "</tr>\n";
    	    }   	    
    	}
        echo "</table>\n";
    }

}
?>