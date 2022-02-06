<?php
function list_run(array $param,string &$html,string &$body):void {
    $tmp=""; if (FindExist2("/pid",$_GET)) {
        info_run($param,$html,$body);
        return;
    }
    $problem_controller=new Problem_Controller;
    $status_controller=new Status_Controller;
    $tags_controller=new Tags_Controller;
    $page=$_GET["page"]; $config=GetConfig();
    $problem_list=$problem_controller->ListProblemByNumber(
        ($page-1)*$config["controllers"]["problem"]["configs"]["number_of_pages"]+1,
        $page*$config["controllers"]["problem"]["configs"]["number_of_pages"]
    ); $style=InsertCssStyle(array(".problem-item"),array(
            "width"=>"100%",
            "min-height"=>"50px",
            "background-color"=>"white",
            "padding-left"=>"20px",
            "margin-bottom"=>"20px",
            "box-shadow"=>"0 0.375rem 1.375rem rgb(175 194 201 / 50%)",
            "align-items"=>"center"
    )); for ($i=0;$i<count($config["difficulties"]);$i++) {
        $style.=InsertCssStyle(array(".problem-difficulties-$i"),array(
            "background-color"=>$config["difficulties"][$i]["color"],
            "border-radius"=>"100px",
            "height"=>"25px",
            "color"=>"white",
            "padding-left"=>"10px",
            "padding-right"=>"10px",
            "font-size"=>"13px",
            "line-height"=>"25px",
            "margin-right"=>"20px"
        ));
    } $style.=InsertCssStyle(array(".problem-tags"),array(
        "background-color"=>"rgb(41,73,180)",
        "border-radius"=>"100px",
        "height"=>"25px",
        "color"=>"white",
        "padding-left"=>"10px",
        "padding-right"=>"10px",
        "font-size"=>"13px",
        "line-height"=>"25px",
        "margin-right"=>"4px",
        "margin-bottom"=>"4px",
        "width"=>"fit-content",
        "display"=>"inline-block"
    )); $body.=InsertTags("style",null,$style);
    for ($i=0;$i<count($problem_list);$i++) {
        $tmp=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"10%"))),"P".$problem_list[$i]["id"]);
        $tmp.=InsertTags("p",array(
            "style"=>InsertInlineCssStyle(array("width"=>"40%","cursor"=>"pointer")),
            "onclick"=>"location.href='".GetUrl("problem",array(
                "pid"=>$problem_list[$i]["id"]
            ))."'"
        ),$problem_list[$i]["name"]); $tags_content="";
        $tags=$tags_controller->ListTagsByPid($problem_list[$i]["id"]);
        if ($tags!=null) for ($j=0;$j<count($tags);$j++) $tags_content.=InsertTags("div",array("class"=>"problem-tags"),$tags[$j]["tagname"]);
        $tmp.=InsertTags("div",array("style"=>InsertInlineCssStyle(array("width"=>"20%","padding-top"=>"12.5px","padding-bottom"=>"8.5px"))),$tags_content);
        $tmp.=InsertTags("div",array("style"=>InsertInlineCssStyle(array("width"=>"15%","text-align"=>"center"))),
        InsertTags("div",array("class"=>"problem-difficulties-".$problem_list[$i]["difficult"],"style"=>InsertInlineCssStyle(array(
            "width"=>"fit-content",
            "margin"=>"auto"
        ))),$config["difficulties"][$problem_list[$i]["difficult"]]["name"]));
        $accepted=count($status_controller->ListAcceptedByPid($problem_list[$i]["id"]));
        $whole=count($status_controller->ListWholeByPid($problem_list[$i]["id"]));
        $tmp.=InsertTags("div",array("style"=>InsertInlineCssStyle(array("width"=>(10*$accepted/($whole?$whole:1))."%","height"=>"15px","background-color"=>"rgb(126,204,89)"))),"");
        $tmp.=InsertTags("div",array("style"=>InsertInlineCssStyle(array("width"=>(10-10*$accepted/($whole?$whole:1))."%","height"=>"15px","background-color"=>"rgb(232,232,232)"))),"");
        $body.=InsertTags("div",array(
            "class"=>"problem-item default_main flex",
        ),$tmp);
    } 
}

function info_run(array $param,string &$html,string &$body):void {
    $config=GetConfig();
    $problem_controller=new Problem_Controller;    
    $login_controller=new Login_Controller;
    $info=$problem_controller->ListProblemByPid($_GET["pid"],$_GET["pid"])[0];

    $fp=fopen("../problem/".$info["id"]."/config.json","r");
    $conf=fread($fp,filesize("../problem/".$info["id"]."/config.json"));
    $conf=JSON_decode($conf,true);
    $min_t=1e18;$max_t=-1e18;$min_m=1e18;$max_m=-1e18;
    for ($i=0;$i<count($conf["data"]);$i++) {
        $min_t=min($min_t,$conf["data"][$i]["time"]);
        $max_t=max($max_t,$conf["data"][$i]["time"]);
        $min_m=min($min_m,$conf["data"][$i]["memory"]);
        $max_m=max($max_m,$conf["data"][$i]["memory"]);
    } $info_res="Input: ".$conf["input"]." | Output: ".$conf["input"]." | ";
    $info_res.="Time: ".(($min_t==$max_t)?$min_t."ms":$min_t."ms~".$max_t."ms");$info_res.=" | ";
    $info_res.="Memory: ".(($min_m==$max_m)?($min_m/1024)."mb":($min_m/1024)."mb~".($max_m/1024)."mb");
    $title=InsertTags("hp",array("style"=>InsertInlineCssStyle(array(
        "padding-left"=>"20px",
        "padding-right"=>"20px",
        "font-size"=>"25px",
        "font-weight"=>"400"
    ))),"P".$info["id"]." - ".$info["name"]).
    InsertTags("p",array("style"=>InsertInlineCssStyle(array(
        "padding-left"=>"20px",
        "padding-right"=>"20px",
        "margin-top"=>"5px",
    ))),htmlentities($info_res));
    $body.=InsertTags("div",array("class"=>"default_main","style"=>InsertInlineCssStyle(array(
        "padding-top"=>"20px",
        "margin-bottom"=>"20px",
        "padding-bottom"=>"20px",
    ))),$title);

    if ($info["bg"]!="") {
        $body.=InsertTags("div",array("class"=>"default_main","style"=>InsertInlineCssStyle(array(
            "padding-top"=>"20px",
            "margin-bottom"=>"20px"
        ))),InsertTags("hp",array("style"=>InsertInlineCssStyle(array(
            "padding-bottom"=>"60px",
            "padding-left"=>"20px",
            "padding-right"=>"20px"
        ))),"Background").InsertTags("div",array("id"=>"problem-background","style"=>InsertInlineCssStyle(array(
            "width"=>"calc(100% - 40px)"
        ))),""));
        $script=md2html($info["bg"],"problem-background");
    }

    if ($info["descrip"]!="") {
        $body.=InsertTags("div",array("class"=>"default_main","style"=>InsertInlineCssStyle(array(
            "padding-top"=>"20px",
            "margin-bottom"=>"20px"
        ))),InsertTags("hp",array("style"=>InsertInlineCssStyle(array(
            "padding-bottom"=>"60px",
            "padding-left"=>"20px",
            "padding-right"=>"20px"
        ))),"Description").InsertTags("div",array("id"=>"problem-description","style"=>InsertInlineCssStyle(array(
            "width"=>"calc(100% - 40px)"
        ))),""));
        $script.=md2html($info["descrip"],"problem-description");
    }

    if ($info["input"]!="") {
        $body.=InsertTags("div",array("class"=>"default_main","style"=>InsertInlineCssStyle(array(
            "padding-top"=>"20px",
            "margin-bottom"=>"20px"
        ))),InsertTags("hp",array("style"=>InsertInlineCssStyle(array(
            "padding-bottom"=>"60px",
            "padding-left"=>"20px",
            "padding-right"=>"20px"
        ))),"Input").InsertTags("div",array("id"=>"problem-input","style"=>InsertInlineCssStyle(array(
            "width"=>"calc(100% - 40px)"
        ))),""));
        $script.=md2html($info["input"],"problem-input");
    }

    if ($info["output"]!="") {
        $body.=InsertTags("div",array("class"=>"default_main","style"=>InsertInlineCssStyle(array(
            "padding-top"=>"20px",
            "margin-bottom"=>"20px"
        ))),InsertTags("hp",array("style"=>InsertInlineCssStyle(array(
            "padding-bottom"=>"60px",
            "padding-left"=>"20px",
            "padding-right"=>"20px"
        ))),"Output").InsertTags("div",array("id"=>"problem-output","style"=>InsertInlineCssStyle(array(
            "width"=>"calc(100% - 40px)"
        ))),""));
        $script.=md2html($info["output"],"problem-output");
    }

    $style=InsertCssStyle(array(".problem-cases"),array(
        "border"=>"rgb(221,221,221) solid 1px",
        "background-color"=>"rgb(249,255,204)",
        "overflow"=>"auto",
        "border-radius"=>"3px",
        "font-size"=>"15px",
        "font-family"=>"monospace",
        "margin"=>"7px 0",
        "padding"=>"8.4px 10px",
        "width"=>"calc(100% - 20px)"
    ));
    $info["cases"]=preg_replace('/[[:cntrl:]]/','',$info["cases"]);
    $sample=JSON_decode($info["cases"],true); $res="";
    for ($i=0;$i<count($sample);$i++) {
        $sample[$i]["input"]=str_replace("\\n","<br/>",$sample[$i]["input"]);
        $sample[$i]["output"]=str_replace("\\n","<br/>",$sample[$i]["output"]);
        $tmp=InsertTags("div",array("style"=>InsertInlineCssStyle(array("width"=>"49%","margin-right"=>"2%"))),
        InsertTags("p",null,"Input #".($i+1).":").InsertTags("pre",array("class"=>"problem-cases","id"=>"problem-cases-input".($i+1)),$sample[$i]["input"]));
        $tmp.=InsertTags("div",array("style"=>InsertInlineCssStyle(array("width"=>"49%"))),
        InsertTags("p",null,"Output #".($i+1).":").InsertTags("pre",array("class"=>"problem-cases","id"=>"problem-cases-output".($i+1)),$sample[$i]["output"]));
        $res.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array(
            "padding-left"=>"20px",
            "padding-right"=>"20px",
            "margin-top"=>"10px"
        ))),$tmp);
    }
    $body.=InsertTags("div",array("class"=>"default_main","style"=>InsertInlineCssStyle(array(
        "padding-top"=>"20px",
        "margin-bottom"=>"20px"
    ))),InsertTags("hp",array("style"=>InsertInlineCssStyle(array(
        "padding-bottom"=>"60px",
        "padding-left"=>"20px",
        "padding-right"=>"20px"
    ))),"Sample").InsertTags("div",array("id"=>"problem-sample","style"=>InsertInlineCssStyle(array(
        "width"=>"calc(100% - 40px)",
        "padding-bottom"=>"20px"
    ))),$res));

    if ($info["hint"]!="") {
        $body.=InsertTags("div",array("class"=>"default_main","style"=>InsertInlineCssStyle(array(
            "padding-top"=>"20px",
            "margin-bottom"=>"20px"
        ))),InsertTags("hp",array("style"=>InsertInlineCssStyle(array(
            "padding-bottom"=>"60px",
            "padding-left"=>"20px",
            "padding-right"=>"20px"
        ))),"Hint").InsertTags("div",array("id"=>"problem-hint","style"=>InsertInlineCssStyle(array(
            "width"=>"calc(100% - 40px)"
        ))),""));
        $script.=md2html($info["hint"],"problem-hint");
    }

    // if ($login_controller->CheckLogin()) {
        $tmp=""; for ($i=0;$i<count($config["lang"]);$i++) 
        if ($i!=$config["default_lang"]) $tmp.=InsertTags("option",array("value"=>$i),$config["lang"][$i]["name"]);
        else $tmp.=InsertTags("option",array("value"=>$i,"selected"=>"selected"),$config["lang"][$i]["name"]);
        $tmp=InsertTags("div",array("class"=>"flex"),InsertTags("p",null,"Choose Language:&nbsp").
            InsertTags("select",array("id"=>"language","style"=>InsertInlineCssStyle(array(
            "width"=>"10px",
            "flex-grow"=>"1000",
            "height"=>"30px",
            "padding-left"=>"10px",
            "outline"=>"none",
            "border"=>"rgb(221,221,221) 1px solid",
            "background-color"=>"rgb(249,255,204)",
            "border-radius"=>"3px",
            "padding-right"=>"10px"
        ))),$tmp));
        $tmp.=InsertTags("div",array("id"=>"code-container","style"=>InsertInlineCssStyle(array(
            "height"=>"500px",
            "min-height"=>"inherit",
            "margin-top"=>"inherit",
            "margin-bottom"=>"inherit",
            "margin-top"=>"10px",
        ))),"");
        $tmp.=InsertTags("center",null,
        InsertTags("button",array("style"=>InsertInlineCssStyle(array(
            "width"=>"80px",
            "height"=>"30px",
            "margin-top"=>"10px",
            "border"=>"rgb(221,221,221) 1px solid"
        )),"onclick"=>"submit()"),"Submit"));
        $script.="var codeEditor=null;";
        $script.="var codeEditor=monaco.editor.create(".
        "document.getElementById('code-container'), {language:'".$config["lang"][$config["default_lang"]]["mode"]."',roundedSelection:false,".
        "scrollBeyondLastLine:false,readOnly:false,theme:'vs-dark'});";
        $body.=InsertTags("div",array("class"=>"default_main","style"=>InsertInlineCssStyle(array(
            "padding-top"=>"20px",
            "margin-bottom"=>"20px"
        ))),InsertTags("hp",array("style"=>InsertInlineCssStyle(array(
            "padding-bottom"=>"60px",
            "padding-left"=>"20px",
            "padding-right"=>"20px"
        ))),"Submit").InsertTags("div",array("id"=>"problem-submit","style"=>InsertInlineCssStyle(array(
            "width"=>"calc(100% - 40px)",
            "padding-left"=>"20px",
            "padding-right"=>"20px",
            "padding-top"=>"20px",
            "padding-bottom"=>"20px"
        ))),$tmp));
        $script.="var mode=["; for ($i=0;$i<count($config["lang"])-1;$i++) $script.="'".$config["lang"][$i]["mode"]."',";
        $script.="'".$config["lang"][count($config["lang"])-1]["mode"]."'];";
        $script.="console.log(codeEditor);document.getElementById('language').onchange=function(){".
        "monaco.editor.setModelLanguage(codeEditor.getModel(),mode[document.getElementById('language').value])};";
        $script.="function submit(){var lang=document.getElementById('language').value,code=codeEditor.getValue();".
        "console.log(lang);console.log(code);}";
    // }

    $body.=InsertTags("style",null,$style);
    $body.=InsertTags("script",null,$script);
}
?>