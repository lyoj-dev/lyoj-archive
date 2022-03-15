<?php
    require_once "config.php";
    $conn=mysqli_connect($config["mysql"]["server"],$config["mysql"]["user"],
                         $config["mysql"]["passwd"],$config["mysql"]["database"]);
    if (!$conn) {
        echo "Failed to connect database! ".mysqli_error($conn);
        exit;
    }
    header("Content-Type:text/html;charset=utf-8");session_start();
    $result=mysqli_query($conn,"SELECT * FROM status WHERE id=".$_GET["id"]);
    $info=mysqli_fetch_assoc($result);
    if ($info["judged"]==false) {echo $info["status"];exit;} 
    echo json_decode($info["result"],true)["result"];
?>