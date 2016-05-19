<?php

#Ishmeet Singh Malhotra

include("top.html");
include("common.php");

//get database
$db = newDB();

// check if the second form is set
if (isset($_REQUEST['newMovie'])) {
    //get inputs from the form
    $movieName = htmlspecialchars($_REQUEST['movieName']);
    $movieYear = htmlspecialchars($_REQUEST['movieYear']);
    $actorsFirstName = htmlspecialchars($_REQUEST['actorsFirstName']);
    $actorsLastName = htmlspecialchars($_REQUEST['actorsLastName']);
    $directorsFirstName = htmlspecialchars($_REQUEST['directorsFirstName']);
    $directorsLastName = htmlspecialchars($_REQUEST['directorsLastName']);
    $movieGenre = htmlspecialchars($_REQUEST['movieGenre']);

// when user inputs are valid    
    if (checkAllInputs($db, $movieName, $movieYear, $actorsFirstName, $actorsLastName, $directorsFirstName, $directorsLastName)) {
        try {
            // get largest movie id number to prepare insertion
            $maxMovieID = getMovieID(5);

            $stmt = $db->prepare("INSERT INTO movies (id, name, year) VALUES (:id, :movieName, :movieYear)");
            $stmt->bindParam(":id", $maxMovieID);
            $stmt->bindParam(":movieName", $movieName);
            $stmt->bindParam(":movieYear", $movieYear);
            $stmt->execute();



            // update table role
            if ($actorsFirstName != "" && $actorsLastName != "") {
                
                $actorID = getActorID($actorsFirstName, $actorsLastName, $db);

                $stmt = $db->prepare("INSERT INTO roles (actor_id, movie_id) VALUES (:actorID, :movie_id)");
                $stmt->bindParam(":actorID", $actorID);
                $stmt->bindParam(":movie_id", $maxMovieID);
                $stmt->execute();

                // update actor role's film_count column
                $stmt = $db->prepare("UPDATE actors SET film_count=film_count +1 WHERE id = :actorID");
                $stmt->bindParam(":actorID", $actorID);
                $stmt->execute();
            }

            // update table movies_directors
            if ($directorsFirstName != "" && $directorsFirstName != "") {
                $directorID = getDirectorID($directorsFirstName, $directorsLastName, $db);
                $stmt = $db->prepare("INSERT INTO movies_directors (director_id, movie_id) VALUES (:directorID, :movie_id)");
                $stmt->bindParam(":directorID", $directorID);
                $stmt->bindParam(":movie_id", $maxMovieID);
                $stmt->execute();
            }
            
        } catch (PDOException $e) {
            die("Error: {$e->getMessage()}");
        }
        print "Movie: " . $movieName . ", " . $movieYear . " added successfully.";
    }
}
?>
<?php include("bottom.html"); ?>