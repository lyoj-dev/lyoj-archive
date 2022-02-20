<meta charset="UTF-8">
<?php
    $fp=fopen("/etc/judge/config.json","r");
    $json=fread($fp,filesize("/etc/judge/config.json"));
    echo $json;
?>
