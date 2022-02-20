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
        return self::$db->Query("SELECT * FROM problem WHERE banned=0 AND hidden=0 AND id<=$r AND id>=$l");
    }

    /**
     * 以现存题目列举题目信息 ListProblemByNumber
     * @param float $l=1 题目数量的左边界
     * @param float $r=1e18 题目数量的右边界
     * @return array|null
     */
    static function ListProblemByNumber(float $l=1,float $r=1e18):array|null {
        $array=self::$db->Query("SELECT * FROM problem WHERE banned=0 AND hidden=0"); $res=array();
        for ($i=$l-1;$i<count($array)&&$i<$r;$i++) $res[]=$array[$i];
        return $res;
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