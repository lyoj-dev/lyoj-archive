<?php
class Tags_Controller {
    static $db;
    function __construct() {
        self::$db=new Database_Controller;
    }

    /**
     * 以pid列举Tag信息 ListTagsByPid
     * @param int $pid 题目id
     * @return array|null
     */
    static function ListTagsByPid(int $pid):array|null {
        $array=self::$db->Query("SELECT * FROM tags WHERE pid=$pid");
        return $array;
    }

    /**
     * 获取Tag信息 ListTag
     * @return array|null
     */
    static function ListTag():array|null {
        $array=self::$db->Query("SELECT tagname FROM tags");
        $res=array(); for ($i=0;$i<count($array);$i++) $res[]=$array[$i]["tagname"];
        $res=array_unique($res); $res=array_values($res);
        return $res; 
    }
}
?>