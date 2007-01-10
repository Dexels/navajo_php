<?php
	require_once "NavajoPhpClient.php";
	require_once "TableLayout.php";
	session_start();
	
?>

<!--  do graphic stuff -->

<!-- show property: -->

<form action="NavajoHandler.php" method="post">

<?php  	NavajoPhpClient::showAbsoluteProperty('club/ProcessQueryClub','ClubData/ClubName'); ?> <br/>
<?php  	NavajoPhpClient::showAbsoluteProperty('club/ProcessQueryClub','ClubData/LegalForm'); ?><br/>
<?php  	NavajoPhpClient::showAbsoluteProperty('club/ProcessQueryClub','ClubData/EstablishedDate'); ?><br/>
<?php  	NavajoPhpClient::showAbsoluteProperty('club/ProcessQueryClub','ClubData/StreetName'); ?><br/>
<?php  	NavajoPhpClient::showAbsoluteProperty('club/ProcessQueryClub','ClubData/AddressNumber'); ?><br/><br/><br/><br/>
<?php  	NavajoPhpClient::showAbsoluteProperty('club/ProcessQueryClub','ClubData/TelephoneData'); ?><br/>
<?php  	NavajoPhpClient::showAbsoluteProperty('club/ProcessQueryClub','ClubData/ZipCode'); ?><br/>

<hr/>

<?php
	$nn = getNavajo('club/ProcessGetClubActivities');
	
	$mm = $nn->getMessage('NewClubActivities');
	$l = new TableLayout(array('ActivityGameTypeCode','Active','ActivityGameTypeDescription'));
	$l->doRender('club/ProcessGetClubActivities',$mm);

	?>


<input type="submit" value="Do something else"/>
<input type="hidden" name="action" value="processNavajo"/>
<input type="hidden" name="currentNavajo" value="club/ProcessQueryClub"/>
<input type="hidden" name="source" value="club/ProcessQueryClub"/>
<input type="hidden" name="script" value="club/ProcessUpdateClub"/>
<input type="hidden" name="next" value="ProcessQueryClub.php"/>
</form>

<form action="NavajoHandler.php" method="post">
<input type="hidden" name="action" value="processNavajo"/>
<input type="hidden" name="source" value="club/InitUpdateClub"/>
<input type="hidden" name="script" value="club/ProcessQueryClub"/>
<input type="hidden" name="next" value="ProcessQueryClub.php"/>
<input type="submit" value="reload"/>
</form>
