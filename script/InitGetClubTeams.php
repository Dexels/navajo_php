<?php

	session_start();
	require_once "NavajoPhpClient.php";
	
	startupNavajo('http://slwebsvracc.sportlink.enovation.net/sportlink/knvb/servlet/Postman','ROOT','R20T');
	NavajoClient::callInitService('external/competition/InitCompetitionData');
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" dir="ltr" >
<head>
  <title>Sportlink Club Site</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta name="description" content="sportlink club site">
  <meta name="robots" content="index,follow"> 
  <link rel="stylesheet" type="text/css" href="navajo.css" />
</head>
<body>
<div class="navajo">
<form action="NavajoHandler.php" method="post">

<?php  	
	NavajoPhpClient::showAbsoluteProperty('external/competition/InitCompetitionData','ClubData/ClubId', false, true, '_fixed');
	echo "<br/>";	
	NavajoPhpClient::showAbsoluteProperty('external/competition/InitCompetitionData','PoolData/SportId', false, true, '_fixed');
	echo "<br/>";
	NavajoPhpClient::showAbsoluteProperty('external/competition/InitCompetitionData','PoolData/CompetitionKind', false, true, '_fixed');
	echo "<br/>";
	NavajoPhpClient::showAbsoluteProperty('external/competition/InitCompetitionData','PoolData/AgeClassCode', false, true, '_fixed');
	echo "<br/>";
	NavajoPhpClient::showAbsoluteProperty('external/competition/InitCompetitionData','PoolData/TeamSex', false, true, '_fixed');
	echo "<br/>";
?>

<input type="submit" value="ophalen teams"/>
<input type="hidden" name="action" value="processNavajo"/>
<input type="hidden" name="serverCall" value="external/competition/InitCompetitionData:external/competition/ProcessGetClubTeams"/>
<input type="hidden" name="next" value="ProcessGetClubTeams.php"/>
</div>
</body>
</html>