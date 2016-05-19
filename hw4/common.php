<?php

#Ishmeet Singh Malhotra




function newDB() {

    ini_set('display_errors', 1);
    error_reporting(E_ALL | E_STRICT);

    $dbunix_socket = '/ubc/icics/mss/ism1990/mysql/mysql.sock';
    $dbuser = 'ism1990';
    $dbpass = 'a87553146';
    $dbname = 'ism1990'; 
    try {
       $db = new PDO("mysql:localhost=host;dbname=$dbname", $dbuser, $dbpass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        header("HTTP/1.1 500 Server Error");
        die("HTTP/1.1 500 Server Error: Database Unavailable ({$e->getMessage()})");
    }
    return $db;
}

function openDB() {

    ini_set('display_errors', 1);
    error_reporting(E_ALL | E_STRICT);
	try {
   		$dbunixSocket = '/ubc/icics/mss/cics516/db/cur/mysql/mysql.sock';
   		$dbname = 'cics516';
 		$dbuser = 'cics516';
  		$dbpass = 'cics516password';
   		
    
   		$db = new PDO("mysql:unix_socket=$dbunixSocket;dbname=$dbname", $dbuser, $dbpass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        header("HTTP/1.1 500 Server Error");
        die("HTTP/1.1 500 Server Error: Database Unavailable ({$e->getMessage()})");
    }
    return $db;
}

function getMovieID($length){
	$digits = 1;
    $numbers = range(0,9);
    shuffle($numbers);
    for($i = 0;$i < $length;$i++)
       $digits .= $numbers[$i];
    return $digits;
}


function getActorID($firstname, $lastname, $db) {


    $rows = $db->prepare("SELECT id, first_name FROM actors 
    						WHERE last_name='$lastname'
    						AND first_name LIKE '$firstname%' 
    						ORDER BY film_count DESC, id ASC;");
    try {
        $rows->execute();
    } catch (PDOException $e) {
        print ("Error details: <?= $e->getMessage()?>)");
    }

    $firstRow = $rows->fetch();


    if ($rows->rowCount() != 0) {
        $id = $firstRow["id"];
        return $id;
    } else {

        print "Actor " . $firstname . " " . $lastname . " not found.";
        return -1;
    }
}


function getAllMovies($db, $id) {
    $id = $db->quote($id);

    $rows = $db->prepare(" SELECT name, year FROM actors, movies, roles 
    						WHERE $id = actors.id 
    						AND actors.id = roles.actor_id 
    						AND movies.id = roles.movie_id 
    						ORDER BY year DESC, name ASC;");

    try {
        $rows->execute();
    } catch (PDOException $e) {
        print ("Error details: <?= $e->getMessage()?>)");
    }
    return $rows;
}


function getCommonMovies($db, $id, $firstname, $lastname) {
    $id = $db->quote($id);


    $rows = $db->prepare("SELECT DISTINCT movies.name, movies.year 
    						FROM movies, actors AS a0, actors AS a1, roles AS r0, roles AS r1 
    						WHERE a0.id=$id 
    						AND a0.id = r0.actor_id 
    						AND a1.id = r1.actor_id 
    						AND movies.id = r0.movie_id  
    						AND a1.first_name='Kevin' 
    						AND a1.last_name= 'Bacon' 
    						ORDER BY year DESC, name ASC;");

    try {
        $rows->execute();
    } catch (PDOException $e) {
        print ("Error details: <?= $e->getMessage()?>)");
    }

    $rowCount = $rows->rowCount();
    if ($rowCount != 0) {
        return $rows;
    } else {

        print $firstname . " " . $lastname . " wasn't in any films with Kevin Bacon";
        return NULL;
    }
}


function getDirectorID($firstname, $lastname, $db) {

    $lastname = $db->quote($lastname);

    $firstname = $db->quote($firstname . '%');


    $sqlMatching = "SELECT id FROM directors "
            . "WHERE last_name=$lastname "
            . "AND first_name LIKE $firstname;";
    $rows = $db->prepare($sqlMatching);
    try {
        $rows->execute();
    } catch (PDOException $e) {
        print ("Error details: <?= $e->getMessage()?>)");
    }

    $firstRow = $rows->fetch();

    $rowCount = $rows->rowCount();

    if ($rowCount == 1) {
        $id = $firstRow["id"];
        return $id;
    } else {

        print "Director " . $firstnameOri . " " . $lastnameOri . " not found.";
        return -1;
    }
}



function printTable($db, $rows, $firstname, $lastname) {
    $lineNum = 1;
        ?>
        <table>
            <tr><th>#</th><th>Title</th><th>Year</th></tr>

            <?php foreach ($rows as $row) { ?>
                <tr>
                    <td><?php print $lineNum; ?></td>
                    <td><?php print $row["name"]; ?></td>
                    <td><?php print $row["year"]; ?></td>
                </tr>
                <?php
                $lineNum = $lineNum + 1;
            }
            ?>
        </table>
        <?php
    
}


function checkAllInputs($db, $movieName, $movieYear, $actorsFirstName, $actorsLastName, $directorsFirstName, $directorsLastName) {

    if ($movieName == null || $movieYear == null) {
        print "Please complete mandotory fields: movie name, year and genre.";
        return FALSE;
    } else {


        if ((!preg_match("/^[a-zA-Z]*/", $movieName)) || (!preg_match("/^(19\d\d|20[01][01234])$/", $movieYear))) {
            print "Movie info invalid, please try again.";
            return FALSE;
        }


        if ($actorsFirstName != "" || $actorsLastName != "") {
            $idActor = getActorID($actorsFirstName, $actorsLastName, $db);

            if ($idActor == -1) {
                exit(0);
            }
        }

        // use inputs directorsFirstName or directorsFirstName or both of them
        if ($directorsFirstName != "" || $directorsFirstName != "") {
            $idDirector = getDirectorID($directorsFirstName, $directorsLastName, $db);
            // director not in the db
            if ($idDirector == -1) {
                exit(0);
            }
        }
        return TRUE;
    }  
}

