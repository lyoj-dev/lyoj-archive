<?php
class Problem_Controller {
    static $db;

    function __construct() {
        self::$db=new Database_Controller;
    }

    /**
     * 以pid列举题目信息 ListProblemByPid
     * @param float $l=1 pid的左边界
     * @param float $r=1e18 pid的右边界
     * @return array|null
     */
    static function ListProblemByPid(float $l=1,float $r=1e18):array|null {
        $array=self::$db->Query("SELECT * FROM problem WHERE banned=0 AND hidden=0"); $res=array();
        for ($i=$l-1;$i<count($array)&&$i<$r;$i++) $res[]=$array[$i];
        return $res;
    }

    /**
     * 以现存题目列举题目信息 ListProblemByNumber
     * @param float $l=1 题目数量的左边界
     * @param float $r=1e18 题目数量的右边界
     * @return array|null
     */
    static function ListProblemByNumber(float $l=1,float $r=1e18,string $key="",array|null $tag,array|null $diff,float& $num):array|null {
        $diffs=" AND ("; for ($i=0;$i<count($diff);$i++) $diffs.=($i!=0?"OR ":"")."difficult=".$diff[$i]." "; $diffs.=")"; 
        // echo "SELECT * FROM problem WHERE ".($key!=""?"name LIKE '$key' AND ":"")."banned=0 AND hidden=0".(count($diff)!=0?$diffs:""); exit;
        $array=self::$db->Query("SELECT * FROM problem WHERE ".($key!=""?"name LIKE '%$key%' AND ":"")."banned=0 AND hidden=0".(count($diff)?$diffs:""));
        if ($tag==null) {
            $res2=array();
            for ($i=$l-1;$i<count($array)&&$i<$r;$i++) $res2[]=$array[$i];
            $num=count($array); return $res2;
        }
        $tags=" WHERE ("; for ($i=0;$i<count($tag);$i++) $tags.=($i!=0?"OR ":"")."tagname='".$tag[$i]."' "; $tags.=")"; 
        // echo "SELECT pid FROM tags WHERE pid<=$r AND pid>=$l".(count($tag)?$tags:""); exit;
        $array2=self::$db->Query("SELECT pid FROM tags".(count($tag)?$tags:""));
        // echo count($array); exit;
        $a=array(); for ($i=0;$i<count($array2);$i++) $a[]=$array2[$i]["pid"];
        // print_r($a); exit;
        $res=array(); for ($i=0;$i<count($array);$i++) {
            if (array_search($array[$i]["id"],$a)===false) ;
            else $res[]=$array[$i];
        } $res2=array(); $num=count($res);
        for ($i=$l-1;$i<count($res)&&$i<$r;$i++) $res2[]=$res[$i];
        return $res2;
    }

    /**
     * 获取题目总数 GetProblemTotal
     * @return int
     */
    static function GetProblemTotal():int {
        $array=self::$db->Query("SELECT * FROM problem WHERE banned=0 AND hidden=0");
        return $array==null?0:count($array);
    }

    /**
     * 输出api题目信息 OutputAPIInfo
     * @param int $pid 题目id
     * @return array|null
     */
    static function OutputAPIInfo(int $pid):array|null {
        $arr=self::$db->Query("SELECT * FROM problem WHERE id=$pid");
        $arr=$arr[0];
        return array("pid"=>$arr["id"],"name"=>$arr["name"],
        "hidden"=>$arr["hidden"],"banned"=>$arr["banned"],"difficult"=>$arr["difficult"]);
    }
}
?>