<?php
class AdvancedTableLayout extends NavajoLayout {
	var $i;
	var $myprops;
	var $columnWidths;

	function __construct($properties, $params, $columnWidths="", $columnLabels="") {
		$this->myprops = $properties;
		$this->columnWidths = $columnWidths;
		$this->columnLabels = $columnLabels;
	}

	protected function beforeRendering($nav, $params) {
		global $i, $key, $link, $id, $itemId;
		$i = 1;
		$key    = (isset($params["key"]))?$params["key"]:null;
		$link   = (isset($params["link"]))?$params["link"]:null;
		$id     = (isset($params["id"]))?$params["id"]:null;
		$itemId = (isset($params["Itemid"]))?$params["Itemid"]:null;
		
		$totalWidth = "0";
		if(is_array($this->columnWidths)) {
			foreach ($this->columnWidths as $currentWidth) {
				$totalWidth += $currentWidth + 3;
			}
		} else {
			$totalWidth = "100%";
		}
		echo "\n<table id='" . $nav . "' class='sortable-onload-1 rowstyle-alt no-arrow' cellpadding='3' cellspacing='0' border='0' width='" . $totalWidth . "'>\n";
	}

	protected function renderHeader($nav, $msg, $params) {
		global $key;
		$j = 0;
		echo "<thead>\n";
		echo "<tr>\n";
		
		foreach ($this->myprops as $property) {
			$params['width'] = (isset($this->columnWidths[$j]))?$this->columnWidths[$j]:"100";
			$params['label'] = (isset($this->columnLabels[$j]))?$this->columnLabels[$j]:$msg->getProperty($property)->getDescription();
			switch ($property) {
		        case $key :
					break;        		
				case "Update" :
					echo "\t<th width='" . $params['width'] . "' />";
					break;
				case "Delete" :
					echo "\t<th width='" . $params['width'] . "' />";
					break;
				default :
					echo "\t<th width='" . $params['width'] . "' class='sortable-text'>" . $params['label'] . "</th>\n";
			}
			$j++;
		}
		echo "</tr>\n";
		echo "</thead>\n";
		echo "<tbody>\n";
	}
	protected function render($nav, $msg, $params) {
		global $i, $key, $link, $id, $itemId;
		$j = 0;
		
		$keyValue = null;
		$sfx = ($i % 2) ? "odd" : "even";
		echo "<tr class='row_" . $sfx . "'>\n";
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
		        case $link :
		       		echo "\t<td>";
		            echo "<a href='index.php?option=com_content&view=article&id=" . $id . "&Itemid=" . $itemId . "&" . $key . "=" . $keyValue . "'>";
		        	NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, "");
		        	echo "</a>";
		        	echo "</td>\n";
		        	break;
				default :
				    echo "\t<td>";
					NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, "");
					echo "</td>\n";
			}
			
			$j++;
		}
		$i++;
		echo "</tr>\n";
	}

	protected function afterRendering($nav, $params) {
		echo "</tbody>\n";
		echo "</table>\n";
	
	}

}
?>
