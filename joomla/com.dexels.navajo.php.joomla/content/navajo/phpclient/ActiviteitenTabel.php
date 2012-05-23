<?php
class ActiviteitenTabel extends NavajoLayout {
    var $i;
    var $myprops;
    var $columnWidths;

    function __construct($properties, $params, $columnWidths="", $columnLabels="", $columnDirections="") {
        $this->myprops          = $properties;
        $this->columnWidths     = $columnWidths;
        $this->columnLabels     = $columnLabels;        
        $this->columnDirections = $columnDirections;
    }

    protected function beforeRendering($nav, $params) {
        global $i, $key, $subkey, $link, $id, $itemId, $colGroup, $tableCSS, $totalWidth;
        $i = 1;
        $key    = (isset($params["key"]))?$params["key"]:null;
        $subkey = (isset($params["subkey"]))?$params["subkey"]:null;
        $link   = (isset($params["link"]))?$params["link"]:null;
        $id     = (isset($params["id"]))?$params["id"]:null;
        $itemId = (isset($params["Itemid"]))?$params["Itemid"]:null;
        $totalWidth = "100%";
        $colGroup = "";
        $tableCSS = "";
        if(isset($params["class"])) {
            $tableCSS = $params["class"] . " " . $tableCSS;
        }
    }

    protected function renderHeader($nav, $msg, $params) {
        global $key, $subkey, $colGroup, $tableCSS, $totalWidth;
        $j = 0;
    }
    protected function render($nav, $msg, $params) {
        global $i, $key, $subkey, $link, $id, $itemId;
        $j = 0;
        $blnOut = false;
        $keyValue = null;
        $subkeyValue = null;
        foreach ($this->myprops as $property) {
            $p = $msg->getProperty($property);
            switch ($property) {
                case "activiteit" :
                    echo "<h2 class='alt'>";
                    NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, ""); 
                    echo "</h2>";
                    break;
                case "datumvan" :
                    echo "\n<p><label>Wanneer?</label>";
                    NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, ""); 
                    break;
                case "tijdvan" :
                    if ($p->getValue() != '') { 
                        echo " vanaf ";
                        NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, ""); 
                    }
                    break;
                case "datumtm" :
                    if ($p->getValue() != '') { 
                        $hasTm = TRUE;
                        echo " t/m ";
                        NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, ""); 
                    }
                    break;
                case "tijdtm" :
                    if ($p->getValue() != '') { 
                        if ($hasTm) { echo " om "; } else { echo " t/m "; }
                        NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, ""); 
                    }
                    echo "\n</p>";
                    break;
                case "plaats" :
                    echo "\n<p><label>Waar?</label>";
                    NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, ""); 
                    break;
                    echo "\n</p>";
                    break;
                case "opmerkingen" :
                    echo "\t<p><br/>" . nl2br($p->getValue()) . "</p>";
                    echo "<hr class='space' />";
                    $hasTm = FALSE;
                    break;
                default :
                    break;
            }
            $j++;
        }
        $i++;
    }

    protected function renderFooter($nav, $msg, $params) {
    }

    protected function afterRendering($nav, $params) {
    }
}
?>
