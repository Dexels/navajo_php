<?php
class AdvancedTableLayout extends NavajoLayout {
	var $i;
	var $myprops;
	var $columnWidths;

	function __construct($properties, $columnWidths="") {
		$this->myprops = $properties;
		$this->columnWidths = $columnWidths;
	}

	protected function beforeRendering($params) {
		global $i;
		$i = 1;
		$totalWidth = "0";
		if(is_array($this->columnWidths)) {
			foreach ($this->columnWidths as $currentWidth) {
				$totalWidth += $currentWidth + 3;
			}
		}
		echo "<table class='navajo' cellpadding='3' cellspacing='0' border='0' width='" . $totalWidth . "'>\n";
	}

	protected function renderHeader($nav, $msg, $params) {
		echo "<tr>\n";
		$j = 0;
		foreach ($this->myprops as $property) {
			$params['width'] = (isset($this->columnWidths[$j]))?$this->columnWidths[$j]:"100";
			switch ($property) {
				case "Update" :
					echo "\t<th width='" . $params['width'] . "' />";
					break;
				case "Delete" :
					echo "\t<th width='" . $params['width'] . "' />";
					break;
				default :
					echo "\t<th width='" . $params['width'] . "' >";
					$msgName = $msg->getAttribute("name");
					NavajoPhpClient :: showDescription($nav, $property, $msg, $params);
					echo "</th>\n";
			}
			$j++;
		}
		echo "</tr>\n";
	}
	protected function render($nav, $msg, $params) {
		global $i;
		$j = 0;
		$sfx = ($i % 2) ? "odd" : "even";
		echo "<tr class='row_" . $sfx . "'>\n";
		foreach ($this->myprops as $property) {
			$params['width'] = (isset($this->columnWidths[$j]))?$this->columnWidths[$j]:"100";
			echo "\t<td>";
			switch ($property) {
				case "Update" :
					NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, " update hidden");
					break;
				case "Delete" :
					NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, " delete hidden");
					break;
				default :
					NavajoPhpClient :: showProperty($nav, $property, $msg, $params, false, false, "");
			}
			echo "</td>\n";
			$j++;
		}
		$i++;
		echo "</tr>\n";
	}

	protected function afterRendering($params) {
		echo "</table>\n";
	}

}
?>
