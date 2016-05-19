<?php
session_save_path("/ubc/icics/mss/ism1990/public_html/php_tmp");
session_start();

if (isset($_SESSION["uName"])) {
    
    $filename = "list_" . $_SESSION["uName"] . ".json";

    if (file_exists($filename)) {
        $text = file_get_contents($filename);
        echo $text;
    } else {
  
        $initialText = '{ "items": [ ] }';
        file_put_contents($filename, $initialText);
        echo "File created.";
    }
}
?>