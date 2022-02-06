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
}
?>