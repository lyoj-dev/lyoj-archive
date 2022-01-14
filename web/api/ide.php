<?php
    require_once "config.php";
    $conn=mysqli_connect($config["mysql"]["server"],$config["mysql"]["user"],
                         $config["mysql"]["passwd"],$config["mysql"]["database"]);
    if (!$conn) {
        echo "Failed to connect database! ".mysqli_error($conn);
        exit;
    }
    header("Content-Type:text/html;charset=utf-8");session_start();
    if ($_SERVER['REQUEST_METHOD']!='POST') {echo "Failed to parse data!";exit;}
    $code=trim($_POST["code"]);$uid=trim($_POST["uid"]);
    $lang=trim($_POST["lang"]);$time=trim($_POST["time"]);
    $memory=trim($_POST["memory"]);$input=trim($_POST["input"]);
    $array=array("input"=>$input,"t"=>intval($time),"m"=>intval($memory)*1024);
    $code=str_replace("'","\\'",str_replace("\\","\\\\",$code));
    $result=mysqli_query($conn,"SELECT * FROM status");
    if (!$result) {echo "Failed to query database! ".mysqli_error($conn);exit;}
    $sum=mysqli_num_rows($result);
    $result=mysqli_query($conn,"SELECT * FROM waited_judge");
    if (!$result) {echo "Failed to query database! ".mysqli_error($conn);exit;}
    $sum+=mysqli_num_rows($result);
    $sql="INSERT INTO waited_judge (id,uid,pid,lang,code,time,status,ideinfo) VALUES 
    (".($sum+1).",$uid,0,$lang,'$code',".time().",'Waiting...','".JSON_encode($array,JSON_UNESCAPED_UNICODE)."')";
    if (!$conn->query($sql)) {echo "Failed to insert data! ".mysqli_error($conn);exit;}
    echo $sum+1;exit;
?>