<?php 
# this example should be called via index.php?action=example3
# this script can call itself to search for a club by city

if (!isset($_POST['formsubmit'])) { # display search form

    echo "<h2>Example 3</h2>";
    echo "<p>* e.g. try searching for &quot;Sparta&quot;</p>";
    $init = NavajoClient :: callInitService('club/InitSearchClubs');
    NavajoPhpClient :: showAbsoluteProperty('club/InitSearchClubs', 'ClubSearch/SearchName', '', 0, 1, '');

    # the "Search" (submit) button: the name and id should be formsubmit
    echo "<input type='submit' name='formsubmit' id='formsubmit' value='Search' />";

    # the script you want to include next, in our case it's this script
    echo "<input type='hidden' name='action' value='example3' />";

    # the WS you want to call when submitting; the pattern is <input WS>:<output WS>
    # NB: multiple calls can be seperated by a ";" 
    # NB: note the "Search:" construction. The name before the server call should be the value of the submit button 
    # this way we can have multiple actions in one page
    echo "<input type='hidden' name='Search:serverCall' value='club/InitSearchClubs:club/ProcessSearchClubs' />";

} else { # display search result
    # the NavajoHandler has already called the search WS using the required input. 
    # Use one of the other helper NavajoPhpClient functions here showTable():

    $columns       = explode(',', 'ClubIdentifier,ClubName,ClubCity');
    $columnWidths  = explode(',', '100,200,200');

    NavajoPhpClient :: showTable('club/ProcessSearchClubs', 'Club', $columns, $columnWidths, '', '', '');
}
?>
