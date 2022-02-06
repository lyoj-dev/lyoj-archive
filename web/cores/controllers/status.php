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
    function ListWholeByPid(int $pid):array|null {
        $array=self::$db->Query("SELECT * FROM status WHERE pid=$pid");
        return $array;
    }

    /**
     * 根据题目pid列举正确状态 ListAcceptedByPid
     * @param int $pid 题目pid
     * @return array|null
     */
    function ListAcceptedByPid(int $pid):array|null {
        $array=self::$db->Query("SELECT * FROM status WHERE pid=$pid"); $res=array();
        for ($i=0;$i<count($array);$i++) {
            $array[$i]["result"]=preg_replace('/[[:cntrl:]]/','',$array[$i]["result"]);
            $result=JSON_decode($array[$i]["result"],true)["result"];
            if ($result=="Accepted") $res[]=$array[$i];
        } return $res;
    }

    /**
     * 题目提交函数 Submit
     * @param string $lang 语言id
     * @param string $code 代码
     * @return void
     */
    function Submit(string $lang,string $code):void {

    }
}
?>