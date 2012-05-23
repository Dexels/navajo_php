<?php
class TeamResultsMatrix extends NavajoLayout {
    var $i;
    var $myprops;
    var $columnWidths;
    var $myTeams;
    var $resultsMatrix;

    function __construct($properties, $params, $columnWidths="", $columnLabels="", $columnDirections="") {
        $this->myprops          = $properties;
        $this->columnWidths     = $columnWidths;
        $this->columnLabels     = $columnLabels;        
        $this->columnDirections = $columnDirections;
    }

    protected function beforeRendering($nav, $params) {
        global $i, $id, $itemId, $colGroup, $tableCSS, $totalWidth;
        $i = 1;
        $id     = (isset($params["id"]))?$params["id"]:null;
        $itemId = (isset($params["Itemid"]))?$params["Itemid"]:null;
    }

    protected function renderHeader($nav, $msg, $params) {
        error_reporting(E_ALL);
        global $myTeams, $colGroup, $tableCSS, $totalWidth;
        $j = 1;
        $myTeams = array(); 
        $resultsMatrix = array();
 
        echo "\n<table id='" . str_replace("/", "_", $nav) . "' class='teammatrix " . $tableCSS . "' cellpadding='3' cellspacing='0' border='0' width='" . $totalWidth . "'>\n";
        echo $colGroup;

        echo "<thead>\n";
        echo "<tr>\n";
        echo "<th>&nbsp;</th>";
        $myNavajo = getNavajo($nav);
        $allMsgs = $myNavajo->getMessage("Teams")->getSubMessages();    
        foreach($allMsgs as $headerMsg) {
            $homeTeam = $headerMsg->getProperty("TeamDescription");
            $myTeams[$j] = $homeTeam->getValue();
            echo "\n<th class='header'>" . $myTeams[$j] . "</th>";
            $j++;
        }
        echo "</tr>\n";
        echo "</thead>\n";
        echo "<tbody>\n";
    }

    protected function render($nav, $msg, $params) {
        global $i, $myTeams, $id, $itemId, $resultsMatrix;
        $sfx = ($i % 2) ? "" : "altRow";
        echo "<tr id='" . $i . "' class='" . $sfx . "'>\n";

        $currentTeam = $msg->getProperty("TeamDescription")->getValue();
        $resultsMatrix[$i][0] = $currentTeam;
        
        $teamResults = $msg->getMessage("TeamResults");
        $opponents = $teamResults->getAllMessages();
        foreach ($opponents as $opponent) {
            $awayTeam    = "";
            $homeGoals= "";
            $awayGoals= "";
        
            $props = $opponent->getAllProperties();
            foreach ($props as $pp) {
                if ($pp->getName() == "AwayTeamDescription") {
                    $awayTeam = $pp->getValue();
                }
                if ($pp->getName() == "HomeGoals") {
                    $homeGoals = $pp->getValue();
                }
                if ($pp->getName() == "AwayGoals") {
                    $awayGoals = $pp->getValue(); 
                }
            }
            $j = 1;
            foreach ($myTeams as $otherTeam) {
                if ($otherTeam == $awayTeam) {
                    if ($otherTeam != $currentTeam) {
                        $resultsMatrix[$i][$j] = $homeGoals . " - " . $awayGoals;
                    } else { 
                      $resultsMatrix[$i][$j] = '-';  
                    }
                }  
                $j++;
            } 
        }
        $i++;
    }

    protected function renderFooter($nav, $msg, $params) {
        global $myTeams, $resultsMatrix;
        for ($i = 1; $i <= count($resultsMatrix); $i++) {
            echo "<tr>";
            echo "<td class='header'>" . $resultsMatrix[$i][0] . "</td>";
            for ($j = 1; $j <= count($resultsMatrix); $j++) {
                if (isset($resultsMatrix[$i][$j])) {
                    if ( $resultsMatrix[$i][$j] == '-' ) { 
                        echo "<td class='disabled'>&nbsp;</td>";
                    } else {
                        echo "<td>" . $resultsMatrix[$i][$j] . "</td>";
                    }
                } else {
                    echo "<td class='undefined'>&nbsp;</td>";
                }
            } 
            echo "</tr>\n";
        } 
        echo "</tbody>\n";
        echo "</table>\n";
    }
    protected function afterRendering($nav, $params) {
    
    }
}
?>
