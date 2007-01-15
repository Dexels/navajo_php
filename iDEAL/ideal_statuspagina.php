<?php
$_SESSION['site']->echoPageHeader();
$_SESSION['site']->echoPanelHeader();

if (isset ($_GET['trxid']) && isset($_SESSION['invoiceId'])) {

    require_once ("./include/ThinMPI.php");
    require_once ("./include/AcquirerStatusRequest.php");

    $data = & new AcquirerStatusRequest();
    $transID = $_GET['trxid'];
    $transID = str_pad($transID, 16, "0");
    $data->setTransactionID($transID);

    $rule = new ThinMPI();
    $result = $rule->ProcessRequest($data);

    if (!$result->isOK()) {
        print ("<p class='error'>Status kon niet worden opgehaald, klik <a href='' onclick='refresh()'>hier</a> om het nogmaals te proberen.</p>");
        print ("<p class='error'>De iDEAL foutmelding is als volgt: ");
        print ($result->getErrorMessage());
        print ("</p>");
    } else
        if (!$result->isAuthenticated()) {        	
            print ("<p class='error'><b>Fout opgetreden: </b><br/><br/>Uw betaling is afgebroken. Als dit per ongeluk is, ga dan terug naar de betaalpagina en probeer het nog eens. Indien een probleem zich blijft voordoen, neem dan contact op met de helpdesk van Sportlink Services.</p>");
        } else
            if (!isset ($_SESSION['isPayed'])) {

                # update the transaction in the db and set invoice payment
                startupNavajo($_SESSION['postMan'], '#' . $_SESSION['clubId'], $_SESSION['pinCode']);
                NavajoClient :: callInitService('vla/financial/InitUpdateInvoice');
                $n = getNavajo('vla/financial/InitUpdateInvoice');
                $p = $n->getAbsoluteProperty('Invoice/InvoiceId');
                $p->setAttribute('value', $_SESSION['invoiceId']);
                $p = $n->getAbsoluteProperty('Club/ClubIdentifier');
                $p->setAttribute('value', $_SESSION['clubId']);
                $p = $n->getAbsoluteProperty('Club/DbSchema');
                $p->setAttribute('value', $_SESSION['dbSchema']);
                $p = $n->getAbsoluteProperty('Club/DbPassword');
                $p->setAttribute('value', $_SESSION['dbPassword']);
                $invoiceData = NavajoClient :: processNavajo('vla/financial/ProcessQueryInvoice', $n);

                NavajoClient :: processNavajo('vla/financial/ProcessUpdateInvoicePayment', $invoiceData);

                echo "<div class='saPanel' style='width:800px;margin:auto'>";
                echo "<div>";
                echo "<table class='saTable' cellpadding='0' cellspacing='0' border='0'>";
                echo "<tr>";
                echo "<td style='background-color:#f9ffb8;width:200px'>";
                echo "<label style='font-size:16pt;'>DIGITALE</label> <label style='font-size:16pt;color:grey'>NOTA</label><br/>";
                echo "<label style='font-size:10pt;color:green;font-weight:bold;'>BETALING GESLAAGD</label>";
                echo "</td>";
                echo "<td style='background-color:#ffffff;width:170px'>";
                echo "<img src='./icons/sportlink_logo.png'></img>";
                echo "</td>";
                echo "<td style='background-color:#f9ffb8;font-weight:bold;vertical-align:top;width:100px;'>";
                echo "Begunstigde<br/><br/>";
                echo "Bedrag<br/><br/>";
                echo "Debiteur<br/><br/>";
                echo "Kenmerk<br/><br/>";
                echo "Omschrijving<br/><br/>";
                echo "</td>";
                echo "<td style='background-color:#f9ffb8;font-weight:bold;width:200px;color:grey'>";
                echo $_SESSION['organizationName'] . "<br/><br/>";
                echo "&euro;" . $_SESSION['invoiceAmount'] . "<br/><br/>";
                echo $_SESSION['fullName'] . "<br/><br/>";
                echo $_SESSION['invoiceId'] . "<br/><br/>";
                echo $_SESSION['invoiceDescription'] . "<br/><br/>";
                echo "</td>";
                echo "<td style='background-color:#ffffff;width:100px'>";
                echo "<td style='background-color:#ffffff;width:100px'>";
                echo "<img src='./icons/ideal_logo.png' border='0'></img>";
                echo "</td>";
                echo "</tr>";
                echo "</table>";
                echo "</div>";
                echo "</div>";
                echo "<div class='saPanel' style='width:800px'>";
                echo "<div>";
                echo "<table class='saTable' cellpadding='0' cellspacing='0' border='0'>";
                echo "<tr>";
                echo "<td style='background-color:#ffffff'>";
                echo "<p>Uw betaling is succesvol afgerond en verwerkt in de administratie van Sportlink Club.</p>";
                echo "<p>Klik <a href='javascript:window.close()'>hier</a> om dit scherm te sluiten.</p>";
                unset($_SESSION['invoiceId']);         
                echo "</td>";
                echo "</tr>";
                echo "</table>";
                echo "</div>";
                echo "</div>";
            } else {
                print ("<p class='error'>Uw factuur is al betaald. U kunt dit scherm sluiten.</p>");
            }
} else {
    print ("<p class='error'>Deze pagina kan slechts eenmaal en alleen via de bank worden aangeroepen.</p>");
}
$_SESSION['site']->echoPanelFooter();
$_SESSION['site']->echoPageFooter();
?>
