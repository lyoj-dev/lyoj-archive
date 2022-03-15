<?php
    require_once "config.php";
    $conn=mysqli_connect($config["mysql"]["server"],$config["mysql"]["user"],
                         $config["mysql"]["passwd"],$config["mysql"]["database"]);
    if (!$conn) {
        echo "Failed to connect database! ".mysqli_error($conn);
        exit;
    }
    header("Content-Type:text/html;charset=utf-8");session_start();
    if ($_SERVER['REQUEST_METHOD']!='POST')
    {echo "Failed to parse data!";exit;}
    $code=trim($_POST["code"]);$pid=trim($_POST["pid"]);$uid=trim($_POST["uid"]);$lang=trim($_POST["lang"]);$lang=trim($_POST["lang"]);$lang=trim($_POST["lang"]);
    $code=str_replace("'","\\'",str_replace("\\","\\\\",$code));
    $result=mysqli_query($conn,"SELECT * FROM status");
    if (!$result) {echo "Failed to query database! ".mysqli_error($conn);exit;}
    $sum=mysqli_num_rows($result);
    $sql="INSERT INTO status (id,uid,pid,lang,code,result,time,status,ideinfo,judged) VALUES 
    (".($sum+1).",$uid,$pid,$lang,'$code','NULL',".time().",'Waiting...','NULL',0)";
    if (!$conn->query($sql)) {echo "Failed to insert data! ".mysqli_error($conn);exit;}
    echo $sum+1;exit;
?>
