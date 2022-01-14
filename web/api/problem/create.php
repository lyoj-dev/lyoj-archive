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
    header("Content-Type:text/html;charset=utf-8");
    session_start(); mysqli_query($conn, "SET NAMES UTF8");
    mysqli_query($conn,"set character_set_database=\"utf8\"");
    $sql = "SELECT * FROM problem";
    $result = mysqli_query($conn, $sql);
    $id = mysqli_num_rows($result) + 1;
    if (!is_dir("../../../problem/$id")) mkdir("../../../problem/$id");
    $data = explode(",",$_POST["file"]);
    $data = base64_decode($data[1]);
    $fp=fopen("../../../problem/$id/data.zip","wb");fwrite($fp,$data);
    fclose($fp); $zip = new ZipArchive();
    $filePath = realpath("../../../problem/$id/data.zip");
    $path = realpath("../../../problem/$id/");
    if ($zip->open($filePath) === true) {
        $zip->extractTo($path);
        $zip->close();
    } else {
        unlink($filePath);
        exit;
    } unlink($filePath);
    $files=scandir("../../../problem/$id/");$bracket=array();
    for ($i=0;$i<count($files);$i++) {
        $name=explode(".",$files[$i])[0];
        $extension=explode(".",$files[$i])[1];
        if (!array_key_exists($name,$bracket)) $bracket[$name]=array();
        $bracket[$name][]=$extension;
    } $json=array(
        "input"=>$_POST["input-file"],
        "output"=>$_POST["output-file"],
        "spj"=>array(
            "type"=>1,
            "source"=>"",
            "compile_cmd"=>"",
            "exec_path"=>"",
            "exec_name"=>"",
            "exec_param"=>""
        ),
        "data"=>array()
    );ksort($bracket,SORT_STRING | SORT_FLAG_CASE | SORT_NATURAL);
    foreach ($bracket as $key => $value) {
        $accepted=true;$exist_in=false;$exist_out=false;
        for ($i=0;$i<count($value);$i++) {
            if ($value[$i]!="in"&&$value[$i]!="out"&&$value[$i]!="ans") $accepted=false;
            else if ($value[$i]=="in") $exist_in=true;
            else $exist_out=true;
        }
        if (count($value)==2&&$accepted&&$exist_in&&$exist_out) {
            $array=array(
                "input"=>$key.".".($value[0]=="in"?$value[0]:$value[1]),
                "output"=>$key.".".($value[0]=="in"?$value[1]:$value[0]),
                "score"=>0,
                "time"=>1000,
                "memory"=>131072
            );
            $json["data"][]=$array;
        }
    } if (count($json["data"])) $min=100/count($json["data"]);$max=count($json["data"])-(100-count($json["data"])*$min);
    for ($i=0;$i<count($json["data"]);$i++) $json["data"][$i]["score"]=($i<=$max?$min:$min+1);
    $fp=fopen("../../../problem/$id/config.json","wb");fwrite($fp,json_encode($json));fclose($fp);
    $_POST["title"]=str_replace("'","\\'",$_POST["title"]);
    $_POST["background"]=str_replace("'","\\'",$_POST["background"]);
    $_POST["description"]=str_replace("'","\\'",$_POST["description"]);
    $_POST["input"]=str_replace("'","\\'",$_POST["input"]);
    $_POST["output"]=str_replace("'","\\'",$_POST["output"]);
    $_POST["cases"]=str_replace("'","\\'",$_POST["cases"]);
    $_POST["hint"]=str_replace("'","\\'",$_POST["hint"]);
    $sql = "INSERT INTO problem (id,name,bg,descrip,input,output,cases,hint) VALUES 
        (" . $id . ",'" . trim($_POST["title"]) . "','" . trim($_POST["background"]) . "',
        '" . trim($_POST["description"]) . "','" . $_POST["input"] . "','" . trim($_POST["output"]) . "',
        '" . trim($_POST["cases"]) . "','" . trim($_POST["hint"]) . "')";
    if (!$conn->query($sql)) echo mysqli_error($conn);
    else echo $id;
?>