<?php
class AdvancedTableLayout extends NavajoLayout {

    var $i;
    var $myprops;

    function __construct($properties) {
        $this->myprops = $properties;
    }

    protected function beforeRendering() {
        global $i;
        $i = 1;
        echo "<table class='navajo' cellpadding='5' cellspacing='0' border='0'>\n";
    }

    protected function renderHeader($nav, $msg) {    	
        echo "<tr><th />\n";
        foreach ($this->myprops as $property) {
            echo "\t<th>";
            switch ($property) {
                case "Update" :
                    break;
                default :
                    $sortKey = NavajoPhpClient :: getPropertyName($nav, $property, $msg);
                    $sortDirection = (isset ($_REQUEST['sortDir']) && $_REQUEST['sortDir'] == 'ASC') ? 'DESC' : 'ASC';
                    $msgName = $msg->getAttribute('name');
                    if(!isset($_SESSION['subPage'])) {
                        $_SESSION['subPage'];
                    }
                    echo "<a href='index.php?sortKey=" . $sortKey . "&sortDir=" . $sortDirection . "&action=".$_SESSION['currentPage']."&msgName=".$msgName."&nav=".$nav."&".$_SESSION['subPage']."'>";
                    NavajoPhpClient :: showDescription($nav, $property, $msg);
                    echo "</a>";
            }
            echo "</th>\n";

        }
        echo "</tr>\n";
    }
    protected function render($nav, $msg) {

        global $i;
        $sfx = ($i % 2) ? 'odd' : 'even';
        echo "<tr class='row_" . $sfx . "'>";
        echo "<td>" . $i . "</td>";

        foreach ($this->myprops as $property) {
            echo "\t<td>";
            switch ($property) {
                case "Update" :
                    NavajoPhpClient :: showProperty($nav, $property, $msg, false, false, "_checkbox");
                    break;
                default :
                    NavajoPhpClient :: showProperty($nav, $property, $msg, true);
            }
            echo "</td>\n";
        }
        $i++;
        echo "</tr>\n";
    }

    protected function afterRendering() {
        echo "</table>\n";
    }

}
?>