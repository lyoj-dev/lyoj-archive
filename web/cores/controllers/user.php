<?php
class Login_Controller {
    static $db;
    function __construct() {
        self::$db=new Database_Controller;
    }

    /**
     * 核验登录态 CheckLogin
     * @return bool
     */
    static function CheckLogin():bool {
        $uid=$_COOKIE["DedeUserID"];
        if (md5($uid)!=$_COOKIE["DedeUserID__ckMd5"]) return false;
        $csrf_token=$_COOKIE["CSRF_TOKEN"]; $sessdata=$_COOKIE["SESSDATA"];
        $arr=self::$db->Query("SELECT * FROM users WHERE uid=$uid");
        if (!count($arr)) return false;
        $arr=self::$db->Query("SELECT * FROM login_token WHERE uid=$uid AND csrf=$csrf_token AND sessdata=$sessdata");
        if (!count($arr)) return false;
        return true;
    }

    /**
     * 获取当前登录账号id GetLoginID
     * @return int
     */
    static function GetLoginID():int {
        if (!self::CheckLogin()) return 0;
        else return $_COOKIE["DedeUserID"];
    }
}

class User_Controller {
    static $db;
    function __construct() {
        self::$db=new Database_Controller;
    }

    /**
     * 输出api用户信息 OutputAPIInfo
     * @param int $uid 用户id
     * @return array|null
     */
    static function OutputAPIInfo(int $uid):array|null {
        $array=self::$db->Query("SELECT * FROM user WHERE uid=$uid");
        return array("uid"=>$array["id"],"name"=>$array["name"],
        "title"=>$array["title"]);
    }
}
?>