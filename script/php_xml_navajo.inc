<?php    
  
$postMan = 'http://slwebsvr4.sportlink.enovation.net:10080/sportlink/knvb/servlet/Postman';
$navajoUsr = '';
$navajoPwd = '';

$webService = '';
$navajoDoc = '';
$currentMsg = '';

function setPostman($urlPostman) {
	$GLOBALS['postMan'] = $urlPostman;
}

function setNavajoUser($strNavajoUsr, $strNavajoPwd) {
	$GLOBALS['navajoUsr'] = $strNavajoUsr;
	$GLOBALS['navajoPwd'] = $strNavajoPwd;
}

function setWebService($strWebService) {
	$GLOBALS['webService'] = $strWebService;
}

function setNavajoDocument($newTmlDoc) {
	$GLOBALS['navajoDoc'] = $newTmlDoc;
}

function createNavajo() {

	unset ($GLOBALS['navajoDoc']);
	unset ($GLOBALS['currentMsg']);

	$navajoDoc = new DOMDocument('1.0', 'iso-8859-1');

	$tmlNode = $navajoDoc->createElement("tml");
	$tmlNode = $navajoDoc->appendChild($tmlNode);

	$headerNode = $navajoDoc->createElement("header");
	$headerNode = $tmlNode->appendChild($headerNode);

	$transactionNode = $navajoDoc->createElement("transaction");
	$transactionNode->setAttribute("rpc_usr", $GLOBALS['navajoUsr']);
	$transactionNode->setAttribute("rpc_pwd", $GLOBALS['navajoPwd']);
	$transactionNode->setAttribute("rpc_name", $GLOBALS['webService']);
	$transactionNode->setAttribute("expiration_interval", "-1");

	$headerNode->appendChild($transactionNode);

	$GLOBALS['navajoDoc'] = $navajoDoc;

	return $navajoDoc;
}

function addMessage($msgName) { // adds a messageNode to the root of the $navajoDoc

	$rootNode = $GLOBALS['navajoDoc']->documentElement;
	$msgNode = $GLOBALS['navajoDoc']->createElement("message");
	$msgNode->setAttribute("name", $msgName);
	$rootNode->appendChild($msgNode);

	$GLOBALS['currentMsg'] = $msgNode;

	return $msgNode;
}

function addArrayMessage($msgName, $blnRootNode = true) { // adds a simple messageNode (type array, with submsg) to the root of the $navajoDoc

        $parentNode = $GLOBALS['navajoDoc']->createElement("message");
        $parentNode->setAttribute("name", $msgName);
        $parentNode->setAttribute("type", "array");

        $childNode = $GLOBALS['navajoDoc']->createElement("message");
        $childNode->setAttribute("name", $msgName);
        $childNode->setAttribute("count", "1");

        $parentNode->appendChild($childNode);

        if($blnRootNode) {
                $rootNode = $GLOBALS['navajoDoc']->documentElement;
                $rootNode->appendChild($parentNode);
        } else {
                $GLOBALS['currentMsg']->appendChild($parentNode);
        }

        $GLOBALS['currentMsg'] = $childNode;
        return $childNode;
}

function addProperty($propName, $propType, $propValue, $propLength = "") { // adds a property to the msg

	$propNode = $GLOBALS['navajoDoc']->createElement("property");
	$propNode->setAttribute("name", $propName);
	$propNode->setAttribute("type", $propType);
	$propNode->setAttribute("direction", "in");
	$propNode->setAttribute("length", $propLength);
	if($propType == 'selection') {
		$propNode->setAttribute("cardinality", "1");	
		addOption($propValue, $propNode);	
	} else {
		$propNode->setAttribute("value", $propValue);	
	}
	$GLOBALS['currentMsg']->appendChild($propNode);

	return $propNode;
}

function addOption($optionValue, $parentNode) { // adds an option to a selection property 

	$optionNode = $GLOBALS['navajoDoc']->createElement("option");
	$optionNode->setAttribute("name", $optionValue);
	$optionNode->setAttribute("value", $optionValue);	
	$optionNode->setAttribute("selected", "1");	
	$parentNode->appendChild($optionNode);

	return $parentNode;
}

function addExpression($exprValue, $parentNode) { // adds an expression with a value to a parent node (e.g. a property)

	$exprNode = $GLOBALS['navajoDoc']->createElement("expression");
	$exprNode->setAttribute("value", $exprValue);
	$parentNode->appendChild($exprNode);

	return $parentNode;
}

function processNavajo($bDebug = false) {
	if (isset ($bDebug) && $bDebug == true) {
		printXml($GLOBALS['navajoDoc']->saveXML());
	}

	$ch = curl_init(); // initialize curl handle
	if (isset($GLOBALS['postManProxy'])) {
		curl_setopt($ch, CURLOPT_PROXY, $GLOBALS['postManProxy']); // set rpoxy to use for request
	}
	curl_setopt($ch, CURLOPT_URL, $GLOBALS['postMan']); // set url to post to
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
	curl_setopt($ch, CURLOPT_TIMEOUT, 10); // times out after 10s
	curl_setopt($ch, CURLOPT_POSTFIELDS, $GLOBALS['navajoDoc']->saveXML()); // add POST fields

	$result = curl_exec($ch);
	if (isset ($bDebug) && $bDebug == true) {
		printXml($result);
	}

	return $result;
}

function postArrayToTml($array) {

	ksort($array);

	$keys = array_keys($array);
	$values = array_values($array);

	$rootNode = $GLOBALS['navajoDoc']->documentElement;

	for ($i = 0; $i < count($array); $i ++) {

		$value = $values[$i];
		$key = $keys[$i];

		$tokenizer_one = explode("|", $key);
		$tokenizer_two = explode("/", $tokenizer_one[0]);

		$propName = $tokenizer_two[count($tokenizer_two) - 1];
		$type = $tokenizer_one[1];

		// create a property node

		$propNode = $GLOBALS['navajoDoc']->createElement("property");
		$propNode->setAttribute("name", $propName);
		$propNode->setAttribute("type", $type);
		$propNode->setAttribute("direction", "in");

		if ($type == "selection") {
			$propNode->setAttribute("cardinality", "1");	
			$optionNode = $GLOBALS['navajoDoc']->createElement("option");
			$optionNode->setAttribute("name", $value);
			$optionNode->setAttribute("value", $value);
			$optionNode->setAttribute("selected", "1");
			$propNode->appendChild($optionNode);
		} if ($type == "boolean") { // booleans are always true, otherwise HTML forms omit them
			$propNode->setAttribute("value", "true");
                } else {
			$propNode->setAttribute("value", $value);
		}

		for ($j = 0; $j < count($tokenizer_two) - 1; $j ++) {

			if (count($tokenizer_two) - 1 == 1) {
				// echo "<br>it's a root message <br><br>";
				$parentNode = $rootNode;
			}

			$msgName = $tokenizer_two[$j];

			if ($j < (count($tokenizer_two) - 2)) {

				// first check if the message already exists there

				$blnPresent = false;
				$node_array = $rootNode->getElementsByTagName("message");
			
				// echo "<br><br> Searching for message : ".$msgName." under root"; 
				foreach($node_array as $current_node) {
					if ($msgName == $current_node->getAttribute("name")) {
						$blnPresent = true;
					}	
				}

				// if not there append it and make if of type array

				if ($blnPresent != true) {

					// echo "<br>Creating message : ".$msgName." under root"; 

					$msgNode = $GLOBALS['navajoDoc']->createElement("message");
					$msgNode->setAttribute("name", $msgName);
					$msgNode->setAttribute("type", "array");
					$rootNode->appendChild($msgNode);
					$parentNode = $msgNode;
				}
			} else { // message is not a root message, but a sub message

				// first check if the message already exists in it's parent message

				$blnPresent = false;
				$node_array = $parentNode->getElementsByTagName("message");
				
				// echo "<br><br> Searching for message : ".$msgName." under submsg : ".$parentNode->node_name()." - ".$parentNode->getAttribute("name"); 
				foreach($node_array as $current_node) {
					if ($msgName == $current_node->getAttribute("name")) {
						$blnPresent = true;
					}	
				}

				if ($blnPresent != true) {

					// echo "<br>Creating message : ".$msgName." under submsg : ".$parentNode->node_name()." - ".$parentNode->getAttribute("name"); 

					$msgNode = $GLOBALS['navajoDoc']->createElement("message");
					$msgNode->setAttribute("name", $msgName);
					$parentNode->appendChild($msgNode);
				}
			}
		}
		$msgNode->appendChild($propNode);
	}
}

function hasConditionErrors($str_xml) {
	$new_tml = new DomDocument;
	$new_tml->loadXML($str_xml);
	$allMsg = (array) $new_tml->getElementsByTagName('message');

	foreach ($allMsg as $currentMsg) {
		if ($currentMsg->getAttribute('type') != 'array' && $currentMsg->getAttribute('name') == 'ConditionErrors') {
			return true;
		}
	}
	return false;
}

function getMessagePropertyValue($str_xml, $msgName, $propName) {
        $new_tml = new DomDocument;
        $new_tml->loadXML($str_xml);
        $allMsg = $new_tml->getElementsByTagName('message');
        foreach ($allMsg as $currentMsg) {
                if ($currentMsg->getAttribute('type') != 'array' && $currentMsg->getAttribute('name') == $msgName) {
                        $allPrp = $currentMsg->childNodes;
                        foreach ($allPrp as $currentPrp) {
                                if ($currentPrp->nodeName != '#text') {
                                        if ($currentPrp->getAttribute('name') == $propName) {
                                                $allAttr = $currentPrp->attributes;
                                                if($currentPrp->getAttribute('type') == 'selection') {
                                                   $allOpt = $currentPrp->childNodes;
                                                   foreach ($allOpt as $currentOpt) {
                                                      if($currentOpt->getAttribute('selected') == '1'){
                                                        return $currentOpt->getAttribute('value');
                                                      }
                                                   }
                                                }else{
                                                    foreach ($allAttr as $currentAttr) {
                                                        if ($currentAttr->name == 'value' && $currentAttr->value != '')
                                                                return $currentAttr->value;
                                                        else
                                                                return $currentPrp->nodeValue;
                                                    }
                                                }
                                        }
                                }

                        }
                }
        }
        return null;
}

function printConditionErrors($str_xml) {

	## Check for condition errors				#
	#  1. look for message name 'ConditionErrors'		#		
	#  2. get all child properties			   	#
	#  3. filter out non-text properties		  	#
	#  4. get attribute with name 'Description'        	#
	#  5. get attribute value			   	#

	$new_tml = new DomDocument;
	$new_tml->loadXML($str_xml);
	$allMsg = $new_tml->getElementsByTagname('message');

	foreach ($allMsg as $currentMsg) {
		if ($currentMsg->getAttribute('type') != 'array' && $currentMsg->getAttribute('name') == 'ConditionErrors') {
			$allPrp = $currentMsg->childNodes;
			foreach ($allPrp as $currentPrp) {
				if ($currentPrp->nodeName != '#text') {
					if ($currentPrp->getAttribute('name') == 'FailedExpression') { # SET THIS TO 'Description' WHEN YOU HAVE THEM
						$allAttr = $currentPrp->attributes;
						foreach ($allAttr as $currentAttr) {
							if ($currentAttr->name == 'value')
								echo "<p class='error'>".$currentAttr->value."</p>";
						}
					}
				}

			}
		}
	}
}

function printXml($xmlDoc) {
	echo "<pre>";
	echo htmlentities($xmlDoc);
	echo "</pre>";
}

function simpleTransform($tml_string, $xsl_file, $bDebug = false, $params = Array()) {
	$xmlDom = new DomDocument;
	$xslDom = new DomDocument;
	
	$xmlDom->loadXML($tml_string);
	$xslDom->load($xsl_file);

	$xsltProc = new XsltProcessor();
	$xsltProc->importStyleSheet($xslDom);
	foreach ($params as $name => $value)
	{
		$xsltProc->setParameter ('', $name, $value);
	}
	$xmlResult = $xsltProc->transformToXML($xmlDom);

	if (isset ($bDebug) && $bDebug == true) {
		printXml($xmlDom);
	}
	unset($xsltProc);
	return $xmlResult;
	
}

?>
