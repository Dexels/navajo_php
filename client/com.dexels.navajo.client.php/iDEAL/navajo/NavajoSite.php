<?php
abstract class NavajoSite {
    public abstract function onStartSession();
    public abstract function onDestroySession();
    public abstract function echoPageHeader();
    public abstract function echoPageFooter();
    public abstract function echoPanelHeader();
    public abstract function echoPanelFooter();
}
?>