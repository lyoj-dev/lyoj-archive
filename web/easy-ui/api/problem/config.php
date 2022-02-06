<meta charset="UTF-8">
<?php
    $fp=fopen("../../../../config.json","r");
    $json=fread($fp,filesize("../../../../config.json"));
    echo $json;
?>