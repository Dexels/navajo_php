<?php
	require_once "NavajoPhpClient.php";
	require_once "CompetitionTableLayout.php";
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
	$teamsResult = getNavajo('external/competition/ProcessGetClubTeams');
	
	$clubTeams = $teamsResult->getMessage('Teams');
	$layout    = new CompetitionTableLayout(array('Update', 'TeamName','SportDescription','CompetitionTypeName', 'ClassName', 'PoolName'));
	$layout->doRender('external/competition/ProcessGetClubTeams', $clubTeams);
	
?>
<input type="submit" name="PoolSetup" value="Indeling"/>
<input type="submit" name="PoolSchedule" value="Programma"/>
<input type="submit" name="PoolResults" value="Uitslagen"/>
<input type="submit" name="PoolStandings" value="Stand"/>
<input type="hidden" name="action" value="processNavajo"/>
<input type="hidden" name="next" value="TeamCompetitionData.php"/>
</form>
</div>
</body>
</html>