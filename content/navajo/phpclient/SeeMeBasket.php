<?php
class SeeMeBasket extends NavajoLayout {
    var $i;
    var $myprops;

    function __construct($properties) {
        $this->myprops = $properties;
    }

    protected function beforeRendering($params) {
        global $i;
        $i = 1;
        echo "<table cellpadding='3' cellspacing='0' border='0' width='450'>\n";
    }

    protected function renderHeader($nav, $msg,$params) {    	
    }
    protected function render($nav, $msg,$params) {
	$n = getNavajo($nav);
        global $i;
        $candidateName = '';
        echo "<tr>\n";
        foreach ($this->myprops as $property) {
	    $p = $msg->getProperty($property);
            switch ($property) {
                case "ResumeId" :
                        $resumeId = $p->getValue();
                        break;
                case "SeeMeCode" :
                        echo "<td>";
                        echo "<a href='http://www.seemework.nl/index.php?option=com_content&task=view&id=22&Itemid=14&seemecode=" . $p->getValue() . "&resumeid=" . $resumeId . "' target='_parent'>";
		 	echo "<img border='0' style='padding:5px;' src='http://www.seemework.nl/snapshots/" . $p->getValue() . "_P1.jpg' height='120' alt='SeeMeCV'/>";
                        echo "</a></td>";
                        $seeMeLink = "http://www.seemework.nl/seemelink.php?cv=" . $p->getValue();
			break;
		case "LastName" :
                        echo "<td valign='top'>";
                        $candidateName = $candidateName . $p->getValue();
			break;
		case "Initials" :
                        $candidateName = $candidateName . ' ' . $p->getValue();
			break;
		case "Infix" :
                        if ($p->getValue() != '') {
                            $candidateName = $candidateName . ', ' . $p->getValue();
                        }
			echo "<b style='font-size:13px;'>" . $candidateName . "</b><br/>";
			break;
		case "Remarks" :
                        echo "<p style='font-size:11px'>SeeMeLink : " . $seeMeLink . "</p>";
                        NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, " remarks");
                        echo "</td>";
			break;
		case "Update" :
                        echo "<td valign='top'>";
                        NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, " update hidden");
			break;
		case "Delete" :
                        NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, " delete hidden");
			break;
                        echo "</td>";
                default :
            }
        }
        echo "</tr>\n";
        $i++;
    }

    protected function afterRendering($params) {
        echo "</table>\n";
    }
}
?>
