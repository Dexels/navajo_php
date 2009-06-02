<?php
class AdvancedTableLayout extends NavajoLayout {
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
        global $key, $subkey, $colGroup, $tableCSS, $totalWidth;
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
                    case $subkey :
                        break;                
                    case "Update" :
                        echo "\t<th/>";
                        break;
                    case "Delete" :
                        echo "\t<th/>";
                        break;
                    default :
                        echo "\t<th axis='" . $type . "'>" . $params['label'] . "</th>\n";
                }
                $j++;
            }
            echo "</tr>\n";
            echo "</thead>\n";
        }
        echo "<tbody>\n";
    }
    protected function render($nav, $msg, $params) {
        global $i, $key, $subkey, $link, $id, $itemId;
        $j = 0;
        $blnOut = false;
        $keyValue = null;
        $subkeyValue = null;
        $sfx = ($i % 2) ? "" : "altRow";
        echo "<tr id='" . $i . "' class='" . $sfx . "'>\n";
        foreach ($this->myprops as $property) {
            switch ($property) {
                case $key :
                    $p = $msg->getProperty($property);
                    $keyValue = $p->getValue();
                    break;
                case $subkey :
                    $p = $msg->getProperty($property);
                    $subkeyValue = $p->getValue();
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
                case "Email" :
                    $p = $msg->getProperty($property);
                    echo "\t<td>";
                    echo "<a href=\"mailto:" . $p->getValue() . "\">" . $p->getValue() . "</a>";
                    echo "</td>\n";
                    break;
                case $link :
                       echo "\t<td>";
                    if ( $keyValue != null && $subkeyValue != null ) {
                      echo "<a href='index.php?option=com_content&view=article&id=" . $id . "&Itemid=" . $itemId . "&" . $key . "=" . $keyValue . "&" . $subkey . "=" . $subkeyValue . "'>";
                      NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, "");
                      echo "</a>"; 
                    } else if (  $keyValue != null ) {
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
        if (isset($this->columnLabels[0]) && sizeof($this->columnLabels) != 0 ) {
            if (isset($params['sortColumn'])) { $sortOn = ", sortOn: '" . ( $params['sortColumn'] - 1 ) . "'"; } else { $sortOn = ", sortOn: '0'"; }
            if (isset($params['sortOrder'])) { $sortOrder = ", sortBy: '" . strtoupper($params['sortOrder']) . "'"; } else { $sortOrder = ", sortBy: 'ASC'"; }
            if (!isset($params['sortColumn']) OR $params['sortColumn'] != '-1') {
                echo "<script type='text/javascript'>\n";
                echo "\tvar myTable = {};\n";
                echo "\twindow.addEvent('domready', function() {\n";
                echo "\t\tmyTable = new sortableTable('" . str_replace("/", "_", $nav) . "', {overCls: 'over'" . $sortOn . $sortOrder . ", onClick: function(){}});";
                echo "\t});\n";
                echo "</script>\n";
            }
        }
    }

    protected function afterRendering($nav, $params) {
    
    }
}
?>
