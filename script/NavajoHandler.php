<?php
	//phpinfo();
	require_once "NavajoPhpClient.php";
	require_once "NavajoClient.php";
	
	session_start();
	

	NavajoClient::updateNavajoFromPost();
	
	$action = $_REQUEST['serverCall'];
	if(!is_null($action)) {
		$actions = explode(';',$action);
		foreach( $actions as $current) {
			$initscr = explode(':',$current);
			if(count($initscr)==2) {
				$nnn = NavajoClient::callService($initscr[0],$initscr[1]);
			} else {
				$nnn = NavajoClient::callInitService($initscr[0]);
			}
		}
	}
	
	if($_REQUEST['action']=='exit') {
		session_destroy();
	}
	
	if(!is_null($_REQUEST['next'])) {
		include $_REQUEST['next'];
	}
	
	?>

	
	