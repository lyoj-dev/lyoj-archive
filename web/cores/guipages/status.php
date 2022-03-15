<?php
function GetFormatStatus(string $status,bool $full,string $style):string {
    $icon="";$name=""; 
    if (strpos($status,"Running on Test")===FALSE) {
        switch($status) {
            case "Compiling...":$icon="spinner";break;
            case "Accepted":$icon="check";break;
            case "Wrong Answer":$icon="x";break;
            case "Time Limited Exceeded":$icon="clock";break;
            case "Memory Limited Exceeded":$icon="microchip";break;
            case "Runtime Error":$icon="bomb";break;
            case "Compile Error":$icon="code";break;
            default:$icon="question circle outline";break;
        }; switch($status) {
            case "Compiling...":$name="compiling";break;
            case "Accepted":$name="accepted";break;
            case "Wrong Answer":$name="wrong-answer";break;
            case "Time Limited Exceeded":$name="time-limited-exceeded";break;
            case "Memory Limited Exceeded":$name="memory-limited-exceeded";break;
            case "Runtime Error":$name="runtime-error";break;
            case "Compile Error":$name="compile-error";break;
            default:$name="default";break;
        };
    } else {$icon="spinner";$name="compiling";}
    return InsertTags("p",array("class"=>$name.($full?"-full":"")." transition","style"=>$style),
    InsertTags("i",array("class"=>"$icon icon"),"").$status);
}

function InitStatus():string {
    $ret=InsertCssStyle(array(".transition"),array("transition"=>"color 0.2s","cursor"=>"pointer"));
    $ret.=InsertCssStyle(array(".accepted:hover"),array("color"=>"rgb(36,140,36)"));
    $ret.=InsertCssStyle(array(".wrong-answer:hover"),array("color"=>"rgb(255,0,0)"));
    $ret.=InsertCssStyle(array(".time-limited-exceeded:hover"),array("color"=>"rgb(244,164,96);"));
    $ret.=InsertCssStyle(array(".memory-limited-exceeded:hover"),array("color"=>"gb(244,164,96);"));
    $ret.=InsertCssStyle(array(".runtime-error:hover"),array("color"=>"rgb(153,50,204);"));
    $ret.=InsertCssStyle(array(".compiling:hover"),array("color"=>"#6cf"));
    $ret.=InsertCssStyle(array(".compile-error:hover"),array("color"=>"rgb(0,68,136)"));
    $ret.=InsertCssStyle(array(".default:hover"),array("color"=>"pink"));
    $ret.=InsertCssStyle(array(".accepted-full"),array("color"=>"rgb(36,140,36)"));
    $ret.=InsertCssStyle(array(".wrong-answer-full"),array("color"=>"rgb(255,0,0)"));
    $ret.=InsertCssStyle(array(".time-limited-exceeded-full"),array("color"=>"rgb(244,164,96);"));
    $ret.=InsertCssStyle(array(".memory-limited-exceeded-full"),array("color"=>"gb(244,164,96);"));
    $ret.=InsertCssStyle(array(".runtime-error-full"),array("color"=>"rgb(153,50,204);"));
    $ret.=InsertCssStyle(array(".compiling-full"),array("color"=>"#6cf"));
    $ret.=InsertCssStyle(array(".compile-error-full"),array("color"=>"rgb(0,68,136)"));
    $ret.=InsertCssStyle(array(".default-full"),array("color"=>"pink"));
    $ret.=InsertCssStyle(array(".testcase"),array(
        "width"=>"calc( 100% - 10px )",
        "height"=>"45px",
        "background-color"=>"white",
        "padding-left"=>"30px",
        "margin-bottom"=>"20px",
        "align-items"=>"center",
        "color"=>"rgb(167,167,167)",
        "transition"=>"color 0.2s",
        "border-radius"=>"5px"
    )); $ret.=InsertCssStyle(array(".testcase:hover"),array(
        "color"=>"black"
    ));
    $ret.=InsertCssStyle(array(".testcase:hover > .accepted"),array("color"=>"rgb(36,140,36)"));
    $ret.=InsertCssStyle(array(".testcase:hover > .wrong-answer"),array("color"=>"rgb(255,0,0)"));
    $ret.=InsertCssStyle(array(".testcase:hover > .time-limited-exceeded"),array("color"=>"rgb(244,164,96);"));
    $ret.=InsertCssStyle(array(".testcase:hover > .memory-limited-exceeded"),array("color"=>"gb(244,164,96);"));
    $ret.=InsertCssStyle(array(".testcase:hover > .runtime-error"),array("color"=>"rgb(153,50,204);"));
    $ret.=InsertCssStyle(array(".testcase:hover > .compiling"),array("color"=>"#6cf"));
    $ret.=InsertCssStyle(array(".testcase:hover > .compile-error"),array("color"=>"rgb(0,68,136)"));
    $ret.=InsertCssStyle(array(".testcase:hover > .default"),array("color"=>"pink"));
    $ret.="@keyframes circle{from{transform:rotate(0);}to{transform:rotate(360deg);}}";
    $ret.=InsertCssStyle(array(".spinner"),array("animation"=>"circle 1s infinite linear","transform-origin"=>"center 50%"));
    return $ret;
}

function JudgeStatus(string $status):bool {
    switch($status) {
        case "Accepted":return true;break;
        case "Wrong Answer":return true;break;
        case "Compile Error":return true;break;
        case "Time Limited Exceeded":return true;break;
        case "Memory Limited Exceeded":return true;break;
        case "Runtime Error":return true;break;
        default:return false;break;
    };
}

function run(array $param,string &$html,string &$body):void {
    if (FindExist2("/id",$param)) {
        info_run($param,$html,$body);
        return;
    }
}

function info_run(array $param,string &$html,string &$body):void {
    $config=GetConfig();
    $body.=InsertTags("style",null,InitStatus());
    $status_controller=new Status_Controller;
    $user_controller=new User_Controller;
    $problem_controller=new Problem_Controller;
    $login_controller=new Login_Controller;
    $info=$status_controller->GetJudgeInfoById($param["id"]);
    $userinfo=$user_controller->GetWholeUserInfo($info["uid"]);
    $probleminfo=$problem_controller->ListProblemByPid($info["pid"],$info["pid"]);
    $header=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"10%"))),"#".$info["id"]);
    $header.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"15%"))),$userinfo["name"]);
    $header.=InsertTags("p",array(
        "style"=>InsertInlineCssStyle(array("width"=>"45%","cursor"=>"pointer")),
        "onclick"=>"window.open('".GetUrl("problem",array(
            "pid"=>$probleminfo[0]["id"]
        ))."')"
    ),$probleminfo[0]["name"]); $tags_content="";
    $header.=InsertTags("div",array("id"=>"whole-status"),GetFormatStatus($info["status"],true,""));
    $body.=InsertTags("div",array("class"=>"default_main flex","style"=>InsertInlineCssStyle(array(
        "width"=>"calc( 100% - 10px )",
        "min-height"=>"50px",
        "background-color"=>"white",
        "padding-left"=>"30px",
        "margin-bottom"=>"20px",
        "align-items"=>"center"
    ))),$header);
    if ($login_controller->CheckLogin()==$info["uid"]) {
        $code=InsertTags("textarea",array("id"=>"code","style"=>InsertInlineCssStyle(array(
            "position"=>"absolute",
            "left"=>"-1000px",
            "top"=>"-1000px"
        ))),$info["code"]);
        $code.=InsertTags("pre",array("style"=>InsertInlineCssStyle(array(
            "font-size"=>"15px",
            "margin-top"=>"0px"
        ))),InsertTags("code",array("class"=>"language-".$config["lang"][$info["lang"]]["highlight-mode"],
        "style"=>InsertInlineCssStyle(array(
            "background-color"=>"white",
            "tab-size"=>"4",
            "border"=>"1px solid",
            "border-color"=>"#e8e8e8",
            "border-radius"=>"5px"
        ))),htmlentities($info["code"])));
        $body.=InsertTags("div",array("class"=>"default_main","style"=>InsertInlineCssStyle(array(
            "padding-top"=>"20px",
            "padding-bottom"=>"1px",
            "width"=>"100%",
            "padding-left"=>"10px",
            "padding-right"=>"10px",
            "background-color"=>"white",
            "margin-bottom"=>"20px",
        ))),InsertTags("hp",array("style"=>InsertInlineCssStyle(array(
            "padding-bottom"=>"40px",
            "padding-left"=>"20px",
            "padding-right"=>"20px"
        ))),"Code&nbsp;".InsertTags("button",array("onclick"=>"copy()","style"=>InsertInlineCssStyle(array(
            "width"=>"50px",
            "height"=>"25px",
            "line-height"=>"20px",
            "padding-left"=>"10px",
            "padding-top"=>"2px",
        ))),"copy")).$code);
    }
    $json=json_decode($info["result"],true); 
    if (JudgeStatus($info["status"])) {
        if (array_key_exists("compile_info",$json)&&$json["compile_info"]!="") {
            $body.=InsertTags("div",array("class"=>"default_main","style"=>InsertInlineCssStyle(array(
                "padding-top"=>"20px",
                "padding-bottom"=>"1px",
                "width"=>"100%",
                "padding-left"=>"10px",
                "padding-right"=>"10px",
                "background-color"=>"white",
                "margin-bottom"=>"20px",
            ))),InsertTags("hp",array("style"=>InsertInlineCssStyle(array(
                "padding-bottom"=>"40px",
                "padding-left"=>"20px",
                "padding-right"=>"20px"
            ))),"Compile Info").InsertTags("pre",null,InsertTags("code",array("class"=>"language-cpp",
            "style"=>InsertInlineCssStyle(array(
                "background-color"=>"white",
                "tab-size"=>"4",
                "border"=>"1px solid",
                "border-color"=>"#e8e8e8",
                "border-radius"=>"5px"
            ))),$json["compile_info"])));
        }
    }
    if (JudgeStatus($info["status"])&&$info["status"]!="Compile Error") {
        for ($i=0;$i<count($json["info"]);$i++) {
            $test=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"18%"))),"Test&nbsp;Case&nbsp;#".($i+1));
            $test.=GetFormatStatus($json["info"][$i]["state"],false,InsertInlineCssStyle(array("width"=>"27%","height"=>"100%","line-height"=>"45px")));
            $test.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"18%"))),"Score: ".$json["info"][$i]["score"]);
            $test.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"18%"))),"Memory: ".
            ($json["info"][$i]["memory"]>1024?intval($json["info"][$i]["memory"]/1024)."MB":$json["info"][$i]["memory"]."KB"));
            $test.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"18%"))),"Time: ".$json["info"][$i]["time"]."ms");
            $body.=InsertTags("div",array("class"=>"default_main flex testcase","onclick"=>"testinfo('".str_replace("\n","",str_replace("'","\\'",$json["info"][$i]["info"]))."')"),$test);    
        }
    } 

    $script="hljs.highlightAll();function copy(){".
    "var aux=document.getElementById('code');".
    "aux.select();var flag=document.execCommand('copy');".
    "if(flag){layer.msg('Success');}else{layer.msg('Failed');console.log(flag)}}".
    "function testinfo(content){layer.open({type:0,content:content});}";
    if (!JudgeStatus($info["status"])) {
        $script.="function JudgeStatus(status){";
        $script.="switch(status) {";
        $script.="case \"Accepted\":return true;break;";
        $script.="case \"Wrong Answer\":return true;break;";
        $script.="case \"Compile Error\":return true;break;";
        $script.="case \"Time Limited Exceeded\":return true;break;";
        $script.="case \"Memory Limited Exceeded\":return true;break;";
        $script.="case \"Runtime Error\":return true;break;";
        $script.="default:return false;break;";
        $script.="};}";
        $script.="function GetFormatStatus(status,full,style) {";
        $script.="icon=\"\";name=\"\"; ";
        $script.="if (status.indexOf(\"Running on Test\")) {";
        $script.="switch(status) {";
        $script.="case \"Compiling...\":icon=\"spinner\";break;";
        $script.="case \"Accepted\":icon=\"check\";break;";
        $script.="case \"Wrong Answer\":icon=\"x\";break;";
        $script.="case \"Time Limited Exceeded\":icon=\"clock\";break;";
        $script.="case \"Memory Limited Exceeded\":icon=\"microchip\";break;";
        $script.="case \"Runtime Error\":icon=\"bomb\";break;";
        $script.="case \"Compile Error\":icon=\"code\";break;";
        $script.="default:icon=\"question circle outline\";break;";
        $script.="}; switch(status) {";
        $script.="case \"Compiling...\":name=\"compiling\";break;";
        $script.="case \"Accepted\":name=\"accepted\";break;";
        $script.="case \"Wrong Answer\":name=\"wrong-answer\";break;";
        $script.="case \"Time Limited Exceeded\":name=\"time-limited-exceeded\";break;";
        $script.="case \"Memory Limited Exceeded\":name=\"memory-limited-exceeded\";break;";
        $script.="case \"Runtime Error\":name=\"runtime-error\";break;";
        $script.="case \"Compile Error\":name=\"compile-error\";break;";
        $script.="default:name=\"default\";break;";
        $script.="};";
        $script.="} else {icon=\"spinner\";name=\"compiling\";}";
        $script.="return \"<p class='\"+name+(full?\"-full\":\"\")+\" transition' style='\"+(style==undefined?\"\":style)+\"'>".
        "<i class='\"+icon+\" icon'></i>\"+status+\"</p>\"";
        $script.="}";
        $script.="function strip_tags_pre(msg){msg=msg.replace(/<(\/)?pre[^>]*>/g,'');return msg;}";
        $script.="var eid=setInterval(function(){";
        $script.="var info=SendAjax(\"".GetAPIUrl("/status/info",array("id"=>$param["id"]))."\",\"GET\",null);";
        $script.="console.log(strip_tags_pre(info));";
        $script.="if (JSON.parse(strip_tags_pre(info))[\"code\"]!=0) {clearInterval(eid); return false;}";
        $script.="var json=JSON.parse(strip_tags_pre(info))[\"data\"];";
        $script.="document.getElementById('whole-status').innerHTML=GetFormatStatus(json[\"status\"],true);";
        $script.="if (JudgeStatus(json[\"status\"])) {json=JSON.parse(json[\"result\"]); ";
        $script.="if (json[\"compile_info\"]!=undefined&&json[\"compile_info\"]!=\"\") {";
        $script.="document.getElementById('main').innerHTML+=";
        $script.="'<div class=\"default_main\" style=\"padding:20px 10px 1px;width:100%;background-color:".
        "white;margin-bottom:20px;opacity:1;top:0px;\"><hp style=\"padding-bottom:40px;padding-left:20px;".
        "padding-right:20px;\">Compile Info</hp><pre><code class=\"language-cpp hljs\" style=\"background-color:white;".
        "tab-size:4;border:1px solid;border-color:#e8e8e8;border-radius:5px;\">'+json[\"compile_info\"]+'</code></pre></div>'";
        $script.="} if (json[\"info\"]!=undefined) ";
        $script.="for (i=0;i<json[\"info\"].length;i++) {";
        $script.="var tmp='<p style=\"width:18%\">Test&nbsp;Case&nbsp;#'+(i+1)+'</p>';";
        $script.="tmp+=GetFormatStatus(json['info'][i]['state'],false,'width:27%;height:100%;line-height:45px');";
        $script.="tmp+='<p style=\"width:18%\">Score: '+json['info'][i]['score']+'</p>';";
        $script.="tmp+='<p style=\"width:18%\">Memory: '+(json['info'][i]['memory']>1024?((json['info'][i]['memory']/1024)|0)+\"MB\":json['info'][i]['memory']+\"KB\")+'</p>';";
        $script.="tmp+='<p style=\"width:18%\">Time: '+json['info'][i]['time']+'ms</p>';";
        $script.="testinfo1=json['info'][i]['info']; testinfo1=testinfo1.replace(/[\\n\\r]/g,'');";
        $script.="testinfo1=testinfo1.replace(/'/g,\"\\\\'\"); console.log(testinfo1);";
        $script.="document.getElementById('main').innerHTML+='<div class=\"default_main flex testcase\" onclick=\"testinfo(\''";
        $script.="+testinfo1+'\')\" style=\"opacity:1;top:0px;\">'+tmp+'</div>';";
        $script.="} hljs.highlightAll(); clearInterval(eid);";
        $script.="}},1000)";
    }
    $body.=InsertTags("script",null,$script);
}
?>