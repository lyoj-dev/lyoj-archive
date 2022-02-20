<meta charset="UTF-8">
<?php
    require_once "../config.php";
    $conn = mysqli_connect(
        $config["mysql"]["server"],
        $config["mysql"]["user"],
        $config["mysql"]["passwd"],
        $config["mysql"]["database"]
    );
    if (!$conn) exit;
    header("Content-Type:text/html;charset=utf-8");$id=$_GET["id"];
    session_start(); mysqli_query($conn, "SET NAMES UTF8");
    $sql = "SELECT * FROM problem WHERE id=$id";
    $result = mysqli_query($conn, $sql);
    $myfile = fopen("../../../../problem/$id/config.json", "r") or die("Unable to open file!");
    $json = fread($myfile,filesize("../../../../problem/$id/config.json"));
    $json = json_decode($json,true); 
    $row = mysqli_fetch_assoc($result);
    $json["title"] = $row["name"];
    $json["background"] = $row["bg"];
    $json["description"] = $row["descrip"];
    $json["input-desc"] = $row["input"];
    $json["output-desc"] = $row["output"];
    $json["cases"] = $row["cases"];
    $json["hint"] = $row["hint"];
    echo json_encode($json,JSON_UNESCAPED_UNICODE);
?>