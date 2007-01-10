<?php
//include "php_xml_navajo.inc";

error_reporting(E_ALL);


class NavajoClient {

	static function getServer() {
		return $_SESSION['navajoServer'];
	}

	static function setServer($s) {
		$_SESSION['navajoServer'] = $s;
	} 

	static function getUser() {
		return $_SESSION['navajoUser'];
	}

	static function setUser($s) {
		$_SESSION['navajoUser'] = $s;
	} 

	static function getPassword() {
		return $_SESSION['navajoPassword'];
	}

	static function setPassword($s) {
		$_SESSION['navajoPassword'] = $s;
	} 
	
	
static function processNavajo($bDebug = false, $service, $navajo) {
	//echo 'Calling service: '.$service;
	if(is_null($navajo)) {
		trace('No navajo supplied. Not good');
	}
	$navajo->setHeaderAttributes(self::getUser(),self::getPassword(),$service);

	$ch = curl_init(); // initialize curl handle
	if (isset($GLOBALS['postManProxy'])) {
		curl_setopt($ch, CURLOPT_PROXY, $GLOBALS['postManProxy']); // set rpoxy to use for request
	}
	curl_setopt($ch, CURLOPT_URL,self::getServer()); // set url to post to
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
	curl_setopt($ch, CURLOPT_TIMEOUT, 10); // times out after 10s
	curl_setopt($ch, CURLOPT_POSTFIELDS, $navajo->saveXML()); // add POST fields

	
	$result = curl_exec($ch);

	$res = new Navajo();
	$res->parseXml($result);
	$accessId = $res->getAccessId();
	$service = $res->getService();

	$error = $res->getMessage('error');
	if(!is_null($error)) {
		trace('<h2>SERVER SIDE</h2>Error calling service: '.$service);
	}
	$cerror = $res->getMessage('ConditionErrors');
	if(!is_null($cerror)) {
		trace('<h2>SERVER SIDE</h2> Contidion error calling service: '.$service);
	}
	$_SESSION['navajo@'.$service] = $result;
	$_SESSION['currentNavajo'] = $accessId;
	return $res;
}
	
static function callInitService($service) {
		$nav = new Navajo();
	$nav->setHeaderAttributes(self::getUser(),self::getPassword(),$service);	
	echo $nav->saveXML();
	$_SESSION['navajo@'.$service] = $nav->saveXML();
	
	return self::processNavajo(true,$service,$nav);
}
static function doSimpleSend($s, $navajo) {
	return self::processNavajo(true,$s,$navajo);
}

static function callService($source, $service) {
	$n = getNavajo($source);
	$r =  self::doSimpleSend($service,$n);
	return $r;
}




static function updateNavajoFromPost() {
		foreach($_REQUEST as $current_var => $value) {
			$explode = explode('|',$current_var);
			$aaa = $explode[0];
			if($aaa!='navajo') {
					continue;
			}
			$s = $explode[1];
			$propertypath = $explode[2];
			$n = getNavajo($s);
			
			$property = $n->getAbsoluteProperty($propertypath);
			$property->setAttribute('value',$value);
			$_SESSION['navajo@'.$s] = $n->saveXML();
		}
	}



}

	function startupNavajo($server,$username,$password) {
		NavajoClient::setUser($username);
		NavajoClient::setPassword($password);
		NavajoClient::setServer($server);
	
	}
function getNavajo($s) {
	$data = $_SESSION['navajo@'.$s];
	$n = new Navajo();
	if(is_null($data)) {
		trace('Navajo: '.$s.' not found!');
		print_r($_SESSION);
	}
	$n->parseXml($data);
	return $n;
}



	?>