<?php
if (!isset ($_POST['submit'])) {
    $_SESSION['site']->echoPanelHeader();
    if (isset ($_GET['clubid']) && isset ($_GET['personid']) && isset ($_GET['invoiceid'])) {

        $_SESSION['clubId'] = $_GET['clubid'];
        $_SESSION['personId'] = $_GET['personid'];
        $_SESSION['invoiceId'] = $_GET['invoiceid'];

        # get club subscription data; set clubid

        $n = getNavajo('clubasp/InitUpdateClubSubscription');
        $p = $n->getAbsoluteProperty('ClubSubscription/OrganizationId');
        $p->setAttribute('value', $_SESSION['clubId']);

        $subscriptionData = NavajoClient :: processNavajo('clubasp/ProcessQueryClubSubscription', $n);

        $_SESSION['unionCode'] = $subscriptionData->getAbsoluteProperty('ClubSubscription/UnionCode')->getAttribute('value');
        $_SESSION['postMan'] = 'http://' . $subscriptionData->getAbsoluteProperty('ClubSubscription/Postman')->getAttribute('value');
        $_SESSION['pinCode'] = $subscriptionData->getAbsoluteProperty('ClubSubscription/PinCode')->getAttribute('value');
        $_SESSION['dbSchema'] = $subscriptionData->getAbsoluteProperty('ClubSubscription/DbSchema')->getAttribute('value');
        $_SESSION['dbPassword'] = $subscriptionData->getAbsoluteProperty('ClubSubscription/DbPassword')->getAttribute('value');
        $_SESSION['idealMerchantId'] = $subscriptionData->getAbsoluteProperty('ClubSubscription/IdealMerchantId')->getAttribute('value');
        $_SESSION['idealPassword'] = $subscriptionData->getAbsoluteProperty('ClubSubscription/IdealPassword')->getAttribute('value');

        # display invoice data         

        startupNavajo($_SESSION['postMan'], '#' . $_SESSION['clubId'], $_SESSION['pinCode']);
        NavajoClient :: callInitService('vla/financial/InitUpdateInvoice');

        $n = getNavajo('vla/financial/InitUpdateInvoice');
        $p = $n->getAbsoluteProperty('Invoice/InvoiceId');
        $p->setAttribute('value', $_SESSION['invoiceId']);
        $p = $n->getAbsoluteProperty('Invoice/DebtorId');
        $p->setAttribute('value', $_SESSION['personId']);
        $p = $n->getAbsoluteProperty('Club/ClubIdentifier');
        $p->setAttribute('value', $_SESSION['clubId']);
        $p = $n->getAbsoluteProperty('Club/DbSchema');
        $p->setAttribute('value', $_SESSION['dbSchema']);
        $p = $n->getAbsoluteProperty('Club/DbPassword');
        $p->setAttribute('value', $_SESSION['dbPassword']);

        $invoiceData = NavajoClient :: processNavajo('vla/financial/ProcessQueryInvoice', $n);

        # if an invoice was found
        if (get_class($invoiceData->getMessage('Invoice')) == 'DOMElement') {

            # if it hasn't been payed
            $invoicePaymentDate = $invoiceData->getAbsoluteProperty('Invoice/PaymentDate')->getAttribute('value');
            if ($invoicePaymentDate == null) {

                $_SESSION['organizationName'] = $invoiceData->getAbsoluteProperty('Club/OrganizationName')->getAttribute('value');
                $_SESSION['invoiceAmount'] = $invoiceData->getAbsoluteProperty('Invoice/InvoiceAmount')->getAttribute('value');
                $_SESSION['fullName'] = htmlspecialchars($invoiceData->getAbsoluteProperty('Invoice/FullName')->getAttribute('value'));
                $_SESSION['invoiceDescription'] = $invoiceData->getAbsoluteProperty('Invoice/Description')->getAttribute('value');

                echo "<table class='saTable' cellpadding='0' cellspacing='0' border='0'>";
                echo "<tr>";
                echo "<td style='background-color:#f9ffb8;width:200px'>";
                echo "<label style='font-size:16pt;'>DIGITALE</label> <label style='font-size:16pt;color:grey'>NOTA</label><br/>";
                echo "<label style='font-size:10pt;color:green;font-weight:bold;'>BETAALPAGINA</label>";
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
                echo "<img src='./icons/ideal_logo.png' border='0'></img>";
                echo "</td>";
                echo "</tr>";
                echo "</table>";

                # do a directory request and show a list of issuers

                require_once ("./include/ThinMPI.php");
                require_once ("./include/DirectoryRequest.php");
                require_once ("./include/DirectoryResponse.php");

                $data = & new DirectoryRequest();
                $rule = new ThinMPI();
                $data->setMerchantID = $_SESSION['idealMerchantId'];
                $result = $rule->ProcessRequest($data);

                if (!$result->isOK()) {
                    print ("<p class='error'>Er is op dit moment geen betaling met iDEAL mogelijk.</p>");
                    print ("<br />De iDEAL foutmelding is als volgt: ");
                    print ($result->getErrorMessage());
                    print ("<br />");
                } else {
                    $issuerArray = $result->getIssuerList();
                    if (count($issuerArray) == 0) {
                        print ("<p class='error'>Lijst met banken niet beschikbaar, er is op dit moment geen betaling met iDEAL mogelijk.</p>");
                    } else {
                        for ($i = 0; $i < count($issuerArray); $i++) {
                            if ($issuerArray[$i]->issuerList == "Short") {
                                $issuerArrayShort[] = $issuerArray[$i];
                            } else {
                                $issuerArrayLong[] = $issuerArray[$i];
                            }
                        }
                    }

                    $_SESSION['site']->echoPanelFooter();
                    $_SESSION['site']->echoPanelHeader();
                    echo "<table class='saTable' cellpadding='0' cellspacing='0' border='0'>\n";
                    echo "<tr>";
                    echo "<td style='background-color:#ffffff;width:366px'>";
                    echo "<form method='POST' action='' name='iDEAL payment' style='padding-top:20px;padding-left:80px;'>\n";
                    echo "<select name='issuerID' style='width:150px;'>";
                    echo "<option value='0'>kies uw bank...</option>";
                    for ($i = 0; $i < count($issuerArrayShort); $i++) {
                        echo ("<option value='{$issuerArrayShort[$i]->issuerID}'> {$issuerArrayShort[$i]->issuerName} </option>");
                    }
                    if (count($issuerArrayLong) > 0) {
                        echo "<option value='0'>---overige banken---</option>";
                    }

                    for ($i = 0; $i < count($issuerArrayLong); $i++) {
                        echo "<option value='{$issuerArrayLong[$i]->issuerID}'> {$issuerArrayLong[$i]->issuerName} </option>";
                    }
                    echo "</select>&nbsp;";
                    echo "<input type='submit' class='input' name='submit' value='Betaal Nu' />";
                    echo "<input type='hidden' name='redirect' value='true' />";
                    echo "</form>";
                    echo "</td>";
                    echo "<td style='background-color:#ffffff;width:400px'>";
                    echo "<label style='font-size:16pt;'>TIP</label>";
                    echo "<p>Kies in het overzicht links uw bank en druk op de &quot;Betaal Nu&quot; ";
                    echo "button.</p>";
                    echo "<p>U wordt vervolgens doorgeleid naar uw vertrouwde bankomgeving. ";
                    echo "Na het afhandelen van uw betaling komt u weer op deze pagina terecht.";
                    echo "U ziet dan direct de status van uw betaling terug.</p><br />";
                    echo "</td>";
                    echo "</tr>";
                    echo "</table>\n";
                    $_SESSION['aap'] = 'noot';
                }
            } else {
                print "<p class='error'>Geen betaling mogelijk, omdat deze factuur op " . date('d-m-Y', strtotime($invoicePaymentDate)) . " al is betaald.</p>";
            }
        } else
            print "<p class='error'>Combinatie club, factuurnummer en debiteur levert geen openstaande factuur op.</p>";
    } else {
        print ("<p class='error'>Geen vereniging, factuurnummer en/of debiteur bekend!</p>");
    }
    $_SESSION['site']->echoPanelFooter();
} else {
    # 
    # Transaction request: get a transactionid from the issuer and redirect to payment site
    # 

    require_once ("./include/ThinMPI.php");
    require_once ("./include/AcquirerTrxRequest.php");

    if (isset ($_POST['issuerID'])) {

        # create and store iDEAL transaction		

        $data = & new AcquirerTrxRequest();
        $data->setMerchantID = $_SESSION['idealMerchantId'];
        $data->setIssuerID($_POST['issuerID']);
        $data->setPurchaseID($_SESSION['invoiceId']);
        $data->setEntranceCode($_SESSION['personId']);
#        $data->setAmount($_SESSION['invoiceAmount'] * 100);
        $data->setAmount(4200);

        $rule = new ThinMPI();
        $result = new AcquirerTrxResponse();
        $result = $rule->ProcessRequest($data);

        if ($result->isOK()) {
            $transactionID = $result->getTransactionID();
            $_SESSION['transactionId'] = $transactionID;
            $IssuerUrl = $result->getIssuerAuthenticationURL();
            $IssuerUrl = html_entity_decode($IssuerUrl);           
            header("Location: $IssuerUrl");
            exit ();
        } else {
            $_SESSION['site']->echoPageHeader();
            $_SESSION['site']->echoPanelHeader();
            print ("<p class='error'>Er is iets misgegaan bij het opvragen van de bankgegevens...</p>");
            print ("<br />De iDEAL foutmelding is als volgt: ");
            print ($result->getErrorMessage());
            $_SESSION['site']->echoPanelFooter();
            $_SESSION['site']->echoPageFooter();
        }
    } else {
        $_SESSION['site']->echoPageHeader();
        $_SESSION['site']->echoPanelHeader();
        print ("<p class='error'>Geen bank opgegeven. Ga terug en probeer het nog eens.</p>");
        $_SESSION['site']->echoPanelFooter();
        $_SESSION['site']->echoPageFooter();
    }
}
?>

