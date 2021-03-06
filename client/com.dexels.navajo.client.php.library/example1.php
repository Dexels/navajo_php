<?php 

# This is a standalone script, it includes all necessary libraries, calls a WS, sets a param and calls another
# A session is needed to store web service variables

@session_start();
require_once('./navajo/StartUpServer.php');

$init = NavajoClient :: callInitService('club/InitUpdateClub');
$init->getAbsoluteProperty('Club/ClubIdentifier')->setValue('BBKY84H');

$result = NavajoClient :: processNavajo('club/ProcessQueryClub', $init);

# Use this to dump the contents of a specific web service:
# echo $result->printXml();

echo "<h2>Example 1</h2>";

# Use one of the functions in NavajoPhpClient to show a property directly
NavajoPhpClient :: showAbsoluteProperty('club/ProcessQueryClub', 'ClubData/ClubName'); 


?>
