
<?php  	
	require_once "navajo/AdvancedTableLayout.php";
	$teamsResult = getNavajo('external/competition/ProcessGetClubTeams');
	
	$clubTeams = $teamsResult->getMessage('Teams');
	$layout    = new AdvancedTableLayout(array('Update', 'TeamName','SportDescription','CompetitionTypeName', 'ClassName', 'PoolName'));
	$layout->doRender('external/competition/ProcessGetClubTeams', $clubTeams);
	
?> 
<input type="submit" name="PoolSetup" value="Indeling"/>
<input type="submit" name="PoolSchedule" value="Programma"/>
<input type="submit" name="PoolResults" value="Uitslagen"/>
<input type="submit" name="PoolStandings" value="Stand"/>
<input type="hidden" name="action" value="TeamCompetitionData"/>
