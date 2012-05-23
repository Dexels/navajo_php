<?php
	require_once "NavajoClient.php";
	require_once "NavajoDocument.php";
	require_once "NavajoLayout.php";
	require_once "NavajoSite.php";
	
	class NavajoPhpClient {
				
		static function showAbsoluteProperty($navname, $path, $blnOut = false, $blnDescription = false, $classsuffix = '') {
			$nav = getNavajo($navname);
			if(is_null($nav)) {
				trace('Navajo not found: '.$navname);
			}
			$p = $nav->getAbsoluteProperty($path);
			if(is_null($p)) {
				trace('Property not found: '.$navname);
			}			
			$id = $nav->getPropertyId($p);
			if($blnDescription) {
				NavajoPhpClient::renderDescription($p, $classsuffix);
			}
			NavajoPhpClient::renderProperty($nav, $p, $id, $blnOut, $classsuffix);
		}
		
		static function showProperty($navname, $path, $message, $blnOut = false, $blnDescription = false, $classsuffix = '') {
			$nav = getNavajo($navname);
			$p   = $nav->getProperty($path, $message);
			$id  = $nav->getPropertyId($p);
			if($blnDescription) {
				self::renderDescription($p, $classsuffix);
			}
			self::renderProperty($nav, $p, $id, $blnOut, $classsuffix);			
		}
		
		static function showDescription($navname, $path, $message, $classsuffix = '') {
			$nav = getNavajo($navname);
			$p   = $nav->getProperty($path, $message);			
			self::renderDescription($p, $classsuffix);		
		}
		
		static function getPropertyName($navname, $path, $message) {
			    $nav = getNavajo($navname);
				$p   = $nav->getProperty($path, $message);
				return $p->getAttribute('name');		
		}
		
		static private function renderDescription($property, $classsuffix = '') {		    
				echo "\n<label class='property".$classsuffix."'>".$property->getAttribute('description')."</label>\n";							   					
		}

		static private function renderProperty($nav, $property, $id, $blnOut = false, $classsuffix = '') {
			$type      = $property->getAttribute('type');
			$direction = $property->getAttribute('direction');
			
			if ($direction == 'in' || $blnOut == false) {
			    switch($type) {
					case "string":
						NavajoPhpClient::inputStringProperty($nav, $property, $id, $classsuffix);
						break;
					case "boolean":
						NavajoPhpClient::inputBooleanProperty($nav, $property, $id, $classsuffix);
						break;
					case "integer":
						NavajoPhpClient::inputIntegerProperty($nav, $property, $id, $classsuffix);
						break;
					case "date":
						NavajoPhpClient::inputDateProperty($nav, $property, $id, $classsuffix);
						break;
					case "selection":
						$cardinality = $property->getAttribute('cardinality');
						if($cardinality == '1') {
							NavajoPhpClient::inputSingleSelectionProperty($nav, $property, $id, $classsuffix);
						} else {
							NavajoPhpClient::inputMultiSelectionProperty($nav, $property, $id, $classsuffix);
						}
						break;
					default:
						NavajoPhpClient::inputStringProperty($nav, $property, $id, $classsuffix);
				}					
			} else {
			     switch($type) {
					case "string":
						NavajoPhpClient::outputStringProperty($nav, $property, $id, $classsuffix);
						break;
					case "boolean":
						NavajoPhpClient::outputBooleanProperty($nav, $property, $id, $classsuffix);
						break;
					case "integer":
						NavajoPhpClient::outputIntegerProperty($nav, $property, $id, $classsuffix);
						break;
					case "date":
						NavajoPhpClient::outputDateProperty($nav, $property, $id, $classsuffix);
						break;
					case "selection":
						$cardinality = $property->getAttribute('cardinality');
						if($cardinality == '1') {
							NavajoPhpClient::outputSingleSelectionProperty($nav, $property, $id, $classsuffix);
						} else {
							NavajoPhpClient::outputMultiSelectionProperty($nav, $property, $id, $classsuffix);
						}
						break;
					default:
						NavajoPhpClient::outputStringProperty($nav, $property, $id, $classsuffix);
				}					
			    
			}
		}
		
		# functions for input properties (direction == "in")
		
		static function inputStringProperty($nav, $property, $id, $classsuffix) {
			$value= $property->getAttribute('value');						
			echo '<input type="text" class="property'.$classsuffix.'" name="'.$id.'" value="'.$value.'"/>';
			
		}
		
		static function inputIntegerProperty($nav, $property, $id, $classsuffix) {
			$value= $property->getAttribute('value');		
			echo '<input type="text" class="property'.$classsuffix.'" name="'.$id.'" value="'.$value.'"/>';
		}

		static function inputBooleanProperty($nav, $property, $id, $classsuffix) {
			$value= $property->getAttribute('value');			
			if($value == 'true') {
				echo '<input type="checkbox" class="property'.$classsuffix.'" name="'.$id.'" checked="true"/>';

			} else {
				echo '<input type="checkbox" class="property'.$classsuffix.'" name="'.$id.'"/>';
			}
		}	
		
		static function inputSingleSelectionProperty($nav, $property, $id, $classsuffix) {
			$value = $property->getAttribute('value');			
			$opt   = $nav->getAllSelections($property);
			echo '<select class="property'.$classsuffix.'" name="'.$id.'">';

				foreach($opt as $current_node) {
					$name     = $current_node->getAttribute('name');
					$value    = $current_node->getAttribute('value');
					$selected = $current_node->getAttribute('selected');
					if($selected == '1') {
						echo '<option value="'.$value.'" selected>'.$name.'</option>';
					} else {
						echo '<option value="'.$value.'">'.$name.'</option>';
					}
				}			
			echo '</select>';			
		}	
		
		static function inputMultiSelectionProperty($nav, $property, $id, $classsuffix) {
			$value= $property->getAttribute('value');			
			if($value == 'true') {
				echo '<input type="checkbox" class="property'.$classsuffix.'" name="'.$id.'" checked="true"/>';

			} else {
				echo '<input type="checkbox" class="property'.$classsuffix.'" name="'.$id.'"/>';
			}
		}	

		static function inputDateProperty($nav, $property, $id, $classsuffix = '') {
			$value = $property->getAttribute('value');			
			echo '<input type="text" class="property'.$classsuffix.'" name="'.$id.'" value="'.$value.'"/>';
		}
				
		# functions for ouput properties (direction == "out")
		
		static function outputStringProperty($nav, $property, $id, $classsuffix) {
			$value= $property->getAttribute('value');			
			echo '<div class="property'.$classsuffix.'">'.$value.'</div>';
		}
		
		static function outputIntegerProperty($nav, $property, $id, $classsuffix) {
			$value= $property->getAttribute('value');			
			echo '<div class="property'.$classsuffix.'">'.$value.'</div>';
		}

		static function outputBooleanProperty($nav, $property, $id, $classsuffix) {
			$value= $property->getAttribute('value');			
			if($value == 'true') {
				echo '<input type="checkbox" class="property'.$classsuffix.'" name="'.$id.'" checked="true" disabled/>';

			} else {
				echo '<input type="checkbox" class="property'.$classsuffix.'" name="'.$id.'" disabled />';
			}
		}	
		
		static function outputSingleSelectionProperty($nav, $property, $id, $classsuffix) {
			$value = $property->getAttribute('value');		
			$opt   = $nav->getAllSelections($property);
			echo '<select class="property'.$classsuffix.'" name="'.$id.'" disabled>';

				foreach($opt as $current_node) {
					$name     = $current_node->getAttribute('name');
					$value    = $current_node->getAttribute('value');
					$selected = $current_node->getAttribute('selected');
					if($selected == '1') {
						echo '<option value="'.$value.'" selected>'.$name.'</option>';
					} else {
						echo '<option value="'.$value.'">'.$name.'</option>';
					}
				}			
			echo '</select>';			
		}	
		
		static function outputMultiSelectionProperty($nav, $property, $id, $classsuffix) {
			$value= $property->getAttribute('value');			
			if($value == 'true') {
				echo '<input type="checkbox" class="property'.$classsuffix.'" name="'.$id.'" checked="true" disabled />';

			} else {
				echo '<input type="checkbox" class="property'.$classsuffix.'" name="'.$id.'" disabled />';
			}
		}	

		static function outputDateProperty($nav, $property, $id, $classsuffix) {
			$value = date('d-m-Y', strtotime($property->getAttribute('value')));			
			echo '<div class="property'.$classsuffix.'">'.$value.'</div>';
		}				
	}

?>