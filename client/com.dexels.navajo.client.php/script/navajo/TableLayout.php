<?php
	class TableLayout extends NavajoLayout {

		var $myprops;
		function __construct($properties) {
			$this->myprops = $properties;
		}

		protected function beforeRendering() {
			echo '<table>';
		}
		
		
		protected function render($nav,$msg) {
			echo '<tr>';
			foreach($this->myprops as $property) {
				echo '<td>';
				NavajoPhpClient::showProperty($nav,$property,$msg);
				echo '</td>';
			}
			echo '</tr>';
		}

		protected function afterRendering() {
			echo '</table>';
		}
		
	}
?>