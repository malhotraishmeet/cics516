<?php

#Ishmeet Singh Malhotra

include("top.html");
include("common.php");

//get database
$db = openDB();

// check if the first form is set
if (isset($_REQUEST['all_Movies'])) {

//get inputs from the form
    $firstname = $_REQUEST['firstname'];
    $lastname = $_REQUEST['lastname'];

// get actor id
    $id = getActorID($firstname, $lastname, $db);
    ?>

    <h1>Results for <?php print $firstname . " " . $lastname ?></h1>

    <?php
// when the given actor is in the db
    $moviesRows = getAllMovies($db, $id);
    if ($moviesRows != NULL) {
            printTable($db, $moviesRows, $firstname, $lastname);
        }
}

include("bottom.html");
?>