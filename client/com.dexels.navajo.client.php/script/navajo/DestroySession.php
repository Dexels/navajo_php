<?php
	session_start();
	$_SESSION['site']->onDestroySession();
	
	session_destroy();
?>

De sessie is weg, ouwe.
