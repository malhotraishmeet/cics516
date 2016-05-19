<?php
session_save_path("/ubc/icics/mss/ism1990/public_html/php_tmp");
session_start();


$user = $_POST["user"];
$psw = $_POST["password"];

if ($user == "testuser" && $psw == "testpass") {
    $resp = array("resp" => "OK");

    $_SESSION["uName"] = $user;
    $_SESSION["uPsw"] = $psw;
    echo json_encode($resp);
} else {
    $resp = array("resp" => "ERROR");
    echo json_encode($resp);
}
?>