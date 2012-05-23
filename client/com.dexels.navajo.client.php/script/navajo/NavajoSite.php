<?php

 abstract class NavajoSite {
	public abstract function onStartSession();
	public abstract function onDestroySession();
 	public abstract function echoHeader();

	public abstract function echoFooter();

	}
?>