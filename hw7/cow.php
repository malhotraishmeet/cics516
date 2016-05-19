<?php
session_save_path("/ubc/icics/mss/ism1990/public_html/php_tmp");
session_start();
if (isset($_SESSION["uName"]) && isset($_SESSION["uPass"])) {
    $arr = array("UserName" => $_SESSION["uName"], "Password" => $_SESSION["uPass"]);
} else {
    $arr = array("UserName" => null);
}
echo json_encode($arr);
?>
