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
<input type="hidden" name="serverCall" value="external/competition/InitCompetitionData:external/competition/ProcessGetClubTeams"/>
<input type="hidden" name="action" value="ProcessGetClubTeams"/>
