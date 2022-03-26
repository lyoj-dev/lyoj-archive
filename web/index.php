<?php 
    error_reporting(E_ERROR);
    ini_set("display_errors","Off");
    require_once "./cores/controllers/error.php";
    require_once "./function.php";
    Application::run($_GET);
?>
