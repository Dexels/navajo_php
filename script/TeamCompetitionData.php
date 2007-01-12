<?php

	require_once "navajo/AdvancedTableLayout.php";

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
        $layout = new AdvancedTableLayout(array (
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
        $layout = new AdvancedTableLayout(array (
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
        $layout = new AdvancedTableLayout(array (
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

        $layout = new AdvancedTableLayout(array (
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
