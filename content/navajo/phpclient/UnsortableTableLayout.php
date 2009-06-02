<?php
class UnsortableTableLayout extends NavajoLayout {
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
        global $i, $key, $link, $id, $itemId, $colGroup, $tableCSS, $totalWidth;
        $i = 0;
        $key    = (isset($params["key"]))?$params["key"]:null;
        $link   = (isset($params["link"]))?$params["link"]:null;
        $id     = (isset($params["id"]))?$params["id"]:null;
        $itemId = (isset($params["Itemid"]))?$params["Itemid"]:null;
        
        $totalWidth = "0";
        
        if(is_array($this->columnWidths)) {
            $colGroup = "<colgroup>\n";
            foreach ($this->columnWidths as $currentWidth) {
                if ($currentWidth != 0) {
                    $totalWidth += $currentWidth + 3;
                    $colGroup .= "<col width='". $currentWidth . "px'/>\n";
                }
            }
            $colGroup .= "</colgroup>\n";
        } else {
            $totalWidth = "100%";
            $colGroup = "";
        }
        $tableCSS = "";
        if(isset($params["class"])) {
            $tableCSS = $params["class"] . " " . $tableCSS;
        }
    }

    protected function renderHeader($nav, $msg, $params) {
        global $key, $colGroup, $tableCSS, $totalWidth;
        $j = 0;
        
        echo "\n<table id='" . str_replace("/", "_", $nav) . "' class='" . $tableCSS . "' cellpadding='3' cellspacing='0' border='0' width='" . $totalWidth . "'>\n";
        echo $colGroup;

        if(is_array($this->columnLabels)) {
            echo "<thead>\n";
            echo "<tr>\n";
            
            foreach ($this->myprops as $property) {
                $params['label'] = (isset($this->columnLabels[$j]))?$this->columnLabels[$j]:$msg->getProperty($property)->getDescription();
                $type = $msg->getProperty($property)->getType();
                ($type == 'integer')?$type = 'number':$type = $type;
                switch ($property) {
                    case $key :
                        break;                
                    case "Update" :
                        echo "\t<th/>";
                        break;
                    case "Delete" :
                        echo "\t<th/>";
                        break;
                    default :
                        echo "\t<th >" . $params['label'] . "</th>\n";
                }
                $j++;
            }
            echo "</tr>\n";
            echo "</thead>\n";
        }
        echo "<tbody>\n";
    }
    protected function render($nav, $msg, $params) {
        global $i, $key, $link, $id, $itemId;
        $j = 0;
        $blnOut = false;
        $keyValue = null;
        $sfx = ($i % 2) ? "" : "altRow";
        echo "<tr id='" . $i . "' class='" . $sfx . "'>\n";
        foreach ($this->myprops as $property) {
            switch ($property) {
                case $key :
                    $p = $msg->getProperty($property);
                    $keyValue = $p->getValue();
                    break;
                case "Update" :
                    echo "\t<td>";
                    NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, " update hidden");
                    echo "</td>\n";
                    break;
                case "Delete" :
                    echo "\t<td>";
                    NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, " delete hidden");
                    echo "</td>\n";
                    break;
                case "RouteTo" :
                    $p = $msg->getProperty($property);
                    $keyValue = $p->getValue();
                    echo "\t<td>";
                    echo "<a target='_new' href='" . $keyValue . "'>Toon route</a>";
                    echo "</td>\n";
                    break;
                case $link :
                       echo "\t<td>";
                       if ( $keyValue != null ) {
                      echo "<a href='index.php?option=com_content&view=article&id=" . $id . "&Itemid=" . $itemId . "&" . $key . "=" . $keyValue . "'>";
                      NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, "");
                      echo "</a>"; 
                    } else {
                      NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, "");
                    }
                    echo "</td>\n";
                    break;
                default :
                   
                    echo "\t<td>";
                     if(is_array($this->columnDirections)){
                       $blnOut = ($this->columnDirections[$j] == "in")?false:true;
                    }
                    
                    NavajoPhpClient :: showProperty($nav, $property, $msg, $params, $blnOut, false, "");
                    echo "</td>\n";
            }
            
            $j++;
        }
        $i++;
        echo "</tr>\n";
    }

    protected function renderFooter($nav, $msg, $params) {
        echo "</tbody>\n";
        echo "</table>\n";
    }
    protected function afterRendering($nav, $params) {
    
    }
}
?>
