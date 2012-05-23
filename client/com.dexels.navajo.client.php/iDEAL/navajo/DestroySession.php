<?php
	session_start();
	$_SESSION['site']->onDestroySession();
	
	session_destroy();
?>