<?php
function run(array $param,string &$html,string &$body):void {
    $config=GetConfig();
    $login_controller=new Login_Controller;
    $uid=$login_controller->CheckLogin();
    $user_controller=new User_Controller;
    $admin_controller=new Admin_Controller;
    if (!$uid) Error_Controller::Common("Permission denied");
    $permission=$user_controller->GetWholeUserInfo($uid)["permission"];
    if ($permission<2) Error_Controller::Common("Permission denied");
    $title=InsertTags("hp",array("style"=>InsertInlineCssStyle(array(
        "padding-left"=>"20px",
        "padding-right"=>"20px",
        "font-size"=>"25px",
        "font-weight"=>"400"
    ))),$config["web"]["name"]." Admin Page").
    InsertTags("p",array("style"=>InsertInlineCssStyle(array(
        "padding-left"=>"20px",
        "padding-right"=>"20px",
        "margin-top"=>"5px",
    )),"id"=>"hitokoto"),"");
    $body.=InsertTags("div",array("class"=>"default_main","style"=>InsertInlineCssStyle(array(
        "padding-top"=>"20px",
        "margin-bottom"=>"20px",
        "padding-bottom"=>"20px",
        "font-size"=>"14px"
    ))),$title);
    $control_panel=InsertTags("button",array("onclick"=>"location.href='".GetUrl("admin",array("page"=>"system"))."'"),"System");
    $control_panel.=InsertTags("button",array("onclick"=>"location.href='".GetUrl("admin",array("page"=>"problem"))."'"),"Problem");
    $control_panel.=InsertTags("button",array("onclick"=>"location.href='".GetUrl("admin",array("page"=>"contest"))."'"),"Contest");
    $control_panel.=InsertTags("button",array("onclick"=>"location.href='".GetUrl("admin",array("page"=>"user"))."'"),"User");
    $control_panel.=InsertTags("button",array("onclick"=>"location.href='".GetUrl("admin",array("page"=>"bash"))."'"),"Bash");
    $body.=InsertTags("div",array("class"=>"default_main flex","style"=>
    InsertInlineCssStyle(array("padding-left"=>"10px"))),$control_panel);
    switch($param["page"]) {
        case "system": system_run($param,$html,$body);break;
        case "problem": problem_run($param,$html,$body); break;
        case "contest": contest_run($param,$html,$body); break;
        case "user": user_run($param,$html,$body); break;
        case "bash": control_run($param,$html,$body); break;
        default: system_run($param,$html,$body); break;
    }
    $script="function hitokoto(obj){";
    $script.="if (obj==null) layer.msg('Failed to load hitokoto!');";
    $script.="else {var content=obj['hitokoto']+'    ——'+(obj['from_who']==null?'':obj['from_who'])+'「'+obj['from']+'」';";
    $script.="document.getElementById('hitokoto').innerHTML=content;}};";
    $script.="SendAjax('".$config["hitokoto_link"]."','GET',null,hitokoto,true);";
    $body.=InsertTags("script",null,$script);
}
function control_run(array $param,string &$html,string &$body):void {}

function user_run(array $param,string &$html,string &$body):void {}

function contest_run(array $param,string &$html,string &$body):void {}

function problem_run(array $param,string &$html,string &$body):void {}

function system_run(array $param,string &$html,string &$body):void {
    $admin_controller=new Admin_Controller;

    $cpuinfo=$admin_controller->GetCPUInfo();
    $cpu=InsertTags("div",array("style"=>InsertInlineCssStyle(array(
        "padding-bottom"=>"20px",
        "padding-right"=>"20px"
    ))),InsertTags("hp",null,"CPU Info"));
    $style=InsertCssStyle(array(".cpu-usage-all",".cpu-usage-system",".cpu-usage-user"),array(
        "flex-grow"=>"2000",
        "background-color"=>"lightgrey",
        "height"=>"10px",
        "border-radius"=>"5px"
    )); $style.=InsertCssStyle(array(".cpu-usage-system"),array(
        "background-color"=>"lightskyblue",
        "transition"=>"width 0.5s"
    )); $style.=InsertCssStyle(array(".cpu-usage-user"),array(
        "background-color"=>"orange",
        "transition"=>"width 0.5s"
    )); $cpu.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%"))),"Name:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","value"=>$cpuinfo["Name"])));
    $cpu.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"Cores:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","value"=>$cpuinfo["Cores"]." Cores")));
    $cpu.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"CPU Usage:&nbsp;".
    InsertTags("div",array("class"=>"cpu-usage-all"),InsertTags("div",array("class"=>"cpu-usage-system","id"=>"cpu-usage-system",
    "style"=>InsertInlineCssStyle(array("width"=>($cpuinfo["Usage"]["us"]+$cpuinfo["Usage"]["sy"])."%"))),
    InsertTags("div",array("class"=>"cpu-usage-user","id"=>"cpu-usage-user","style"=>InsertInlineCssStyle(array(
    "width"=>($cpuinfo["Usage"]["us"]/($cpuinfo["Usage"]["us"]+$cpuinfo["Usage"]["sy"]==0?1:($cpuinfo["Usage"]["us"]+$cpuinfo["Usage"]["sy"]))*100)."%"))),""))).
    InsertTags("div",array("id"=>"cpu-usage"),"&nbsp;".$cpuinfo["Usage"]["us"]."% user | ".$cpuinfo["Usage"]["sy"]."% system | ".(100-$cpuinfo["Usage"]["us"]-$cpuinfo["Usage"]["sy"])."% free"));
    // echo ($cpuinfo["Usage"]["us"]/($cpuinfo["Usage"]["us"]+$cpuinfo["Usage"]["sy"]==0?1:($cpuinfo["Usage"]["us"]+$cpuinfo["Usage"]["sy"]))); exit;
    $body.=InsertTags("div",array("class"=>"default_main","style"=>
    InsertInlineCssStyle(array(
        "padding"=>"20px",
        "margin-bottom"=>"20px",
        "margin-top"=>"20px",
    ))),$cpu);

    $meminfo=$admin_controller->GetMemoryInfo();
    $mem=InsertTags("div",array("style"=>InsertInlineCssStyle(array(
        "padding-bottom"=>"20px",
        "padding-right"=>"20px"
    ))),InsertTags("hp",null,"Memory Info"));
    $style.=InsertCssStyle(array(".usage-all",".usage-use"),array(
        "flex-grow"=>"2000",
        "background-color"=>"lightgrey",
        "height"=>"10px",
        "border-radius"=>"5px"
    )); $style.=InsertCssStyle(array(".usage-use"),array(
        "background-color"=>"orange",
        "transition"=>"width 0.5s"
    )); $mem.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%"))),"Memory Usage:&nbsp;".
    InsertTags("div",array("class"=>"usage-all"),InsertTags("div",array("class"=>"usage-use","id"=>"usage-memory","style"=>
    InsertInlineCssStyle(array("width"=>$meminfo["memPercent"]."%"))),""))
    .InsertTags("div",array("id"=>"memory-usage"),"&nbsp;".$meminfo["memUsed"]."/".$meminfo["memTotal"])
    ); $mem.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"Swap Usage:&nbsp;".
    InsertTags("div",array("class"=>"usage-all"),InsertTags("div",array("class"=>"usage-use","id"=>"usage-swap","style"=>
    InsertInlineCssStyle(array("width"=>$meminfo["swapPercent"]."%"))),""))
    .InsertTags("div",array("id"=>"swap-usage"),"&nbsp;".$meminfo["swapUsed"]."/".$meminfo["swapTotal"])
    ); $body.=InsertTags("div",array("class"=>"default_main","style"=>
    InsertInlineCssStyle(array(
        "padding"=>"20px",
        "margin-bottom"=>"20px",
        "margin-top"=>"20px",
    ))),$mem);

    $diskinfo=$admin_controller->GetDiskInfo();
    $disk=InsertTags("div",array("style"=>InsertInlineCssStyle(array(
        "padding-bottom"=>"20px",
        "padding-right"=>"20px"
    ))),InsertTags("hp",null,"Disk Info"));
    $disk.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%"))),"Disk Usage:&nbsp;".
    InsertTags("div",array("class"=>"usage-all"),InsertTags("div",array("class"=>"usage-use","id"=>"usage-disk","style"=>
    InsertInlineCssStyle(array("width"=>$diskinfo["diskPercent"]."%"))),""))
    .InsertTags("div",array("id"=>"disk-usage"),"&nbsp;".$diskinfo["diskUsed"]."/".$diskinfo["diskTotal"])
    ); $body.=InsertTags("div",array("class"=>"default_main","style"=>
    InsertInlineCssStyle(array(
        "padding"=>"20px",
        "margin-bottom"=>"20px",
        "margin-top"=>"20px",
    ))),$disk);

    $dbinfo=$admin_controller->GetDatabaseInfo();
    $db=InsertTags("div",array("style"=>InsertInlineCssStyle(array(
        "padding-bottom"=>"20px",
        "padding-right"=>"20px"
    ))),InsertTags("hp",null,"Database Info"));
    $db.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%"))),"Version:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","value"=>$dbinfo["version"])));
    $db.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"Status:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","id"=>"db-status","value"=>$dbinfo["status"])));
    $db.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"Login:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","value"=>$dbinfo["info"])));
    $db.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"Database:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","value"=>$dbinfo["db_num"]." databases")));
    $db.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"Table:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","value"=>$dbinfo["table_num"]." tables")));
    foreach($dbinfo as $key=>$value) {
        if (strpos($key,"exist")===false) continue;
        $db.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"Table '".substr($key,6)."':&nbsp;".
        InsertSingleTag("input",array("disabled"=>"disabled","value"=>$value)));
    }
    $body.=InsertTags("div",array("class"=>"default_main","style"=>
    InsertInlineCssStyle(array(
        "padding"=>"20px",
        "margin-bottom"=>"20px",
        "margin-top"=>"20px",
    ))),$db);

    $judgerinfo=$admin_controller->GetJudgeInfo();
    $judger=InsertTags("div",array("style"=>InsertInlineCssStyle(array(
        "padding-bottom"=>"20px",
        "padding-right"=>"20px"
    ))),InsertTags("hp",null,"Judger Info"));
    $judger.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%"))),"Number:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","id"=>"judger-number","value"=>$judgerinfo["num"]." judgers")));
    $judger.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"Online:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","id"=>"judger-online","value"=>$judgerinfo["online"]." judgers")));
    for ($i=0;$i<count($judgerinfo["data"]);$i++) {
        $judger.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"Judger '".$judgerinfo["data"][$i]["name"]."' Key:&nbsp;".
        InsertSingleTag("input",array("disabled"=>"disabled","value"=>$judgerinfo["data"][$i]["id"])));
        $judger.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"Judger '".$judgerinfo["data"][$i]["name"]."' Status:&nbsp;".
        InsertSingleTag("input",array("disabled"=>"disabled","id"=>"judger-$i-status","value"=>$judgerinfo["data"][$i]["online"]?"active (running)":"inactive (dead)")));
    } $body.=InsertTags("div",array("class"=>"default_main","style"=>
    InsertInlineCssStyle(array(
        "padding"=>"20px",
        "margin-bottom"=>"20px",
        "margin-top"=>"20px",
    ))),$judger);

    $phpinfo=$admin_controller->GetPHPInfo();
    $php=InsertTags("div",array("style"=>InsertInlineCssStyle(array(
        "padding-bottom"=>"20px",
        "padding-right"=>"20px"
    ))),InsertTags("hp",null,"PHP Info"));
    $php.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%"))),"Version:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","value"=>$phpinfo["version"])));
    $php.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"Zend Version:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","value"=>$phpinfo["zend_version"])));
    $php.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"Configure Param:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","value"=>$phpinfo["configure"])));
    $php.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"Build System:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","value"=>$phpinfo["build"])));
    $php.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"Build Time:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","value"=>$phpinfo["build_time"])));
    $php.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"Memory:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","value"=>round($phpinfo["memory"]/1024,1)." MB")));
    $php.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"Peak Memory:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","value"=>round($phpinfo["peak_memory"]/1024,1)." MB")));
    $body.=InsertTags("div",array("class"=>"default_main","style"=>
    InsertInlineCssStyle(array(
        "padding"=>"20px",
        "margin-bottom"=>"20px",
        "margin-top"=>"20px",
    ))),$php);

    $crontabinfo=$admin_controller->GetCrontabInfo();
    $crontab=InsertTags("div",array("style"=>InsertInlineCssStyle(array(
        "padding-bottom"=>"20px",
        "padding-right"=>"20px"
    ))),InsertTags("hp",null,"Crontab Info"));
    $crontab.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%"))),"Number:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","id"=>"crontab-number","value"=>count($crontabinfo)." tasks")));
    $tmp=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"10%"))),"Task id");
    $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"20%"))),"Next Execute Time");
    $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"20%"))),"Task Name");
    $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"30%"))),"Execute Command");
    $table=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("margin-top"=>"20px","width"=>"100%","margin-bottom"=>"10px"))),$tmp);
    for ($i=0;$i<count($crontabinfo);$i++) {
        $tmp=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"10%"))),"#".$crontabinfo[$i]["id"]);
        $tmp.=InsertTags("p",array("id"=>"crontab-".$crontabinfo[$i]["id"],"style"=>InsertInlineCssStyle(array("width"=>"20%"))),$crontabinfo[$i]["nexttime"]);
        $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"20%"))),$crontabinfo[$i]["name"]);
        $tmp.=InsertTags("p",array("style"=>InsertInlineCssStyle(array("width"=>"30%","flex-grow"=>"1000"))),$crontabinfo[$i]["command"]);
        $tmp.=InsertTags("p",null,InsertTags("button",array("onclick"=>"cronrun('".$crontabinfo[$i]["id"]."')"),"Run"));
        $table.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","height"=>"35px"))),$tmp);
    } $body.=InsertTags("div",array("class"=>"default_main","style"=>
    InsertInlineCssStyle(array(
        "padding"=>"20px",
        "margin-bottom"=>"20px",
        "margin-top"=>"20px",
    ))),$crontab.$table);

    $sysinfo=$admin_controller->GetSystemInfo();
    $system=InsertTags("div",array("style"=>InsertInlineCssStyle(array(
        "padding-bottom"=>"20px",
        "padding-right"=>"20px"
    ))),InsertTags("hp",null,"System Info"));
    $system.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%"))),"Global Time:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","id"=>"global-time","value"=>$sysinfo["timeGlobal"])));
    $system.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"Server Time:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","id"=>"server-time","value"=>$sysinfo["timeServer"])));
    $system.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"Time Stamp:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","id"=>"time-stamp","value"=>$sysinfo["timeStamp"])));
    $system.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"Time Zone:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","value"=>$sysinfo["timeZone"])));
    $system.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"System Name:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","value"=>$sysinfo["sysOperSys"])));
    $system.=InsertTags("div",array("class"=>"flex","style"=>InsertInlineCssStyle(array("width"=>"100%","padding-top"=>"10px"))),"System Architecture:&nbsp;".
    InsertSingleTag("input",array("disabled"=>"disabled","value"=>$sysinfo["sysProcArch"])));
    $body.=InsertTags("div",array("class"=>"default_main","style"=>
    InsertInlineCssStyle(array(
        "padding"=>"20px",
        "margin-bottom"=>"20px",
        "margin-top"=>"20px",
    ))),$system);

    $script="function strip_tags_pre(msg){msg=msg.replace(/<(\/)?pre[^>]*>/g,'');return msg;}";
    $script.="setInterval(function(){";
    $script.="var cpuinfo=SendAjax('".GetAPIUrl("/admin/cpuinfo")."','GET',null);";
    $script.="cpuinfo=JSON.parse(strip_tags_pre(cpuinfo));";
    $script.="cpu_usage_system=Number(cpuinfo['data']['Usage']['sy'])+Number(cpuinfo['data']['Usage']['us']);";
    $script.="cpu_usage_user=Number(cpuinfo['data']['Usage']['us'])/(cpu_usage_system==0?1:cpu_usage_system)*100;";
    $script.="document.getElementById('cpu-usage-system').style.width=cpu_usage_system+'%';";
    $script.="document.getElementById('cpu-usage-user').style.width=cpu_usage_user+'%';";
    $script.="document.getElementById('cpu-usage').innerHTML='&nbsp;'+cpuinfo['data']['Usage']['us']+'% user | '+cpuinfo['data']['Usage']['sy']+'% system | '+(100-cpu_usage_system).toFixed(1)+'% free'";
    $script.="},2000);";
    $script.="setInterval(function(){";
    $script.="var meminfo=SendAjax('".GetAPIUrl("/admin/meminfo")."','GET',null);";
    $script.="meminfo=JSON.parse(strip_tags_pre(meminfo));";
    $script.="document.getElementById('usage-memory').style.width=meminfo['data']['memPercent']+'%';";
    $script.="document.getElementById('usage-swap').style.width=meminfo['data']['swapPercent']+'%';";
    $script.="document.getElementById('memory-usage').innerHTML='&nbsp;'+meminfo['data']['memUsed']+'/'+meminfo['data']['memTotal'];";
    $script.="document.getElementById('swap-usage').innerHTML='&nbsp;'+meminfo['data']['swapUsed']+'/'+meminfo['data']['swapTotal'];";
    $script.="},2000);";
    $script.="setInterval(function(){";
    $script.="var diskinfo=SendAjax('".GetAPIUrl("/admin/diskinfo")."','GET',null);";
    $script.="diskinfo=JSON.parse(strip_tags_pre(diskinfo));";
    $script.="document.getElementById('usage-disk').style.width=diskinfo['data']['diskPercent']+'%';";
    $script.="document.getElementById('disk-usage').innerHTML='&nbsp;'+diskinfo['data']['diskUsed']+'/'+diskinfo['data']['diskTotal'];";
    $script.="},2000);";
    $script.="setInterval(function(){";
    $script.="var dbinfo=SendAjax('".GetAPIUrl("/admin/dbstatus")."','GET',null);";
    $script.="dbinfo=JSON.parse(strip_tags_pre(dbinfo));";
    $script.="document.getElementById('db-status').value=dbinfo['data']['status'];";
    $script.="},2000);";
    $script.="setInterval(function(){";
    $script.="var judgerinfo=SendAjax('".GetAPIUrl("/admin/judgerinfo")."','GET',null);";
    $script.="judgerinfo=JSON.parse(strip_tags_pre(judgerinfo));";
    $script.="document.getElementById('judger-number').value=judgerinfo['data']['num']+' judgers';";
    $script.="document.getElementById('judger-online').value=judgerinfo['data']['online']+' judgers';";
    $script.="for (i=0;i<judgerinfo['data']['data'].length;i++) ";
    $script.="document.getElementById('judger-'+i+'-status').value=judgerinfo['data']['data'][i]['online']?'active (running)':'inactive (dead)';";
    $script.="},2000);";
    $script.="setInterval(function(){";
    $script.="var crontabinfo=SendAjax('".GetAPIUrl("/admin/crontabinfo")."','GET',null);";
    $script.="crontabinfo=JSON.parse(strip_tags_pre(crontabinfo));";
    $script.="document.getElementById('crontab-number').value=crontabinfo['data'].length+' tasks';";
    $script.="for (i=0;i<crontabinfo['data'].length;i++) ";
    $script.="document.getElementById('crontab-'+crontabinfo['data'][i]['id']).innerHTML=crontabinfo['data'][i]['nexttime'];";
    $script.="},2000);";
    $script.="function cronrun(id){";
    $script.="var res=SendAjax('".GetAPIUrl("/admin/cronrun")."','POST',{id:id});";
    $script.="res=strip_tags_pre(res);";
    $script.="res=JSON.parse(res); alert(res['code']==0?'Success!':res['message']);}";
    $script.="setInterval(function(){";
    $script.="var sysinfo=SendAjax('".GetAPIUrl("/admin/sysinfo")."','GET',null);";
    $script.="sysinfo=JSON.parse(strip_tags_pre(sysinfo));";
    $script.="document.getElementById('global-time').value=sysinfo['data']['timeGlobal'];";
    $script.="document.getElementById('server-time').value=sysinfo['data']['timeServer'];";
    $script.="document.getElementById('time-stamp').value=sysinfo['data']['timeStamp'];";
    $script.="},1000);";
    $body.=InsertTags("style",null,$style);
    $body.=InsertTags("script",null,$script);
}
?>