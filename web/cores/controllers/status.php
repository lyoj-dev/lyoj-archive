<?php
class Status_Controller{
    static $db;
    function __construct() {
        self::$db=new Database_Controller;
    }

    /**
     * 根据题目pid列举所有状态 ListWholeByPid
     * @param int $pid 题目pid
     * @return array|null  
     */
    static function ListWholeByPid(int $pid):array|null {
        $array=self::$db->Query("SELECT * FROM status WHERE pid=$pid");
        return $array;
    }

    /**
     * 根据题目pid列举正确状态 ListAcceptedByPid
     * @param int $pid 题目pid
     * @return array|null
     */
    static function ListAcceptedByPid(int $pid):array|null {
        $array=self::$db->Query("SELECT * FROM status WHERE pid=$pid"); $res=array();
        for ($i=0;$i<count($array);$i++) {
            $array[$i]["result"]=preg_replace('/[[:cntrl:]]/','',$array[$i]["result"]);
            $result=JSON_decode($array[$i]["result"],true)["result"];
            if ($result=="Accepted") $res[]=$array[$i];
        } return $res;
    }

    /**
     * 列举所有已测评的状态 ListJudgedStatus
     * @return array|null
     */
    static function ListJudgedStatus():array|null {
        return self::$db->Query("SELECT * FROM status");
    }
    
    /**
     * 列举所有未测评的状态 ListJudgingStatus
     * @return array|null
     */
    static function ListJudgingStatus():array|null {
        return self::$db->Query("SELECT * FROM waited_judge");
    }

    /**
     * 题目提交函数 Submit
     * @param string $lang 语言id
     * @param string $code 代码
     * @param int $uid 用户id
     * @param int $pid 题目id
     * @return int
     */
    static function Submit(string $lang,string $code,int $uid,int $pid):int {
        $sid=count(self::ListJudgedStatus())+count(self::ListJudgingStatus())+1;
        self::$db->Execute("INSERT INTO waited_judge (id,uid,pid,lang,code,time,status,ideinfo) VALUES 
        ($sid,$uid,$pid,$lang,'$code',".time().",'Waiting...','NULL')"); return $sid;
    }

    /**
     * 根据测评id获取测评状态 GetJudgeStatusById
     * @param int $id 测评id
     * @return array|null
     */
    static function GetJudgeStatusById(int $id):string|null {
        $array=self::$db->Query("SELECT status FROM waited_judge WHERE id=$id");
        if ($array!=null) return $array[0]["status"];
        $array=self::$db->Query("SELECT result FROM status WHERE id=$id");
        $json=json_decode($array[0]["result"],true);
        return $json["result"];
    }

    /**
     * 根据测评id获取测评结果 GetJudgeResultById
     * @param int $id 测评id
     * @return array|null
     */
    static function GetJudgeResultById(int $id):array|null {
        $array=self::$db->Query("SELECT result FROM status WHERE id=$id");
        return json_decode($array[0]["result"],true);
    }
}
?>