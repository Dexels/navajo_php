<?php
class NavajoPhpClient {

    static function showTable($navname, $msgpath, $columns, $params="", $columnWidths = "", $columnLabels = "", $columnDirections = "", $updateNavajo = "", $deleteNavajo = "") {
        require_once "NavajoLayout.php";   
        $result = getNavajo($navname);

        $data = $result->getMessage($msgpath);
        if (is_null($data)) {
            echo ("Message: $msgpath not found in navajo: $navname");
            $result->printXml();
            return;
        }
        if(isset($params["customLayout"])) {
            require_once $params["customLayout"].".php";   
            $layout = new $params["customLayout"]($columns);
          } else {
            require_once "AdvancedTableLayout.php";   
       	    $layout = new AdvancedTableLayout($columns, $params, $columnWidths, $columnLabels, $columnDirections);
        }
        if($updateNavajo != "") {
            echo "<input type='submit' name='submit' class='updateBtn hidden' value='Update_" . $navname . "' />";
            echo "<input type='hidden' name='Update_" . $navname . ":target' value='". $_SESSION['currentPage'] . "' />";
            echo "<input type='hidden' name='Update_" . $navname . ":serverCall' value='" . $navname . ":" . $updateNavajo . "' />";
        }
        if($deleteNavajo != "") {
            echo "<input type='submit' name='submit' class='deleteBtn hidden' value='Delete_" . $navname . "' />";
            echo "<input type='hidden' name='Delete_" . $navname . ":target' value='". $_SESSION['currentPage'] . "' />";
            echo "<input type='hidden' name='Delete_" . $navname . ":serverCall' value='" . $navname . ":" . $deleteNavajo . "' />";
        }
        $layout->doRender($navname, $data, $params);
    }

    static function setValue($navname, $proppath, $value) {
        $nav = getNavajo($navname);
        $p = $nav->getAbsoluteProperty($proppath);
	    extract($_GET);
	    if(strpos($value, "@") == 0) {
		    if (isset(${substr($value, 1)})) { 
  		        $value = ${substr($value, 1)};
            }
   	    }
        $p->setValue($value);
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
        $nav = getNavajo($navname);
        if (is_null($nav)) {
            echo "<!-- showAbsoluteProperty(): Navajo not found: " . $navname . "-->";
        } else {
            $p = $nav->getAbsoluteProperty($path);
            
            if (is_null($p)) {
               echo "<!-- showAbsoluteProperty(): property " . $path . " not found in : " . $navname . "-->";
            } else {
                $id = $p->getPropertyId();
                echo "\n<label>";
                if ($blnDescription) {
                    self :: renderDescription($p, $params, $classsuffix);
                }
                echo "\n</label>";
                self :: renderProperty($nav, $p, $id, $params, $blnOut, $classsuffix);
                echo "\n<span/>";
            }
        }
    }

    static function showProperty($navname, $path, $message, $params, $blnOut = false, $blnDescription = false, $classsuffix = "") {
        $nav = getNavajo($navname);
        $p = $message->getProperty($path);
        $id = $p->getPropertyId();

        if ($blnDescription) {
            self :: renderDescription($p, $params, $classsuffix);
        }
        self :: renderProperty($nav, $p, $id, $params, $blnOut, $classsuffix);
    }

    static function showDescription($navname, $path, $message, $params, $classsuffix = "") {
        $nav = getNavajo($navname);
        $p = $message->getProperty($path);
        self :: renderDescription($p, $params, $classsuffix);
    }

    static function showAbsoluteDescription($navname, $path, $params, $classsuffix = "") {
        $nav = getNavajo($navname);
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
                case "memo" :
                    NavajoPhpClient :: outputMemoProperty($nav, $property, $id, $params, $classsuffix, $blnDescription);
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

       // $config = JFactory :: getConfig();
        //$site = $config->getValue("config.sitename");
        $site = "beheer";
echo "BInARYAAP";
        $rr = explode("|", $id);
        $service = $rr[1];
        $path = $rr[2];

        if (isset ($params["height"])) {
            $height = $params["height"];
        }
        if (isset ($params["width"])) {
            $width = $params["width"];
        }
        $joomlaSession = session_name();

        # store the binary value in an array

        if (isset ($_SESSION["myBinary[$service][$path]"])) {
            unset ($_SESSION["myBinary[$service][$path]"]);
        }

        $_SESSION["myBinary[$service][$path]"] = $property->getBinaryValue();

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
                echo "\n<img src='/" . $site . "/components/com_navajo/Binary.php?extension=" . $extension . "&service=" . $service . "&amp;path=" . $path . "&amp;joomlaSession=" . $joomlaSession . "'/>";
                break;
            case "gif" :
                echo "\n<img src='/" . $site . "/components/com_navajo/Binary.php?extension=" . $extension . "&service=" . $service . "&amp;path=" . $path . "&amp;joomlaSession=" . $joomlaSession . "'/>";
                break;
            case "png" :
                echo "\n<img src='/" . $site . "/components/com_navajo/Binary.php?extension=" . $extension . "&service=" . $service . "&amp;path=" . $path . "&amp;joomlaSession=" . $joomlaSession . "'/>";
                break;
            case "pdf" :
                echo "\n<a href='/" . $site . "/components/com_navajo/Binary.php?extension=" . $extension . "&service=" . $service . "&amp;path=" . $path . "&amp;joomlaSession=" . $joomlaSession . "'>download <img src='/images/M_images/pdf_button.png' border='0' /></a>";
                break;
            case "dat" :
            	echo "<p id='player1'><a href='http://www.macromedia.com/go/getflashplayer'>Get the Flash Player</a> to see this player.</p>";
            	echo "<script type='text/javascript'>";
				echo "var FO = { movie:\"/" . $site . "/components/com_navajo/flvplayer.swf\"," .
								"width:\"300\"," .
								"height:\"170\"," .
								"majorversion:\"7\"," .
								"build:\"0\"," .
								"bgcolor:\"#FFFFFF\"," .
								"allowfullscreen:\"false\"," .
								"flashvars:\"file=http://".$_SERVER["HTTP_HOST"] ."/" . $site . "/components/com_navajo/Binary.php?extension=" . $extension . "&service=" . $service . "&path=" . $path . "&joomlaSession=" . $joomlaSession . "};";
								//"flashvars:\"file=/" . $site . "/components/com_navajo/video.flv\" };";
				echo "UFO.create( FO, \"player1\");";
				echo "</script>";
            	/*
            	echo "\n<script language='JavaScript'>";                            
            	echo "\nvar flash = new show_flash('http://".$_SERVER["HTTP_HOST"] ."/" . $site . "/components/com_navajo/xevidwidebotplus.swf?" .
            		    "myfilm1=http://".$_SERVER["HTTP_HOST"] ."/" . $site . "/components/com_navajo/Binary.php?extension=" . $extension . "&service=" . $service . "&path=" . $path . "&joomlaSession=" . $joomlaSession . 
            			"&amp;mytext1=..." .
            			"&amp;automode=1" .
            			"&amp;embcode=0" .
            			"&amp;stretch=http://".$_SERVER["HTTP_HOST"] .
            			"&amp;thumbsbg=0xdc7608" .
            			"&amp;img=no" .
            			"&amp;width=352" .
            			"&amp;height=318" .
            			"&amp;embed=http://".$_SERVER["HTTP_HOST"] ."/" . $site . "/components/com_navajo/xevidwidebotplus.swf?myfilm1=http://".$_SERVER["HTTP_HOST"] ."/" . $site . "/components/com_navajo/Binary.php?extension=" . $extension . "&service=" . $service . "&path=" . $path . "&joomlaSession=" . $joomlaSession . "'" .
            			", 'xevidwidebotplus'" .
            			", '352'" .
            			", '318'" .
            			", 'transparent');";
            		echo "\n</script>";
            		*/
                break;
            default :
            	echo "";                
        }

        echo "\n<div id='text_" . $path . "'><a onclick=\"new Fx.Style('file_" . $path . "', 'opacity', { duration: 500 }).start(0,1); new Fx.Style('text_" . $path . "', 'opacity', { duration: 500 }).start(1,0);\">edit</a></div>";
        echo "\n<div id='file_" . $path . "' style='opacity:0' class='property'>";
        echo "\n<input type='file' name='" . $id . "'/>";
        echo "\n</div>";

    }

    # functions for ouput properties (direction == "out")

    static function outputStringProperty($nav, $property, $id, $params, $classsuffix) {
        $value = $property->getAttribute("value");
        if ($value == "")
            $value = "-";
        echo $value;
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
          echo "<div class='property_value'>" . html_entity_decode($value) . "</div>";
        } else {
          echo html_entity_decode($value);
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
    
    static function outputBinaryProperty($nav, $property, $id, $params, $classsuffix) {
        $config = JFactory :: getConfig();
        # $site = $config->getValue("config.sitename");
        $site = "";

        $rr = explode("|", $id);
        $service = $rr[1];
        $path = $rr[2];

        $joomlaSession = session_name();

        # store the binary value in an array

        if (isset ($_SESSION["myBinary[$service][$path]"])) {
            unset ($_SESSION["myBinary[$service][$path]"]);
        }

        $_SESSION["myBinary[$service][$path]"] = $property->getBinaryValue();

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
                echo "<img src='" . $site . "plugins/content/navajo/Binary.php?extension=" . $extension . "&service=" . $service . "&amp;path=" . $path . "&amp;joomlaSession=" . $joomlaSession . "'/>";
                break;
            case "gif" :
                echo "<img src='" . $site . "plugins/content/navajo/Binary.php?extension=" . $extension . "&service=" . $service . "&amp;path=" . $path . "&amp;joomlaSession=" . $joomlaSession . "'/>";
                break;
            case "png" :
                echo "<img src='" . $site . "plugins/content/navajo/Binary.php?extension=" . $extension . "&service=" . $service . "&amp;path=" . $path . "&amp;joomlaSession=" . $joomlaSession . "'/>";
                break;
            case "pdf" :
                echo "<a href='" . $site . "plugins/content/navajo/Binary.php?extension=" . $extension . "&service=" . $service . "&amp;path=" . $path . "&amp;joomlaSession=" . $joomlaSession . "'><img src='/" . $site . "/images/M_images/pdf_button.png' border='0' /> download</a>";
                break;
            default :
                echo "";
        }
    }

    static function outputMethods($service) {
        $nav = getNavajo($service);
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
