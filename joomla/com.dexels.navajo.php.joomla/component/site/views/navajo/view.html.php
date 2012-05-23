<?php
/**
* @package		HelloWorld
* @license		GNU/GPL, see LICENSE.php
*/

jimport( 'joomla.application.component.view');

class NavajoViewNavajo extends JView
{
	function display($tpl = null)
	{		
		$greeting = "Hello World!";
		$this->assignRef( 'greeting',	$greeting );

		parent::display($tpl);
	}
}
?>
