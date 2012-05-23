<?php defined('_JEXEC') or die('Restricted access'); ?>
<p>Vul uw relatiecode en wachtwoord in om in te loggen<br/>
Als u succesvol bent ingelogd verschijnen een aantal extra opties onder het "Mijn Club" menu.</p>
<form action="<?php echo JRoute::_( 'index.php', true, $this->params->get('usesecure')); ?>" method="post" name="com-login" id="com-form-login">
    <div class="loginelement">
        <label for="username" style='width: 100px; display: block; float:left;'>Relatiecode</label>
        <input name="username" id="username" type="text" class="inputbox" alt="username" size="10" />
    </div>
    <div class="loginelement">
        <label for="passwd" style='width: 100px; display: block; float:left;'>Wachtwoord</label>
        <input name="passwd" id="passwd" type="password" class="inputbox" alt="password" size="10" />
    </div>
    <input type="submit" name="Submit" class="button" value="<?php echo JText::_('LOGIN') ?>" />
    <input type="hidden" name="option" value="com_user" />
    <input type="hidden" name="task" value="login" />
    <input type="hidden" name="return" value="<?php echo $this->return; ?>" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
