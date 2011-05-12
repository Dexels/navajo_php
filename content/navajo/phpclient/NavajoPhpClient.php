<?php
class NavajoPhpClient {

    static function showTable($navname, $msgpath, $columns, $params="", $columnWidths = "", $columnLabels = "", $columnDirections = "", $updateNavajo = "", $deleteNavajo = "",$tableId = "") {
        require_once "NavajoLayout.php";   
        $result = getNavajo($navname, 'showTable');
        if ($result == null) { return; }
        $data = $result->getMessage($msgpath);
        if (is_null($data)) {
            echo ("Message: $msgpath not found in navajo: $navname");
            # $result->printXml();
            return;
        }
        if(isset($params["customLayout"])) {
            require_once $params["customLayout"].".php";   
            $layout = new $params["customLayout"]($columns, $params, $columnWidths, $columnLabels, $columnDirections);
          } else {
            require_once "AdvancedTableLayout.php";   
               $layout = new AdvancedTableLayout($columns, $params, $columnWidths, $columnLabels, $columnDirections,$tableId);
        }
        if($updateNavajo != "") {
#            echo "<input type='submit' name='submit' class='updateBtn hidden' value='Update_" . $navname . "' />";
#            echo "<input type='hidden' name='Update_" . $navname . ":target' value='". $_SESSION['currentPage'] . "' />";
#            echo "<input type='hidden' name='Update_" . $navname . ":serverCall' value='" . $navname . ":" . $updateNavajo . "' />";
        }
        if($deleteNavajo != "") {
#            echo "<input type='submit' name='submit' class='deleteBtn hidden' value='Delete_" . $navname . "' />";
#            echo "<input type='hidden' name='Delete_" . $navname . ":target' value='". $_SESSION['currentPage'] . "' />";
#            echo "<input type='hidden' name='Delete_" . $navname . ":serverCall' value='" . $navname . ":" . $deleteNavajo . "' />";
        }
        $layout->doRender($navname, $data, $params);
    }

    static function setValue($navname, $proppath, $value) {
        $nav = getNavajo($navname, 'setValue');
        if ($nav != null) : 
            $p   = $nav->getAbsoluteProperty($proppath);
            extract($_GET);
            if(strpos($value, "@") == 0) {
                if (isset(${substr($value, 1)})) { 
                    $value = ${substr($value, 1)};
                }
            }
            if($p != null) :
                $p->setValue($value);
            endif;
        else :
            return;
        endif;
    }

    static function showNavajo($navajo, $params) {
        if(is_object($navajo)) {
            $messages = $navajo->getMessages();
            foreach ($messages as $current) {
                self :: showMessage($navajo, $current, $params);
            }
            self :: showMethods($navajo);
            }
        }

    static function showMessage($navajo, $message, $params) {
   
         if (isset($params["label"])) {
            $label = $params["label"];
        } else {
            $label = $message->getName(); 
        }        
        echo "<h1 class='messageName'>" . $label . "</h1>";
        if ($message->getType() == "array") {
            $c = $message->getAllMessages();
            if (count($c) == 0) {
                echo "\n<p class='error'>empty array message: " . $message->getName() . "</p>\n";
                return;
            }
            $elt0 = $message->getArrayMessage(0);
       
            if ($elt0 != null) {
                $columns = $elt0->getPropertyNames();
                self :: showTable($navajo->getService(), $message->getPath(), $columns,"","","", $params);
            }
        } else {
            if ($message == null) {
                echo "\n<p class='error'>empty message, no data found.</p>\n";
                return;
            }

            $msgs = $message->getAllMessages();
            $props = $message->getAllProperties();
            $m = $message->getAllProperties();

            foreach ($msgs as $mmm) {
                self :: showMessage($navajo, $mmm, $params);
            }

            foreach ($props as $pp) {
                $id = $pp->getPropertyId();
                NavajoPhpClient :: renderDescription($pp, $params, "");
                NavajoPhpClient :: renderProperty($navajo, $pp, $id, $params, false, "");
            }
        }

    }

    static function showMethods($navajo) {
        if(!isset($navajo) || !is_object($navajo)) {
            return;
        }
        $methods = $navajo->getAllMethods();
        if ($methods != null) {
            echo "\n<div class='methods'>\n";
            foreach ($methods as $method) {
                self :: showMethod($navajo, $method);
            }
            echo "\n</div>\n";
        }

    }

    function showMethod($navajo, $method, $params = null) {
        echo "\n<div class='method'>\n";
        $service = $navajo->getService();
        
        if (isset($params["label"])) {
            $label = $params["label"];
        } else {
            $label = $method->getName(); 
        }               
        if (isset ($_REQUEST["id"])) {
            $id = $_REQUEST["id"];
        } else {
            $id = $method->getName();
        }
        if (isset ($_REQUEST["Itemid"])) {
            echo "<input type='hidden' name='" . $label . ":itemid' value='" . $_REQUEST["Itemid"] . "'/>";
        }
    
        echo "<input type='submit' name='direction' value='" . $label . "'/>";
        echo "<input type='hidden' name='" . $label . "' value='" . $id . "'/>";
        echo "<input type='hidden' name='" . $label . ":serverCall' value='" . $service . ":" . $method->getName() . "'/>";
        echo "<input type='hidden' name='" . $label . ":id' value='" . $id . "'/>";
        echo "<input type='hidden' name='joomlaSession' value='" . session_name() . "'/>";            
        echo "\n</div>\n";
    }

    static function showAbsoluteProperty($navname, $path, $params, $blnOut = false, $blnDescription = false, $classsuffix = "") {
        $nav = getNavajo($navname, 'showAbsoluteProperty');
        if (is_null($nav)) {
            echo "<!-- showAbsoluteProperty(): Navajo not found: " . $navname . "-->";
        } else {
            $p = $nav->getAbsoluteProperty($path);
            
            if (is_null($p)) {
               echo "<!-- showAbsoluteProperty(): property " . $path . " not found in : " . $navname . "-->";
            } else {
                $id = $p->getPropertyId();
                if ($blnDescription) {
                    self :: renderDescription($p, $params, $classsuffix);
                }
                self :: renderProperty($nav, $p, $id, $params, $blnOut, $classsuffix);
                //echo "\n<span/>";
            }
        }
    }

    static function showProperty($navname, $path, $message, $params, $blnOut = false, $blnDescription = false, $classsuffix = "") {
        $nav = getNavajo($navname, 'showProperty');
        $p = $message->getProperty($path);
        $id = $p->getPropertyId();

        if ($blnDescription) {
            self :: renderDescription($p, $params, $classsuffix);
        }
        self :: renderProperty($nav, $p, $id, $params, $blnOut, $classsuffix);
    }

    static function showDescription($navname, $path, $message, $params, $classsuffix = "") {
        $nav = getNavajo($navname, 'showDescription');
        $p = $message->getProperty($path);
        self :: renderDescription($p, $params, $classsuffix);
    }

    static function showAbsoluteDescription($navname, $path, $params, $classsuffix = "") {
        $nav = getNavajo($navname, 'showAbsoluteDescription');
        $p = $nav->getAbsoluteProperty($path);
        self :: renderDescription($p, $params, $classsuffix);
    }

    static function getPropertyName($navname, $path, $message) {
        $p = $message->getProperty($path);
        return $p->getName();
    }
 
    static private function renderDescription($property, $params, $classsuffix = "") {
        $desc = $property->getAttribute("description");
        if ($desc == null) {
            $desc = $property->getAttribute("name");
        }
        if (isset($params['width'])) {
            $width = "style='width:" . $params['width'] . "px'";
        } else {
            $width = "";
        }
        echo "\n<label " . $width . ">" . $desc . "</label>";
    }

    static private function renderProperty($nav, $property, $id, $params, $blnOut = false, $classsuffix = "") {
        $type = $property->getAttribute("type");
        $direction = $property->getAttribute("direction");
        if ($direction == "in" && $blnOut == false) {
            switch ($type) {
                case "string" :
                    NavajoPhpClient :: inputStringProperty($nav, $property, $id, $params, $classsuffix);
                    break;
                case "boolean" :
                    NavajoPhpClient :: inputBooleanProperty($nav, $property, $id, $params, $classsuffix);
                    break;
                case "integer" :
                    NavajoPhpClient :: inputIntegerProperty($nav, $property, $id, $params, $classsuffix);
                    break;
                case "date" :
                    NavajoPhpClient :: inputDateProperty($nav, $property, $id, $params, $classsuffix);
                    break;
                case "memo" :
                    NavajoPhpClient :: inputMemoProperty($nav, $property, $id, $params, $classsuffix);
                    break;
                case "binary" :
                    NavajoPhpClient :: inputBinaryProperty($nav, $property, $id, $params, $classsuffix);
                    break;
                case "selection" :
                    $cardinality = $property->getAttribute("cardinality");
                    if ($cardinality == "1") {
                        NavajoPhpClient :: inputSingleSelectionProperty($nav, $property, $id, $params, $classsuffix);
                    } else {
                        NavajoPhpClient :: inputMultiSelectionProperty($nav, $property, $id, $params, $classsuffix);
                    }
                    break;
                default :
                    NavajoPhpClient :: inputStringProperty($nav, $property, $id, $params, $classsuffix);
            }
        } else {
            switch ($type) {
                case "string" :
                    NavajoPhpClient :: outputStringProperty($nav, $property, $id, $params, $classsuffix);
                    break;
                case "boolean" :
                    NavajoPhpClient :: outputBooleanProperty($nav, $property, $id, $params, $classsuffix);
                    break;
                case "integer" :
                    NavajoPhpClient :: outputIntegerProperty($nav, $property, $id, $params, $classsuffix);
                    break;
                case "date" :
                    NavajoPhpClient :: outputDateProperty($nav, $property, $id, $params, $classsuffix);
                    break;
                case "clocktime" :
                    NavajoPhpClient :: outputClockTimeProperty($nav, $property, $id, $params, $classsuffix);
                    break;
                case "memo" :
                    NavajoPhpClient :: outputMemoProperty($nav, $property, $id, $params, $classsuffix);
                    break;
                case "binary" :
                    NavajoPhpClient :: outputBinaryProperty($nav, $property, $id, $params, $classsuffix);
                    break;
                case "selection" :
                    $cardinality = $property->getAttribute("cardinality");
                    if ($cardinality == "1") {
                        NavajoPhpClient :: outputSingleSelectionProperty($nav, $property, $id, $params, $classsuffix);
                    } else {
                        NavajoPhpClient :: outputMultiSelectionProperty($nav, $property, $id, $params, $classsuffix);
                    }
                    break;
                default :
                    NavajoPhpClient :: outputStringProperty($nav, $property, $id, $params, $classsuffix);
            }
        }
    }

    # functions for input properties (direction == "in")

    static function inputStringProperty($nav, $property, $id, $params, $classsuffix) {
        $value = $property->getAttribute('value');
        $length = $property->getAttribute('length');
        if (isset($params['width'])) {
            $width = "style='width:" . $params['width'] . "px'";
        } else {
            $width = "style='width:150px'";
        }
        echo "<input type='text' class='property" . $classsuffix . "' name='" . $id . "' value='" . $value . "' maxlength='" . $length . "' $width/>";

    }

    static function inputIntegerProperty($nav, $property, $id, $params, $classsuffix) {
        $value = $property->getAttribute("value");
        if (isset($params['width'])) {
            $width = "style='width:" . $params['width'] . "px'";    
        } else {
            $width = "style='width:150px'";
        }
        echo "<input type='text' class='property" . $classsuffix . "' name='" . $id . "' value='" . $value . "' $width/>";
    }

    static function inputBooleanProperty($nav, $property, $id, $params, $classsuffix) {
        $value = $property->getAttribute("value");
        # use the hidden var to always have a POST property
        echo "<input type='hidden' name='" . $id . "'/>";
        # alse create the checkboxes omitting the navajo|message prefix, to map their existence on the hidden variable
        $explode = explode("|", $id);
        $propertypath = $explode[2];
        if ($value == "true") {
            echo "<input type='checkbox' class='checkbox" . $classsuffix . "' name='" . $propertypath . "' checked='true'/>";

        } else {
            echo "<input type='checkbox' class='checkbox" . $classsuffix . "' name='" . $propertypath . "' />";
        }
    }

    static function inputSingleSelectionProperty($nav, $property, $id, $params, $classsuffix) {
        $value = $property->getAttribute("value");
        $opt = $property->getAllSelections();
        if (isset($params['width'])) {
            $width = "style='width:" . $params['width'] . "px'";
        } else {
            $width = "style='width:150px'";
        }
        echo "<select class='property" . $classsuffix . "' name='" . $id . "' " . $width . ">";

        foreach ($opt as $current_node) {
            $name = $current_node->getAttribute("name");
            $value = $current_node->getAttribute("value");
            $selected = $current_node->getAttribute("selected");
            if ($selected == "1") {
                echo "<option value='" . $value . "' selected>" . $name . "</option>";
            } else {
                echo "<option value='" . $value . "'>" . $name . "</option>";
            }
        }
        echo "</select>";
    }

    static function inputMemoProperty($nav, $property, $id, $params, $classsuffix) {
        $value= preg_replace('!<br.*>!iU', "\n", $property->getValue());
        $length = $property->getAttribute('length');
        echo "<textarea cols='20' rows='" . $length . "' class='property" . $classsuffix . "' name='" . $id . "' id='" . $id . "'>" . $value . "</textarea>";
        # echo "<script language='JavaScript'>generate_wysiwyg('" . $id . "');</script>";
    }

    static function inputMultiSelectionProperty($nav, $property, $id, $params, $classsuffix) {
        $value = $property->getAttribute("value");
        $opt = $property->getAllSelections();
    $size = floor(count($opt) / 2);
        echo "<select class='property" . $classsuffix . "' name='" . $id . "[]' multiple size='".$size."'>";

        foreach ($opt as $current_node) {
            $name = $current_node->getAttribute("name");
            $value = $current_node->getAttribute("value");
            $selected = $current_node->getAttribute("selected");
            if ($selected == "1") {
                echo "<option value='" . $value . "' selected>" . $name . "</option>";
            } else {
                echo "<option value='" . $value . "'>" . $name . "</option>";
            }
        }
        echo "</select>";
    }

    static function inputDateProperty($nav, $property, $id, $params, $classsuffix = "") {
        $value = date("Y-m-d", strtotime($property->getAttribute("value")));
        if (isset($params['width'])) {
            $width = "style='width:" . $params['width'] . "px'";
        } else {
            $width = "style='width:150px'";
        }
        echo "<input type='text' class='date property" . $classsuffix . "' name='" . $id . "' value='" . $value . "' $width />";
    }

    static function inputBinaryProperty($nav, $property, $id, $params, $classsuffix) {
        # TODO re-implement
    }

    # functions for ouput properties (direction == "out")

    static function outputStringProperty($nav, $property, $id, $params, $classsuffix) {
        $value = $property->getAttribute("value");
        if ($value == "")
            $value = "-";
        if(isset($params['subtype']) && $params['subtype'] == 'url') {
            if ( strpos($value, "http") === false ) {
                if(isset($params['label'])) {
              echo "<a href=\"http://".$value."\" target=\"_blank\" ><input type=\"button\" class=\"labelbutton\" value=\"". $params['label'] ."\" /></a>";
                } else {
              echo "<a href=\"http://".$value."\" target=\"_blank\" >" . $value . "</a>";
                }
            } else {
                if(isset($params['label'])) {
              echo "<a href=\"".$value."\" target=\"_blank\" ><input class=\"labelbutton\" type=\"button\" value=\"". $params['label'] ."\" /></a>";
                } else {
              echo "<a href=\"".$value."\" target=\"_blank\" >" . $value . "</a>";
                }
            }
        }
    else { echo $value; }
    }

    static function outputIntegerProperty($nav, $property, $id, $params, $classsuffix) {
        $value = $property->getAttribute("value");
        echo $value;
    }

    static function outputBooleanProperty($nav, $property, $id, $params, $classsuffix) {
        $value = $property->getAttribute("value");
        if ($value == "true") {
            echo "<input type='checkbox' class='checkbox' name='" . $id . "' checked='true' disabled/>";
        } else {
            echo "<input type='checkbox' class='checkbox' name='" . $id . "' disabled />";
        }
    }

    static function outputMemoProperty($nav, $property, $id, $params, $classsuffix, $blnDescription = false) {
        $value = $property->getAttribute("value");
        if ($value == "")
            $value = "-";
        if ($blnDescription) {
/// removed html_entity_decode() around $value
        	echo "<div class='property_value'>" . $value . "</div>";
        } else {
          echo $value;
        }
    }

    static function outputSingleSelectionProperty($nav, $property, $id, $params, $classsuffix) {
        $opt   = $property->getAllSelections($property);
    $selectedValue = "-";
        foreach ($opt as $current_node) {
            $name = $current_node->getAttribute("name");
            $selected = $current_node->getAttribute("selected");
            if ($selected == "1") {
                $selectedValue = $name;
            }
        }
        echo $selectedValue;
    }

    static function outputMultiSelectionProperty($nav, $property, $id, $params, $classsuffix) {
        $value = $property->getAttribute("value");
        if ($value == "true") {
            echo "<input type='checkbox' class='checkbox' name='" . $id . "' checked='true' disabled />";

        } else {
            echo "<input type='checkbox' class='checkbox' name='" . $id . "' disabled />";
        }
    }

    static function outputDateProperty($nav, $property, $id, $params, $classsuffix) {
        $value = date("d/m/Y", strtotime($property->getAttribute("value")));
        echo $value;
    }
    
    static function outputClockTimeProperty($nav, $property, $id, $params, $classsuffix) {
        $value = $property->getAttribute("value");
        if($value != '') { 
            $value = date("H:i", strtotime($value));
            echo $value;
        }
    }
    
    static function outputBinaryProperty($nav, $property, $id, $params, $classsuffix) {
        $site = "http://" . $_SERVER['HTTP_HOST'];

        $rr = explode("|", $id);
        $service = $rr[1];
        $path = $rr[2];

        $myBinary = base64_decode($property->getBinaryValue());
        $fileName = rand() . ".jpg";
        if (!$handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/tmp/" . $fileName, 'w')) {
                 echo "Cannot open file ($fileName)";
                 exit;
        }

        fwrite($handle, $myBinary);

        # binaries come in 2 flavours: as images and as "download" links -> get extension
        $extension = "";
        $subTypeList = explode(",", $property->getSubType());
        if (isset ($subTypeList[0])) {
            $extensionList = explode("=", $subTypeList[0]);
            if (isset ($extensionList[1])) {
                $extension = $extensionList[1];
            }
        }
        switch ($extension) {
            case "jpg" :
                echo "<img src='/tmp/$fileName'/>";
                break;
            default :
                echo "<img src='/tmp/$fileName'/>";
        }
        fclose($handle);
        # unlink($_SERVER['DOCUMENT_ROOT'] . "/tmp/" . $fileName);
    }

    static function outputMethods($service) {
        $nav = getNavajo($service, 'outputMethods');
        $methods = $nav->methodsNode;
        $nodelist = $methods->childNodes;
        for ($i = 0; $i < $nodelist->length; $i++) {
            if (get_class($nodelist->item($i)) == "DOMElement") {
                $currentvalue = $nodelist->item($i)->getAttribute("name");
                echo $currentvalue;
            }
        }
    }
}
?>
