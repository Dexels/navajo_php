<?php
class SeeMeLayout extends NavajoLayout {
    var $i;
    var $myprops;

    function __construct($properties) {
        $this->myprops = $properties;
    }

    protected function beforeRendering($params) {
        global $i;
        $i = 1;
        echo "<table cellpadding='0' cellspacing='0' border='0' width='550'>\n";
    }

    protected function renderHeader($nav, $msg,$params) {    	
    }
    protected function render($nav, $msg,$params) {
	setlocale(LC_ALL, 'nl_NL');
	$n = getNavajo($nav);
        global $i;
        echo "<tr>\n";
	echo "<td align='left'>";

        foreach ($this->myprops as $property) {
	    $p = $msg->getProperty($property);
            switch ($property) {
                case "FromDate" :
		 	echo "<h4>";
			echo "Van " . $p->getValue();
			break;
		case "ToDate" :
			echo " tot " . $p->getValue();
			echo "</h4>";
			break;
		case "Employer" :
			echo "<div style='width:110px;float:left;'>Werkgever:</div>" . $p->getValue() . "<br/>";
			break;
		case "Function" :
			echo "<div style='width:110px;float:left'>Functie:</div>" . $p->getValue() . "<br/>";
			break;
		case "HasFinished" :
			$v = ($p->getValue() == "true")?"ja":"nee";
			echo "<div style='width:110px;float:left;'>Afgerond?</div>" . $v . "<br/>";
			break;
		case "Description" :
			echo "<div style='width:110px;float:left;height:30px;'>Omschrijving:</div>" . $p->getValue() . "<br/>";
			break;
                default :
			echo $p->getValue() . " ";
            }
        }
        $i++;
        echo "</td>\n";
        echo "</tr>\n";
    }

    protected function afterRendering($params) {
        echo "</table>\n";
    }
}
?>
