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
        global $i, $key, $subkey, $altkey, $link, $id, $itemId, $colGroup, $tableCSS, $totalWidth;
        $i = 0;
        $key    = (isset($params["key"]))?$params["key"]:null;
        $subkey = (isset($params["subkey"]))?$params["subkey"]:null;
        $altkey = (isset($params["altkey"]))?$params["altkey"]:null;
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
        global $key, $subkey, $altkey, $colGroup, $tableCSS, $totalWidth;
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
                    case $altkey :
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
        global $i, $key, $subkey, $altkey, $link, $id, $itemId;
        $j = 0;
        $blnOut = false;
        $keyValue = null;
        $subkeyValue = null;
        $altkeyValue = null;

        $sfx = ($i % 2) ? "" : "altRow";

        # check if the 'eigenteam' property exists, and its value is true if so, make the table row bold
        $prp  = $msg->getProperty('eigenteam');
        if (is_null($prp)) {
           $prp  = $msg->getProperty('ClubData');
        }
        if (is_null($prp)) {
            echo "<tr id='" . $i . "' class='" . $sfx . "'>\n";
        } else {
            if ( $prp->getValue() == 'true') {
                echo "<tr id='" . $i . "' class='" . $sfx . " bold'>\n";
            } else {
                echo "<tr id='" . $i . "' class='" . $sfx . "'>\n";
            }
        }

        # echo "<tr id='" . $i . "' class='" . $sfx . "'>\n";
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
                case $altkey :
                    $p = $msg->getProperty($property);
                    $altkeyValue = $p->getValue();
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
                    if (isset($params['article'])) {
                        $articleLink = "/index.php/" . $params['article'] . "?";
                    } else {
                        $articleLink = "/index.php?option=com_content&view=article&id=" . $id . "&Itemid=" . $itemId . "&";
                    }
                    if ($keyValue != null) {
                        echo "<a href='" . $articleLink . $key . "=" . $keyValue;
                    }
                    if ($subkeyValue != null) { 
                        echo "&" . $subkey . "=" . $subkeyValue;
                    }
                    if ($altkeyValue != null) { 
                        echo "&" . $altkey . "=" . $altkeyValue;
                    }
                    if ($keyValue != null) {
                        echo "'>";
                    }
                    NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, "");
                    if ($keyValue != null) {
                        echo "</a>";
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
