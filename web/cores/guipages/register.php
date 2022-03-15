<?php
function run(array $param,string& $html,string& $body):void {
    $config=GetConfig();
    $data.=InsertTags("hp",array("style"=>InsertInlineCssStyle(array(
        "padding-right"=>"20px",
        "margin-bottom"=>"20px"
    ))),"Sign Up");
    $data.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array(
        "padding-top"=>"10px"
    ))), InsertTags("p",null,"Account Email:&nbsp;").
    InsertSingleTag("input",array("id"=>"passwd","type"=>"text","placeholder"=>"Input email here...")));
    $data.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array(
        "padding-top"=>"10px"
    ))), InsertTags("p",null,"Account Name:&nbsp;").
    InsertSingleTag("input",array("id"=>"passwd","type"=>"text","placeholder"=>"Input name here...")));
    $data.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array(
        "padding-top"=>"10px"
    ))), InsertTags("p",null,"Account Password:&nbsp;").
    InsertSingleTag("input",array("id"=>"passwd","type"=>"password","placeholder"=>"Input password here...")));
    $data.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array(
        "padding-top"=>"10px"
    ))), InsertTags("p",null,"Repeat Password:&nbsp;").
    InsertSingleTag("input",array("id"=>"passwd","type"=>"password","placeholder"=>"Input password here...")));
    $body.=InsertTags("div",array("class"=>"default_main","style"=>InsertInlineCssStyle(array(
        "padding-top"=>"20px",
        "padding-left"=>"20px",
        "padding-right"=>"20px",
        "padding-bottom"=>"15px",
        "margin-bottom"=>"20px",
        "width"=>"40%",
        "margin"=>"auto"
    ))),$data);
}
?>