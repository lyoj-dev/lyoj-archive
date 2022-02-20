<?php
function run(array $param,string &$html,string &$body):void {
    if (array_key_exists("id",$param)) {
        info_run($param,$html,$body);
        return;
    }
}

function info_run(array $param,string &$html,string &$body):void {
    $body.=InsertTags("div",array("class"=>"default_main"),$param["id"]);
    
}
?>