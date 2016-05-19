<?php
//session_save_path("/ubc/icics/mss/ism1990/public_html/php_tmp");
//session_start();

$jsonStr = $_POST["jsonString"];

if (isset($_SESSION["uName"])) {

    $filename = "list_" . $_SESSION["uName"] . ".json";
  
    file_put_contents($filename, $jsonStr);
    echo "OK";
} else {
    echo "ERROR";
}
?>
