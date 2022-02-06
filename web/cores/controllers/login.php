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
    function CheckLogin():bool {
        $uid=$_COOKIE["DedeUserID"];
        if (md5($uid)!=$_COOKIE["DedeUserID__ckMd5"]) return false;
        $csrf_token=$_COOKIE["CSRF_TOKEN"]; $sessdata=$_COOKIE["SESSDATA"];
        $arr=$db->Query("SELECT * FROM users WHERE uid=$uid");
        if (!count($arr)) return false;
        $arr=$db->Query("SELECT * FROM login_token WHERE uid=$uid AND csrf=$csrf_token AND sessdata=$sessdata");
        if (!count($arr)) return false;
        return true;
    }
}
?>