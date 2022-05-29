<?php
chdir("/etc/judge/crontab");
require_once "./application.php";
$db=new Database_Controller;
$contest=$db->Query("SELECT * FROM contest");
for ($i=0;$i<count($contest);$i++) {
    $problem=$db->Query("SELECT pid FROM contest_problem WHERE id=".$contest[$i]["id"]);
    $user=$db->Query("SELECT * FROM contest_signup WHERE id=".$contest[$i]["id"]);
    for ($j=0;$j<count($user);$j++) {
        $exist=$db->Query("SELECT * FROM contest_ranking WHERE id=".$contest[$i]["id"]." AND uid=".$user[$j]["uid"]);
        if (count($exist)!=0) {
            $info=$exist[0]["info"]; $sinfo=json_decode($info,true);
            if (count($sinfo)==count($problem)) continue;
        } $info=array(); for ($k=0;$k<count($problem);$k++) $info[]=array("score"=>0,"time"=>0,"id"=>0);
        $db->Execute("DELETE FROM contest_ranking WHERE id=".$contest[$i]["id"]." AND uid=".$user[$j]["uid"]);
        $db->Execute("INSERT INTO contest_ranking (id,uid,score,time,info) VALUES (".
        $contest[$i]["id"].",".$user[$j]["uid"].",0,0,'".json_encode($info,JSON_UNESCAPED_UNICODE)."')");
    } for ($j=0;$j<count($user);$j++) {
        $u=$user[$j]; $c=$contest[$i];
        $r=$db->Query("SELECT * FROM contest_ranking WHERE id=".$c["id"]." AND uid=".$u["uid"])[0];
        $sinfo=json_decode($r["info"],true); $sumtime=0; $sumscore=0;
        // print_r($sinfo);
        for ($k=0;$k<count($problem);$k++) {
            $p=$problem[$k]; $status=$db->Query("SELECT * FROM status WHERE uid=".$u["uid"]." AND pid=".$p["pid"].
            " AND contest=".$c["id"]." AND time>=".$c["starttime"]." AND time<=".($c["starttime"]+$c["duration"])." ORDER BY id DESC");
            $sinfo[$k]["score"]=$sinfo[$k]["time"]=$sinfo[$k]["id"]=0;
            if (count($status)==0) continue;
            $s=$status[0]; if ($s["result"]=="") continue;
            $json=json_decode($s["result"],true);
            if (!array_key_exists("info",$json)||count($json["info"])==0) {
                if ($c["type"]==2) $sinfo[$k]["score"]=0;
                else if ($c["type"]==0&&$c["starttime"]+$c["duration"]>=time()) 
                {$sinfo[$k]["score"]=0; $sinfo[$k]["time"]=0;}
                else $sinfo[$k]["score"]=0;
                $sumtime+=$sinfo[$k]["time"];
                $sumscore+=$sinfo[$k]["score"];
                continue;
            } $score=0; for ($l=0;$l<count($json["info"]);$l++) $score+=$json["info"][$l]["score"];
            $sinfo[$k]["time"]=$s["time"]-$c["starttime"]; $sinfo[$k]["id"]=$s["id"];
            if ($c["type"]==2) $sinfo[$k]["score"]=$s["status"]=="Accepted"?1:0;
            else if ($c["type"]==0&&$c["starttime"]+$c["duration"]>=time()) 
            {$sinfo[$k]["score"]=0; $sinfo[$k]["time"]=0;}
            else $sinfo[$k]["score"]=$score;
            if ($c["type"]!=0) $sumtime+=$sinfo[$k]["time"];
            else if ($c["starttime"]+$c["duration"]<time()) $sumtime+=$json["time"];
            $sumscore+=$sinfo[$k]["score"];
        } if ($c["type"]!=0) $sumtime+=count($db->Query("SELECT id FROM status WHERE contest=".$c["id"].
            " AND uid=".$u["uid"]." AND status!='Accepted' AND time>=".$c["starttime"]." AND time<=".($c["starttime"]+$c["duration"])))*20*60;
        $db->Execute("UPDATE contest_ranking SET score=$sumscore,time=$sumtime,info='".
        json_encode($sinfo,JSON_UNESCAPED_UNICODE)."' WHERE id=".$c["id"]." AND uid=".$u["uid"]); 
    }
}
?>