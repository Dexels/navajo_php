<?php

	require_once "NavajoPhpClient.php";
	session_start();
	
	startupNavajo('http://ficus:3000/sportlink/knvb/servlet/Postman','ROOT','');
	NavajoClient::callInitService('club/InitUpdateClub');
	
	
?>

<!--  do graphic stuff -->

<!-- show property: -->

<form action="NavajoHandler.php" method="post">

<?php  	NavajoPhpClient::showAbsoluteProperty('club/InitUpdateClub','Club/ClubIdentifier'); ?>

<input type="submit" value="Do something"/>
<input type="hidden" name="action" value="processNavajo"/>
<input type="hidden" name="serverCall" value="club/InitUpdateClub:club/ProcessQueryClub;club/InitUpdateClub:club/ProcessGetClubActivities"/>
<input type="hidden" name="next" value="ProcessQueryClub.php"/>

</form>
