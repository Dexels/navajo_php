<?php
if (isset($_GET['unioncode']) && isset($_GET['clubid']) && isset($_GET['pincode'])) {
    if(strtolower($_GET['unioncode']) == 'knvb') {
        $server = 'http://hera1.dexels.com/sportlink/' . strtolower($_GET['unioncode']) . '/servlet/Postman';
    } else {
        $server = 'http://hera3.dexels.com/sportlink/' . strtolower($_GET['unioncode']) . '/servlet/Postman';
    }
    startupNavajo($server, 'SLCASPUSER', 'l4mm3tj3');
    
    $initJoomla = NavajoClient :: callInitService('sportlinkservices/clubsite/InitCheckClubsiteURL');

    $n = getNavajo('sportlinkservices/clubsite/InitCheckClubsiteURL');
    $n->getAbsoluteProperty('ClubData/ClubId')->setValue($_GET['clubid']);
    $n->getAbsoluteProperty('ClubData/PinCode')->setValue($_GET['pincode']);
    $resultData = NavajoClient :: processNavajo('sportlinkservices/clubsite/ProcessCheckClubsiteURL', $n);
    $correctURL = $resultData->getAbsoluteProperty('Result/Ok')->getValue();
    
    if($correctURL == "true") {
    	$_SESSION['navajoServer'] = $server;
    	$_SESSION['unionCode'] = $_GET['unioncode'];
    	$_SESSION['navajoUsr'] = $_GET['clubid'];
    	$_SESSION['navajoPwd'] = $resultData->getAbsoluteProperty('Result/PinCode')->getValue();
    } else {
    	unset ( $_SESSION['navajoServer'] );
    	unset ( $_SESSION['unionCode'] );
    	unset ( $_SESSION['navajoUsr'] );
    	unset ( $_SESSION['navajoPwd'] );
    }
}
?>
