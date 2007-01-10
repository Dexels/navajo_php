<?php
	require_once "NavajoClient.php";
	require_once "NavajoDocument.php";
	require_once "NavajoLayout.php";
	
	class NavajoPhpClient {
		
		
		static function showAbsoluteProperty($navname,$path, $classsuffix = '') {
			$nav = getNavajo($navname);
			if(is_null($nav)) {
				trace('Navajo not found: '.$navname);
			}
			$p = $nav->getAbsoluteProperty($path);
			if(is_null($p)) {
				trace('Property not found: '.$navname);
			}
			
			$id = $nav->getPropertyId($p);
	//		echo '>>>>>>'.$id.'<br/>';
			NavajoPhpClient::renderProperty($nav, $p,$id,$classsuffix);
		}
		
		static function showProperty($navname,$path,$message, $classsuffix = '') {
			$nav = getNavajo($navname);
			$p = $nav->getProperty($path,$message);
			$id = $nav->getPropertyId($p);
		//	echo '>>>>>'.$id.'<br/>';
			self::renderProperty($nav, $p,$id,$classsuffix);
		}

		static private function renderProperty($nav, $property, $id, $classsuffix = '') {
//echo 'id>'.$id.'<id';
			$type = $property->getAttribute('type');
			if($type == 'string') {
				NavajoPhpClient::showStringProperty($nav, $property,$id,$classsuffix);
			}
			if($type == 'boolean') {
				NavajoPhpClient::showBooleanProperty($nav, $property,$id,$classsuffix);
			}
			if($type == 'integer') {
				NavajoPhpClient::showIntegerProperty($nav, $property,$id,$classsuffix);
			}
			if($type == 'date') {
				NavajoPhpClient::showDateProperty($nav, $property,$id,$classsuffix);
			}
			if($type == 'selection') {
				$cardinality = $property->getAttribute('cardinality');
				if($cardinality == '1') {
					NavajoPhpClient::showSingleSelectionProperty($nav, $property,$id,$classsuffix);
				} else {
					NavajoPhpClient::showMultiSelectionProperty($nav, $property,$id,$classsuffix);
				}
			}
		}
	
		static function showStringProperty($nav, $property,$id,$classsuffix = '') {
			$value= $property->getAttribute('value');
			echo '<input type="text" class="property'.$classsuffix.'" name="'.$id.'" value="'.$value.'"/>';
		}
		
		static function showIntegerProperty($nav, $property,$id,$classsuffix = '') {
			$value= $property->getAttribute('value');
			echo '<input type="text" class="property'.$classsuffix.'" name="'.$id.'" value="'.$value.'"/>';
		}

		static function showBooleanProperty($nav, $property,$id,$classsuffix = '') {
			$value= $property->getAttribute('value');
			if($value=='true') {
				echo '<input type="checkbox" class="property'.$classsuffix.'" name="'.$id.'" checked="true"/>';

			} else {
				echo '<input type="checkbox" class="property'.$classsuffix.'" name="'.$id.'"/>';
			}
		}
		
		

		//<select name="normal/noot"><option value="Aap">Aap</option><option value="Noot">Noot</option><option value="Mies">Mies</option><option selected value="Wim">Wim</option><option value="Zus">Zus</option></select></td>
		
		
		static function showSingleSelectionProperty($nav, $property,$id,$classsuffix = '') {
			$value= $property->getAttribute('value');
			$opt = $nav->getAllSelections($property);
			echo '<select name="'.$id.'">';

				
				// echo "<br><br> Searching for message : ".$msgName." under root"; 
				foreach($opt as $current_node) {
					$name = $current_node->getAttribute('name');
					$value = $current_node->getAttribute('value');
					$selected = $current_node->getAttribute('selected');
					if($selected=='1') {
						echo '<option value="'.$value.'" selected>'.$name.'</option>';
					} else {
						echo '<option value="'.$value.'">'.$name.'</option>';
					}
				}			
			echo '</select>';
			
		}	
		
		static function showMultiSelectionProperty($nav, $property,$id,$classsuffix = '') {
			$value= $property->getAttribute('value');
			if($value=='true') {
				echo '<input type="checkbox" class="property'.$classsuffix.'" name="'.$id.'" checked="true"/>';

			} else {
				echo '<input type="checkbox" class="property'.$classsuffix.'" name="'.$id.'"/>';
			}
		}	

		static function showDateProperty($nav, $property,$id,$classsuffix = '') {
			$value= $property->getAttribute('value');
			echo '<input type="text" class="property'.$classsuffix.'" name="'.$id.'" value="'.$value.'"/>';
		}
				
		
		
	}

?>