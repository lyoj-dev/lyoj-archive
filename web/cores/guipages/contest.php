<?php
function GetFormatStatus(string $status,bool $full,string $style):string {
    $icon="";$name=""; 
    if (strpos($status,"Running")===FALSE) {
        switch($status) {
            case "Compiling...":$icon="spinner";break;
            case "Accepted":$icon="check";break;
            case "Submitted":$icon="check";break;
            case "Wrong Answer":$icon="x";break;
            case "Time Limited Exceeded":$icon="clock";break;
            case "Memory Limited Exceeded":$icon="database";break;
            case "Runtime Error":$icon="bomb";break;
            case "Compile Error":$icon="code";break;
            default:$icon="question circle outline";break;
        }; switch($status) {
            case "Compiling...":$name="compiling";break;
            case "Accepted":$name="accepted";break;
            case "Submitted":$name="accepted";break;
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
    $ret.=InsertCssStyle(array(".memory-limited-exceeded:hover"),array("color"=>"rgb(244,164,96);"));
    $ret.=InsertCssStyle(array(".runtime-error:hover"),array("color"=>"rgb(153,50,204);"));
    $ret.=InsertCssStyle(array(".compiling:hover"),array("color"=>"#6cf"));
    $ret.=InsertCssStyle(array(".compile-error:hover"),array("color"=>"rgb(0,68,136)"));
    $ret.=InsertCssStyle(array(".default:hover"),array("color"=>"pink"));
    $ret.=InsertCssStyle(array(".accepted-full"),array("color"=>"rgb(36,140,36)"));
    $ret.=InsertCssStyle(array(".wrong-answer-full"),array("color"=>"rgb(255,0,0)"));
    $ret.=InsertCssStyle(array(".time-limited-exceeded-full"),array("color"=>"rgb(244,164,96);"));
    $ret.=InsertCssStyle(array(".memory-limited-exceeded-full"),array("color"=>"rgb(244,164,96);"));
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
        case "Submitted":return true;break;
        case "Wrong Answer":return true;break;
        case "Compile Error":return true;break;
        case "Time Limited Exceeded":return true;break;
        case "Memory Limited Exceeded":return true;break;
        case "Runtime Error":return true;break;
        default:return false;break;
    };
}

function run(array $param,string& $html,string& $body):void {
    $tmp=""; if (array_key_exists("id",$param)) {
        info_run($param,$html,$body);
        return;
    }
    $style=""; $content="";
    $contest_controller=new Contest_Controller;
    $tags_controller=new Tags_Controller;
    $page=$param["page"]; $config=GetConfig(); $num=$contest_controller->GetContestTotal();
    $pages_num=($num+$config["number_of_pages"]-1)/$config["number_of_pages"];
    $pages_num=intval($pages_num);
    if ($page<=0||$page>$pages_num) $page=1; 
    $contest_list=$contest_controller->GetContest
    (($page-1)*$config["number_of_pages"]+1,$page*$config["number_of_pages"],false,"starttime DESC");
    $style=InsertCssStyle(array(".contest-item"),array(
        "width"=>"100%",
        "min-height"=>"50px",
        "background-color"=>"white",
        "padding-left"=>"20px",
        "margin-bottom"=>"20px",
        "box-shadow"=>"0 0.375rem 1.375rem rgb(175 194 201 / 50%)",
        "align-items"=>"center"
    )); $style.=InsertCssStyle(array(".contest-tags"),array(
        "background-color"=>"#e67e22",
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
        "display"=>"inline-block",
        "cursor"=>"pointer"
    )); for ($i=0;$i<count($contest_list);$i++) {
        $color=""; $word="";
        if (time()<$contest_list[$i]["starttime"]){$color="green";$word="Not start";}
        else if (time()<=$contest_list[$i]["starttime"]+$contest_list[$i]["duration"]){$color="blue";$word="Running";}
        else {$color="red";$word="Finished";}
        $tmp=InsertTags("p",array("style"=>InsertInlineCssStyle(array(
            "margin-left"=>"1%",
            "width"=>"9%",
            "font-weight"=>"500",
            "color"=>$color,
            "cursor"=>"pointer"
        ))),$word);
        $tmp.=InsertTags("p",array(
            "style"=>InsertInlineCssStyle(array("width"=>"37%","cursor"=>"pointer")),
            "onclick"=>"location.href='".GetUrl("contest",array(
            "id"=>$contest_list[$i]["id"],"page"=>"index"))."'",
            "class"=>"ellipsis"
        ),$contest_list[$i]["title"]);
        $starttime=$contest_list[$i]["starttime"]; $endtime=$starttime+$contest_list[$i]["duration"];
        $tags_content=""; $tags=$tags_controller->ListContestTagsById($contest_list[$i]["id"]);
        if ($tags!=null) for ($j=0;$j<count($tags);$j++) $tags_content.=InsertTags("div",array("class"=>"contest-tags"),$tags[$j]["tagname"]);
        $tmp.=InsertTags("div",array("style"=>InsertInlineCssStyle(array("width"=>"20%","padding-top"=>"12.5px","padding-bottom"=>"8.5px"))),$tags_content);
        $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"20%"))),date("m-d H:i",$starttime)." ~ ".date("m-d H:i",$endtime));
        $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"10%"))),count($contest_list[$i]["problem"])." Problems");
        $body.=InsertTags("div",array(
            "class"=>"contest-item default_main flex",
        ),$tmp);
    } $style.=InsertCssStyle(array(".pages"),array(
        "height"=>"30px",
        "line-height"=>"30px",
        "border"=>"1px solid",
        "border-color"=>"rgb(213,216,218)",
        "color"=>"rgb(27,116,221)",
        "margin-top"=>"10px",
        "margin-bottom"=>"10px",
        "padding-left"=>"20px",
        "padding-right"=>"20px",
        "border-radius"=>"3px",
        "font-weight"=>"500",
        "font-size"=>"13px",
        "background-color"=>"white",
        "cursor"=>"pointer",
        "transition"=>"background-color 0.5s,color 0.5s,border-color 0.5s"
    )).InsertCssStyle(array(".banned"),array(
        "color"=>"rgb(137,182,234)"
    )).InsertCssStyle(array(".pages:not(.banned):hover"),array(
        "background-color"=>"rgb(27,116,221)",
        "color"=>"white",
        "border-color"=>"rgb(27,116,221)"
    )); $content.=InsertTags("div",array("class"=>"pages","onclick"=>"location.href='".GetUrl("contest",array("page"=>1))."'",
    "style"=>InsertInlineCssStyle(array("margin-right"=>"10px"))),InsertTags("p",null,"Top"));
    if ($page==1) $content.=InsertTags("div",array("class"=>"pages banned"),InsertTags("p",null,"Previous"));
    else $content.=InsertTags("div",array("class"=>"pages","onclick"=>"location.href='".GetUrl("contest",array("page"=>$page-1))."'"),InsertTags("p",null,"Previous"));
    if ($page==$pages_num) $content.=InsertTags("div",array("class"=>"pages banned"),InsertTags("p",null,"Next"));
    else $content.=InsertTags("div",array("class"=>"pages","onclick"=>"location.href='".GetUrl("contest",array("page"=>$page+1))."'"),InsertTags("p",null,"Next"));
    $content.=InsertTags("div",array("class"=>"pages","onclick"=>"location.href='".GetUrl("contest",array("page"=>$pages_num))."'",
    "style"=>InsertInlineCssStyle(array("margin-left"=>"10px"))),InsertTags("p",null,"Bottom"));
    $body.=InsertTags("style",null,$style);
    $body.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array(
        "justify-content"=>"center"
    ))),$content);
}

function ranking_sort($a,$b) {
    $c=$a["score"]==""?0:$a["score"];
    $d=$b["score"]==""?0:$b["score"];
    if ($c==$d) return $a["name"]<$b["name"]?-1:1;
    return $c<$d?1:-1;
}

function info_run(array $param,string& $html,string& $body):void {
    $config=GetConfig();
    $contest_controller=new Contest_Controller;
    $login_controller=new Login_Controller;
    $user_controller=new User_Controller;
    $problem_controller=new Problem_Controller;
    $tags_controller=new Tags_Controller;
    $status_controller=new Status_Controller;
    Contest_Controller::JudgeContestExist($param["id"]);
    $info=$contest_controller->GetContest($param["id"],$param["id"])[0];
    $info_res="Time: ".date("m-d H:i",$info["starttime"])." ~ ".date("m-d H:i",$info["starttime"]+$info["duration"])." | ".
    "Signup: ".$contest_controller->GetContestSignupNumber($param["id"])." | Problems: ".count($info["problem"]);
    $title=InsertTags("hp",array("style"=>InsertInlineCssStyle(array(
        "padding-left"=>"20px",
        "padding-right"=>"20px",
        "font-size"=>"25px",
        "font-weight"=>"400"
    )),"class"=>"ellipsis"),"#".$param["id"]." - ".$info["title"]).
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

    $style=InitStatus();
    $style.=InsertCssStyle(array(".problem-item"),array(
        "width"=>"calc(100% - 20px)",
        "min-height"=>"50px",
        "background-color"=>"white",
        "padding-left"=>"20px",
        "margin-top"=>"20px",
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
            "margin-right"=>"5px",
            "width"=>"fit-content",
            "display"=>"inline-block",
            "cursor"=>"pointer"
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
        "display"=>"inline-block",
        "cursor"=>"pointer"
    )); $style.=InsertCssStyle(array(".unsubmitted"),array(
        "background-color"=>"rgb(210,210,210)"
    )); $style.=InsertCssStyle(array(".pages"),array(
        "height"=>"30px",
        "line-height"=>"30px",
        "border"=>"1px solid",
        "border-color"=>"rgb(213,216,218)",
        "color"=>"rgb(27,116,221)",
        "margin-top"=>"10px",
        "margin-bottom"=>"10px",
        "padding-left"=>"20px",
        "padding-right"=>"20px",
        "border-radius"=>"3px",
        "font-weight"=>"500",
        "font-size"=>"13px",
        "background-color"=>"white",
        "cursor"=>"pointer",
        "transition"=>"background-color 0.5s,color 0.5s,border-color 0.5s"
    )).InsertCssStyle(array(".banned"),array(
        "color"=>"rgb(137,182,234)"
    )).InsertCssStyle(array(".pages:not(.banned):hover"),array(
        "background-color"=>"rgb(27,116,221)",
        "color"=>"white",
        "border-color"=>"rgb(27,116,221)"
    )); $body.=InsertTags("style",null,$style);

    $timeline=InsertTags("p",null,date("m-d H:i",$info["starttime"])."&nbsp;&nbsp;");
    $timeline.=InsertTags("div",array("style"=>InsertInlineCssStyle(array("flex-grow"=>"2000","background-color"=>"lightgrey","height"=>"10px","border-radius"=>"10px"))),
    InsertTags("p",array("id"=>"time","style"=>InsertInlineCssStyle(array("background-color"=>"orange","height"=>"10px","border-radius"=>"10px","width"=>"0%"))),"")).
    InsertTags("p",null,"&nbsp;&nbsp;".date("m-d H:i",$info["starttime"]+$info["duration"]));
    $body.=InsertTags("div",array("class"=>"flex default_main","style"=>InsertInlineCssStyle(array(
        "padding-top"=>"10px",
        "margin-bottom"=>"20px",
        "padding-bottom"=>"10px",
        "padding-left"=>"10px",
        "padding-right"=>"10px",
    ))),$timeline);

    $features="";$uid=$login_controller->CheckLogin();
    if ($uid&&!$contest_controller->JudgeSignup($param["id"])) {
        $contest=$contest_controller->GetContest($param["id"],$param["id"]);
        $endtime=$contest[0]["starttime"]+$contest[0]["duration"];
        if ($endtime>=time()) $features.=InsertTags("button",array("onclick"=>"signup()"),"Sign Up");
    } $features.=InsertTags("button",array("onclick"=>"location.href='".GetUrl("contest",array("id"=>$param["id"],"page"=>"index"))."'"),"Index");
    if (($uid&&($user_controller->GetWholeUserInfo($uid)["permission"]>1||
    ($info["starttime"]<=time()&&$contest_controller->JudgeSignup($param["id"]))))||$info["starttime"]+$info["duration"]<=time()) {
        $features.=InsertTags("button",array("onclick"=>"location.href='".GetUrl("contest",array("id"=>$param["id"],"page"=>"problem"))."'"),"Problems");
        $features.=InsertTags("button",array("onclick"=>"location.href='".GetUrl("contest",array("id"=>$param["id"],"page"=>"submission"))."'"),"Submissions");
        $features.=InsertTags("button",array("onclick"=>"location.href='".GetUrl("contest",array("id"=>$param["id"],"page"=>"ranking"))."'"),"Ranking");
        $features.=InsertTags("button",array("onclick"=>"location.href='".GetAPIUrl("/contest/rank",array("id"=>$param["id"]))."'"),"Download Ranking");
    } $body.=InsertTags("div",array("class"=>"flex default_main","style"=>InsertInlineCssStyle(array(
    "padding-left"=>"10px","padding-right"=>"10px"))),$features);

    if ($param["page"]!="index"&&$param["page"]!="problem"&&$param["page"]!="submission"&&$param["page"]!="ranking") $param["page"]="index";
    switch($param["page"]) {
        case "index": {
            $fp=fopen("../contest/".$info["id"].".md","r");
            $content=fread($fp,filesize("../contest/".$info["id"].".md"));
            $body.=InsertTags("div",array("class"=>"default_main","style"=>InsertInlineCssStyle(array(
                "padding-top"=>"20px",
                "margin-bottom"=>"20px",
                "margin-top"=>"20px"
            ))),InsertTags("hp",array("style"=>InsertInlineCssStyle(array(
                "padding-bottom"=>"60px",
                "padding-left"=>"20px",
                "padding-right"=>"20px"
            ))),"Index").InsertTags("div",array("id"=>"contest-index","style"=>InsertInlineCssStyle(array(
                "width"=>"calc(100% - 40px)"
            ))),""));
            $script=md2html($content,"contest-index");
        }break;
        case "problem": {
            if (!(($uid&&($user_controller->GetWholeUserInfo($uid)["permission"]>1||
            ($info["starttime"]<=time()&&$contest_controller->JudgeSignup($param["id"]))))||$info["starttime"]+$info["duration"]<=time())) 
            Error_Controller::Common("Permission denied");
            $open=0; if ($uid&&($user_controller->GetWholeUserInfo($uid)["permission"]>1)) $open=1;
            if ($info["starttime"]+$info["duration"]<time()) $open=1;
            for ($i=0;$i<count($info["problem"]);$i++) {
                $problem=$problem_controller->ListProblemByPid($info["problem"][$i],false,$param["id"]);
                if ($problem["accepted"]) $tmp=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"3%","cursor"=>"pointer"))),InsertTags("i",array("class"=>"check icon green"),null));
                else $tmp=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"3%"))),InsertTags("i",array("class"=>"minus icon grey"),null));
                $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"7%","cursor"=>"pointer")),
                "onclick"=>"location.href='".GetUrl("problem",array(
                    "id"=>$problem["id"],
                    "contest"=>$param["id"]
                ))."'"),"#".($i+1));
                $tmp.=InsertTags("p",array(
                    "style"=>InsertInlineCssStyle(array("width"=>"40%","cursor"=>"pointer")),
                    "onclick"=>"location.href='".GetUrl("problem",array(
                        "id"=>$problem["id"],
                        "contest"=>$param["id"]
                    ))."'",
                    "class"=>"ellipsis"
                ),$problem["name"]); $tags_content="";
                $tags=$tags_controller->ListProblemTagsByPid($problem["id"]);
                if ($tags!=null) for ($j=0;$j<count($tags);$j++) $tags_content.=InsertTags("div",array("class"=>"problem-tags"),$tags[$j]);
                $tmp.=InsertTags("div",array("style"=>InsertInlineCssStyle(array("width"=>"20%","padding-top"=>"12.5px","padding-bottom"=>"8.5px"))),$open?$tags_content:"");
                $tmp.=InsertTags("div",array("style"=>InsertInlineCssStyle(array("width"=>"15%","text-align"=>"center"))),
                ($open?InsertTags("div",array("class"=>"problem-difficulties-".$problem["difficult"],"style"=>InsertInlineCssStyle(array(
                    "margin"=>"auto"
                ))),$config["difficulties"][$problem["difficult"]]["name"]):""));
                if ($open) {
                    $accepted=count($status_controller->ListAcceptedByPid($problem["id"]));
                    $whole=count($status_controller->ListWholeByPid($problem["id"]));
                    $tmp.=InsertTags("div",array("style"=>InsertInlineCssStyle(array("width"=>(10*$accepted/($whole?$whole:1))."%","height"=>"15px","background-color"=>"rgb(126,204,89)"))),"");
                    $tmp.=InsertTags("div",array("style"=>InsertInlineCssStyle(array("width"=>(10-10*$accepted/($whole?$whole:1))."%","height"=>"15px","background-color"=>"rgb(232,232,232)"))),"");
                } $body.=InsertTags("div",array(
                    "class"=>"problem-item default_main flex",
                ),$tmp);
            }
        }break;
        case "submission": {
            if (!(($uid&&($user_controller->GetWholeUserInfo($uid)["permission"]>1||
            ($info["starttime"]<=time()&&$contest_controller->JudgeSignup($param["id"]))))||$info["starttime"]+$info["duration"]<=time())) 
            Error_Controller::Common("Permission denied");
            if ($param["num"]==null) $param["num"]=1;
            $l=($param["num"]-1)*$config["status_number"]+1;
            $r=$param["num"]*$config["status_number"]; $sum=0;
            $status=$contest_controller->GetContestSubmit($param["id"],$l,$r,$sum);
            $page_num=intval(($sum-1)/$config["status_number"])+1;
            if ($param["num"]<1) $param["num"]=1;
            if ($param["num"]>$page_num) $param["num"]=$page_num;
            $l=($param["num"]-1)*$config["status_number"]+1;
            $r=$param["num"]*$config["status_number"];
            $status=$contest_controller->GetContestSubmit($param["id"],$l,$r,$sum);
            for ($i=0;$i<count($status);$i++) {
                $info2=$status[$i];
                $userinfo=$user_controller->GetWholeUserInfo($info2["uid"]);
                $probleminfo=$problem_controller->ListProblemByPid($info2["pid"],true);
                $header=InsertTags("div",array("style"=>InsertInlineCssStyle(array("width"=>"10%","cursor"=>"pointer"))),
                InsertTags("p",array("onclick"=>"location.href='".GetUrl("status",array("id"=>$info2["id"]))."'"),"#".$info2["id"]));
                $header.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"15%","cursor"=>"pointer")),
                "onclick"=>"location.href='".GetUrl("user",array(
                    "id"=>$userinfo["id"]
                ))."'","class"=>"ellipsis"),$userinfo["name"]);
                $header.=InsertTags("p",array(
                    "style"=>InsertInlineCssStyle(array("width"=>"45%","cursor"=>"pointer")),
                    "onclick"=>"location.href='".GetUrl("problem",array(
                        "id"=>$probleminfo["id"]
                    ))."'",
                    "class"=>"ellipsis"
                ),$probleminfo["name"]);
                $header.=InsertTags("div",array("id"=>"whole-status"),GetFormatStatus($info2["status"],true,""));
                $body.=InsertTags("div",array("class"=>"default_main flex","style"=>InsertInlineCssStyle(array(
                    "width"=>"calc( 100% - 30px )",
                    "min-height"=>"50px",
                    "background-color"=>"white",
                    "padding-left"=>"30px",
                    "margin-top"=>"20px",
                    "align-items"=>"center"
                ))),$header);
            } $content=InsertTags("div",array("class"=>"pages","onclick"=>"location.href='".GetUrl("contest",array("id"=>$param["id"],"page"=>"submission","num"=>1))."'",
            "style"=>InsertInlineCssStyle(array("margin-right"=>"10px"))),InsertTags("p",null,"Top"));
            if ($param["num"]==1) $content.=InsertTags("div",array("class"=>"pages banned"),InsertTags("p",null,"Previous"));
            else $content.=InsertTags("div",array("class"=>"pages","onclick"=>"location.href='".GetUrl("contest",array("id"=>$param["id"],"page"=>"submission","num"=>$param["num"]-1))."'"),InsertTags("p",null,"Previous"));
            if ($param["num"]==$page_num) $content.=InsertTags("div",array("class"=>"pages banned"),InsertTags("p",null,"Next"));
            else $content.=InsertTags("div",array("class"=>"pages","onclick"=>"location.href='".GetUrl("contest",array("id"=>$param["id"],"page"=>"submission","num"=>$param["num"]+1))."'"),InsertTags("p",null,"Next"));
            $content.=InsertTags("div",array("class"=>"pages","onclick"=>"location.href='".GetUrl("contest",array("id"=>$param["id"],"page"=>"submission","num"=>$page_num))."'",
            "style"=>InsertInlineCssStyle(array("margin-left"=>"10px"))),InsertTags("p",null,"Bottom"));
            $body.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array(
                "justify-content"=>"center",
                "margin-top"=>"20px"
            ))),$content);
        } break;
        case "ranking": {
            if (!(($uid&&($user_controller->GetWholeUserInfo($uid)["permission"]>1||
            ($info["starttime"]<=time()&&$contest_controller->JudgeSignup($param["id"]))))||$info["starttime"]+$info["duration"]<=time())) 
            Error_Controller::Common("Permission denied");
            $content=""; $ranking=$contest_controller->GetRanking($param["id"]); $rank=0;
            $page_num=intval((count($ranking)+$config["ranking_number"]-1)/$config["ranking_number"]);
            if ($param["num"]==null) $param["num"]=1;
            if ($param["num"]<1) $param["num"]=1;
            if ($param["num"]>$page_num) $param["num"]=$page_num;
            $tmp=""; $uid=$login_controller->CheckLogin();
            $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"10%"))),"Rank");
            $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"20%"))),"Username");
            $percent=70/(2+count($info["problem"]));
            $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"$percent%","text-align"=>"center"))),"Score");
            $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"$percent%","text-align"=>"center"))),"Time");
            for ($i=0;$i<count($info["problem"]);$i++) 
                $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"$percent%","text-align"=>"center"))),"#".($i+1));
            $content.=InsertTags("div",array("class"=>"default_main flex problem-item"),$tmp);
            if ($contest_controller->JudgeSignup($param["id"])) {
                $id=0; for ($i=0;$i<=count($ranking);$i++) {
                    if ($ranking[$i]["uid"]==$uid){$id=$i; break;}
                } $tmp=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"10%"))),"#".($id+1));
                $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"20%","cursor"=>"pointer")),"onclick"=>"location.href='".GetUrl("user",array("id"=>$ranking[$id]["uid"]))."'","class"=>"ellipsis"),$ranking[$id]["name"]);
                $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"$percent%","text-align"=>"center"))),$ranking[$id]["score"]); 
                if ($info["type"]!=0) {$time=intval($ranking[$id]["time"]/60);
                $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"$percent%","text-align"=>"center"))),
                (intval($time/60)<10?"0".intval($time/60):intval($time/60)).":".($time%60<10?"0".$time%60:$time%60));}
                else $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"$percent%","text-align"=>"center"))),round($ranking[$id]["time"]/1000,2)."s");
                for ($i=0;$i<count($info["problem"]);$i++) 
                    if ($ranking[$id]["info"][$i]["id"]!=0) $tmp.=InsertTags("div",array("style"=>InsertInlineCssStyle(array("width"=>"$percent%","text-align"=>"center","cursor"=>"pointer"))),
                    InsertTags("p",array("onclick"=>"location.href='".GetUrl("status",array("id"=>$ranking[$id]["info"][$i]["id"]))."'"),$ranking[$id]["info"][$i]["score"]));
                    else $tmp.=InsertTags("div",array("style"=>InsertInlineCssStyle(array("width"=>"$percent%","text-align"=>"center","cursor"=>"pointer"))),
                    InsertTags("p",null,$ranking[$id]["info"][$i]["score"]));
                $content.=InsertTags("div",array("class"=>"default_main flex problem-item"),$tmp);
            } for ($i=0;$i<count($ranking);$i++) {
                $type=$info["type"]; 
                if ($type==0) {if ($i==0||$ranking[$i]["score"]!=$ranking[$i-1]["score"]) $rank=$i+1;}
                else{if ($i==0||$ranking[$i]["score"]!=$ranking[$i-1]["score"]||$ranking[$i]["time"]!=$ranking[$i-1]["time"]) $rank=$i+1;}
                if ($i<($param["num"]-1)*$config["ranking_number"]||$i>=$param["num"]*$config["ranking_number"]) continue;
                $tmp=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"10%"))),"#$rank");
                $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"20%","cursor"=>"pointer")),"onclick"=>"location.href='".GetUrl("user",array("id"=>$ranking[$i]["uid"]))."'","class"=>"ellipsis"),$ranking[$i]["name"]);
                $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"$percent%","text-align"=>"center"))),$ranking[$i]["score"]); 
                if ($type!=0) {$time=intval($ranking[$i]["time"]/60);
                $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"$percent%","text-align"=>"center"))),
                (intval($time/60)<10?"0".intval($time/60):intval($time/60)).":".($time%60<10?"0".$time%60:$time%60));}
                else $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"$percent%","text-align"=>"center"))),round($ranking[$i]["time"]/1000,2)."s");
                for ($j=0;$j<count($info["problem"]);$j++) 
                    if ($ranking[$i]["info"][$j]["id"]!=0) $tmp.=InsertTags("div",array("style"=>InsertInlineCssStyle(array("width"=>"$percent%","text-align"=>"center","cursor"=>"pointer"))),
                    InsertTags("p",array("onclick"=>"location.href='".GetUrl("status",array("id"=>$ranking[$i]["info"][$j]["id"]))."'"),$ranking[$i]["info"][$j]["score"]));
                    else $tmp.=InsertTags("div",array("style"=>InsertInlineCssStyle(array("width"=>"$percent%","text-align"=>"center","cursor"=>"pointer"))),
                    InsertTags("p",null,$ranking[$i]["info"][$j]["score"]));
                $content.=InsertTags("div",array("class"=>"default_main flex problem-item"),$tmp);
            } $body.=InsertTags("div",null,$content); $content="";
            $content.=InsertTags("div",array("class"=>"pages","onclick"=>"location.href='".GetUrl("contest",array("id"=>$param["id"],"page"=>"ranking","num"=>1))."'",
            "style"=>InsertInlineCssStyle(array("margin-right"=>"10px"))),InsertTags("p",null,"Top"));
            if ($param["num"]==1) $content.=InsertTags("div",array("class"=>"pages banned"),InsertTags("p",null,"Previous"));
            else $content.=InsertTags("div",array("class"=>"pages","onclick"=>"location.href='".GetUrl("contest",array("id"=>$param["id"],"page"=>"ranking","num"=>$param["num"]-1))."'"),InsertTags("p",null,"Previous"));
            if ($param["num"]==$page_num) $content.=InsertTags("div",array("class"=>"pages banned"),InsertTags("p",null,"Next"));
            else $content.=InsertTags("div",array("class"=>"pages","onclick"=>"location.href='".GetUrl("contest",array("id"=>$param["id"],"page"=>"ranking","num"=>$param["num"]+1))."'"),InsertTags("p",null,"Next"));
            $content.=InsertTags("div",array("class"=>"pages","onclick"=>"location.href='".GetUrl("contest",array("id"=>$param["id"],"page"=>"ranking","num"=>$page_num))."'",
            "style"=>InsertInlineCssStyle(array("margin-left"=>"10px"))),InsertTags("p",null,"Bottom"));
            $body.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array(
                "justify-content"=>"center","margin-top"=>"20px"
            ))),$content);
        }break;
    }

    $info["starttime"]=intval($info["starttime"]);
    $info["duration"]=intval($info["duration"]);
    $script.="var d=new Date(); var t=d.getTime()/1000;";
    $script.="if (t<=".$info["starttime"].") document.getElementById(\"time\").style.width=\"0%\";";
    $script.="else if (t>=".($info["starttime"]+$info["duration"]).") document.getElementById(\"time\").style.width=\"100%\";";
    $script.="else document.getElementById(\"time\").style.width=(t-".$info["starttime"].")/".$info["duration"]."*100+\"%\";";
    $script.="setInterval(function(){";
    $script.="var d=new Date(); var t=d.getTime()/1000;";
    $script.="if (t<=".$info["starttime"].") document.getElementById(\"time\").style.width=\"0%\";";
    $script.="else if (t>=".($info["starttime"]+$info["duration"]).") document.getElementById(\"time\").style.width=\"100%\";";
    $script.="else document.getElementById(\"time\").style.width=(t-".$info["starttime"].")/".$info["duration"]."*100+\"%\";";
    $script.="},1000);";
    $script.="function strip_tags_pre(msg){msg=msg.replace(/<(\/)?pre[^>]*>/g,'');return msg;}";
    $script.="function signup(){";
    $script.="var res=SendAjax('".GetAPIUrl("/contest/signup",null)."','POST',{id:".$param["id"]."});";
    $script.="var json=JSON.parse(strip_tags_pre(res)); if (json[\"code\"]) layer.msg(json[\"message\"]);";
    $script.="else {alert('Success!'); window.location.href=window.location.href;}";
    $script.="}";
    $body.=InsertTags("script",null,$script);
}
?>