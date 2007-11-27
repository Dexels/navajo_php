<?php
	abstract class TableLayout extends NavajoLayout {
    abstract protected function renderHeader($nav, $message_element,$params);

		var $myprops;
		function __construct($properties) {
			$this->myprops = $properties;
		}

		protected function beforeRendering() {
			echo '<table>';
		}
		
		
		protected function render($nav,$msg,$params) {
			echo '<tr>';
			foreach($this->myprops as $property) {
				echo '<td>';
				echo 'hoei';
				NavajoPhpClient::showProperty($nav,$property,$msg,$params);
				echo '</td>';
			}
			echo '</tr>';
		}

		protected function afterRendering() {
			echo '</table>';
		}
		
	}
?>