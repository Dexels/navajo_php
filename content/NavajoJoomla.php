<?php
	require_once('include/NavajoPhpClient.php');
	$server = getNavajoParameter('serverUrl');
	$user = getNavajoParameter('user');
	$password = getNavajoParameter('password');	
	startupNavajo($server,$user,$password);
	
	function getNavajoParameter($id) {
		$db = & JFactory::getDBO();
		$query = 'SELECT value FROM jos_navajo_config where id="'.$id.'"';
		$db->setQuery($query);
		return $db->loadResult();
	}
?>