<?php
session_save_path("/ubc/icics/mss/ism1990/public_html/php_tmp");
session_start();


unset($_SESSION["uName"]);
unset($_SESSION["uPsw"]);

session_destroy();
session_regenerate_id(TRUE);  
?>
