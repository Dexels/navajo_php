<?php defined('_JEXEC') or die('Restricted access'); ?>
<h3>U bent aangemeld bij Sportlink Athlete</h3>
<p>Druk op onderstaande button om uit te loggen:<br/><br/></p>
<form action="index.php" method="post" name="login" id="login">
    <input type="submit" name="Submit" class="button" value="<?php echo JText::_( 'Logout' ); ?>" />
    <input type="hidden" name="option" value="com_user" />
    <input type="hidden" name="task" value="logout" />
    <input type="hidden" name="return" value="<?php echo $this->return; ?>" />
</form>
