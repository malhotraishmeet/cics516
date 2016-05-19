<?php

#Ishmeet Singh Malhotra


include("top.html");
include("common.php");

//get database
$db = openDB();

// check if the second form is set
if (isset($_REQUEST['moviesWithBacon'])) {
//get inputs from the form
    $firstname = htmlspecialchars($_REQUEST['firstname']);
    $lastname = htmlspecialchars($_REQUEST['lastname']);

    // get actor id
    $id = getActorID($firstname, $lastname, $db);
    ?>

    <h1>Results for <?php echo $firstname." ".$lastname; ?> and Kevin Bacon</h1>

    <?php

        $moviesRows = getCommonMovies($db, $id, $firstname, $lastname);
        
        if ($moviesRows != NULL) {
            printTable($db, $moviesRows, $firstname, $lastname);
        }
    
}
// end of isset
include("bottom.html");
?>