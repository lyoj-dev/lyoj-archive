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
    $json=json_decode(trim($_POST["json"]),true);
    $json["title"]=str_replace("\\","\\\\",$json["title"]);
    $json["background"]=str_replace("\\","\\\\",$json["background"]);
    $json["description"]=str_replace("\\","\\\\",$json["description"]);
    $json["input-desc"]=str_replace("\\","\\\\",$json["input-desc"]);
    $json["output-desc"]=str_replace("\\","\\\\",$json["output-desc"]);
    $json["cases"]=str_replace("\\","\\\\",$json["cases"]);
    $json["hint"]=str_replace("\\","\\\\",$json["hint"]);
    $json["title"]=str_replace("'","\\'",$json["title"]);
    $json["background"]=str_replace("'","\\'",$json["background"]);
    $json["description"]=str_replace("'","\\'",$json["description"]);
    $json["input-desc"]=str_replace("'","\\'",$json["input-desc"]);
    $json["output-desc"]=str_replace("'","\\'",$json["output-desc"]);
    $json["cases"]=str_replace("'","\\'",$json["cases"]);
    $json["hint"]=str_replace("'","\\'",$json["hint"]);
    $sql = "UPDATE problem SET name='".$json["title"]."' , bg='".$json["background"]."'
        , descrip='".$json["description"]."' , input='".$json["input-desc"]."'
        , output='".$json["output-desc"]."' , cases='".$json["cases"]."'
        , hint='".$json["hint"]."' WHERE id=$id";
    if (!$conn->query($sql)) echo mysqli_error($conn);
    $arr=array(
        "input"=>$json["input"],
        "output"=>$json["output"],
        "spj"=>$json["spj"],
        "data"=>$json["data"]
    );
    $fp=fopen("../../../../problem/$id/config.json","w");
    fwrite($fp,json_encode($arr,JSON_UNESCAPED_UNICODE));
    echo "Update Successfully! pid: $id";
?>