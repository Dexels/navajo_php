<?php
class WebSite extends NavajoSite {
    public function onStartSession() {
        startupNavajo('http://ficus:3000/sportlink/knvb/servlet/Postman', 'ROOT', '');
        NavajoClient :: callInitService('clubasp/InitUpdateClubSubscription');
    }
    public function onDestroySession() {
    }

    public function echoPageHeader() {

        global $siteHome, $defaultPage, $siteContent, $siteTitle;

        echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>";
        echo "<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en-gb' lang='en-gb' dir='ltr'>";
        echo "<head>";
        echo "<title>" . $siteTitle . "</title>";
        echo "<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>";
        echo "<meta name='description' content='" . $siteContent . "'>";
        echo "<link rel='stylesheet' type='text/css' href='" . $siteHome . "navajo/css/sportlink.css'/>";
        echo "</head>";
        echo "<body style='margin:auto;text-align:center'>";
        echo "<div id='wrap'>";
        echo "<form action='index.php' method='POST'>";
    }

    public function echoPageFooter() {
        echo "</form>";
        echo "</div>";
        echo "</body>";
        echo "</html>";
    }

    public function echoPanelHeader() {
        echo "<div class='saPanel' style='width:800px;margin:auto'>";
        echo "<div>";
    }

    public function echoPanelFooter() {       
        echo "</div>";
        echo "</div>";
    }
}
?>