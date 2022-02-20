<?php
    require_once "../../config.php";
    require_once "../function.php";
    global $config;
    for ($i=0;$i<count($config["require_code"]);$i++) 
        require_once("../../".$config["require_code"][$i]);
?>