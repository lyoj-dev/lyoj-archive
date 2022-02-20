<?php
    require_once "../config.php";
    $conn=mysqli_connect($config["mysql"]["server"],$config["mysql"]["user"],
                         $config["mysql"]["passwd"],$config["mysql"]["database"]);
    if (!$conn) {
        echo "Failed to connect database! ".mysqli_error($conn);exit;
    }
    header("Content-Type:text/html;charset=utf-8");session_start();
    $json=array();mysqli_query($conn,"SET NAMES UTF8");
    $sql="SELECT * FROM problem";
    $result=mysqli_query($conn,$sql);
    if (!$result) {echo "Failed to query database! ".mysqli_error($conn);exit;}
    $line=0;while ($row=mysqli_fetch_assoc($result)) {
        $line++;if ($line<$_GET["l"]) continue;
        if ($line>$_GET["r"]) break;
        $json[]=$row;
    } echo json_encode($json,JSON_UNESCAPED_UNICODE);
?>