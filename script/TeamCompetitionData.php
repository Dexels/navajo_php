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
$selectedTeam = $teamsResult->getSelectedMessage($clubTeams, 'Update');

if ($selectedTeam != null) {

    $selectedPoolId = $teamsResult->getProperty('PoolId', $selectedTeam)->getAttribute('value');
    echo "<h3>Poulenummer: " . $selectedPoolId . "</h3>";

    $initRes = getNavajo('external/competition/InitCompetitionData');
    $p = $initRes->getAbsoluteProperty('PoolData/PoolId');
    $p->setAttribute('value', $selectedPoolId);

    # call pool setup or pool standings

    if (isset ($_REQUEST['PoolSetup'])) {
        $_SESSION['subPage'] = 'PoolSetup';
        $poolSetupResult = NavajoClient :: processNavajo('external/competition/ProcessGetPoolSetup', $initRes);

        $data = $poolSetupResult->getMessage('Teams');
        $layout = new CompetitionTableLayout(array (
            'TeamName',
            'ClubId',
            'TeamId'
        ));
        $layout->doRender('external/competition/ProcessGetPoolSetup', $data);
    }

    if (isset ($_REQUEST['PoolStandings'])) {
        $_SESSION['subPage'] = 'PoolStandings';
        $poolStandingsResult = NavajoClient :: processNavajo('external/competition/ProcessGetPoolStandings', $initRes);

        $data = $poolStandingsResult->getMessage('PoolStandings');
        $layout = new CompetitionTableLayout(array (
            'TeamName',
            'TotalMatches',
            'Won',
            'Draw',
            'Lost',
            'TotalPoints',
            'GoalsFor',
            'GoalsAgainst',
            'PenaltyPoints'
        ));
        $layout->doRender('external/competition/ProcessGetPoolStandings', $data);
    }

    if (isset ($_REQUEST['PoolSchedule'])) {
        $_SESSION['subPage'] = 'PoolSchedule';
        $poolScheduleResult = NavajoClient :: processNavajo('external/competition/ProcessGetPoolSchedule', $initRes);

        $p = $initRes->getAbsoluteProperty('PoolData/PoolId');
        $p->setAttribute('value', $selectedPoolId);

        $data = $poolScheduleResult->getMessage('PoolSchedule');
        $layout = new CompetitionTableLayout(array (
            'ExternalMatchId',
            'MatchDate',
            'MatchTime',
            'HomeTeamDescription',
            'AwayTeamDescription',
            'FacilityName',
            'FacilityField'
        ));
        $layout->doRender('external/competition/ProcessGetPoolSchedule', $data);
    }

    if (isset ($_REQUEST['PoolResults'])) {
        $_SESSION['subPage'] = 'PoolResults';
        $poolResultsResult = NavajoClient :: processNavajo('external/competition/ProcessGetPoolResults', $initRes);

        $data = $poolResultsResult->getMessage('PoolResults');

        $layout = new CompetitionTableLayout(array (
            'ExternalMatchId',
            'MatchDate',
            'HomeTeamName',
            'AwayTeamName',
            'HomeResult',
            'AwayResult'
        ));
        $layout->doRender('external/competition/ProcessGetPoolResults', $data);

    }

} else {
    echo "<div class='error'>U heeft geen team geselecteerd...</div>";
}
?>
</form>
</div>
</body>
</html>