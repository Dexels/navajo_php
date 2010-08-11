<?php 

# this example should be called via index.php?action=example2
# the index.php file includes the necessary libraries and in our case echos a header and footer

$my_club = 'BBKY84H';

$init = NavajoClient :: callInitService('club/InitUpdateClub');
$init->getAbsoluteProperty('Club/ClubIdentifier')->setValue($my_club);

$result = NavajoClient :: processNavajo('club/ProcessQueryClub', $init);

echo "<h2>Example 2</h2>";
echo "<h3>Clubname is : " . $result->getAbsoluteProperty('ClubData/ClubName')->getValue() . "</h3>";

?>
