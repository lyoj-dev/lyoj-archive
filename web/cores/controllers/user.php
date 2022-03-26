<?php
class Login_Controller {
    static $db,$user_controller;
    function __construct() {
        self::$db=new Database_Controller;
        self::$user_controller=new User_Controller;
    }

    /**
     * 核验登录态 CheckLogin
     * @return int
     */
    static function CheckLogin():int {
        $config=GetConfig();
        $uid=$_COOKIE["DedeUserID"];
        if (md5($uid)!=$_COOKIE["DedeUserID__ckMd5"]) return false;
        $csrf_token=$_COOKIE["CSRF_TOKEN"]; $sessdata=$_COOKIE["SESSDATA"];
        $arr=self::$db->Query("SELECT * FROM user WHERE id=$uid");
        if (!count($arr)) return false;
        $arr=self::$db->Query("SELECT * FROM logindata WHERE uid=$uid AND csrf='$csrf_token' AND sessdata='$sessdata'");
        if (!count($arr)) return false;
        for ($i=0;$i<count($arr);$i++) if (time()-$arr[$i]["time"]<=$config["web"]["cookie_time"]) return $uid;
        return false;
    }

    /**
     * 获取当前登录账号id GetLoginID
     * @return int
     */
    static function GetLoginID():int {
        if (!self::CheckLogin()) return 0;
        else return $_COOKIE["DedeUserID"];
    }

    /**
     * 获取盐值 UserLoginSalt
     * @param string $email 用户邮箱
     * @return string
     */
    function UserLoginSalt(string $email):string {
        $uid=self::$user_controller->GetEmailId($email);
        if ($uid==0) return "";
        $salt=bin2hex(openssl_random_pseudo_bytes(5));
        $user_array=self::$db->Query("SELECT * FROM user WHERE id=$uid")[0];
        self::$db->Query("UPDATE user SET salt='$salt' WHERE id=$uid");
        self::$db->Query("UPDATE user SET salttime=".time()." WHERE id=$uid");
        return $salt;
    }

    /**
     * 获取公钥 UserLoginPublicKey
     * @return string
     */
    function UserLoginPublicKey():string {
        $config=GetConfig();
        $fp=fopen($config["web"]["rsa_public_key"],"r");
        $public=fread($fp,filesize($config["web"]["rsa_public_key"]));
        fclose($fp); return $public;
    }

    /**
     * 获取私钥 UserLoginPrivateKey
     * @return string
     */
    function UserLoginPrivateKey():string {
        $config=GetConfig();
        $fp=fopen($config["web"]["rsa_private_key"],"r");
        $private=fread($fp,filesize($config["web"]["rsa_private_key"]));
        fclose($fp); return $private;
    }

    /**
     * 用户密码登录并返回用户id UserLoginPasswd
     * @param string $email 用户邮箱
     * @param string $passwd 用户密码
     * @param &$cookie 返回cookie数据
     * @return int
     */
    function UserLoginPasswd(string $email,string $passwd,&$cookie):int {
        $config=GetConfig();
        $private_key=self::UserLoginPrivateKey();
        if ($private_key==null) return -1;
        $uid=self::$user_controller->GetEmailId($email);
        if (!$uid) return -1;
        $array=self::$db->Query("SELECT * FROM user WHERE id=$uid");
        if ($array==null) return -1; $pass=null;
        $pass=openssl_private_decrypt(base64_decode($passwd),$pass,$private_key)?$pass:null;
        if ($pass==null) return -1;
        $csrf_token=bin2hex(openssl_random_pseudo_bytes(10));
        $sessdata=bin2hex(openssl_random_pseudo_bytes(10));
        self::$db->Query("INSERT INTO logindata (uid,csrf,sessdata,time) VALUES ($uid,'$csrf_token','$sessdata',".time().")");
        if ($array[0]["verify"]!=1) return -3;
        if ($array[0]["passwd"].$array[0]["salt"]==$pass) {
            if (time()-$array[0]["salttime"]>20) return -2;
            $cookie=array("DedeUserID"=>$uid,"DedeUserID__ckMd5"=>md5($uid),
            "CSRF_TOKEN"=>$csrf_token,"SESSDATA"=>$sessdata);
            return $uid;
        } else return 0;
    }

    /**
     * 注册用户 UserRegister
     * @param string $name 用户名
     * @param string $email 用户邮箱
     * @param string $passwd 用户密码
     * @return int
     */
    function UserRegister(string $name,string $email,string $passwd):int {
        $private_key=self::UserLoginPrivateKey();
        if ($private_key==null) return -1;
        $uuid=self::$user_controller->GetEmailId($email);
        if ($uuid) return -2; $pass=null;
        $user=self::$db->Query("SELECT id FROM user WHERE name='$name'");
        if (count($user)) return -3;
        $user=self::$db->Query("SELECT id FROM user"); $uid=count($user)+1;
        $pass=openssl_private_decrypt(base64_decode($passwd),$pass,$private_key)?$pass:null;
        $code=bin2hex(openssl_random_pseudo_bytes(50));
        self::$db->Execute("INSERT INTO user (id,name,passwd,email,permission,verify,verify_code) VALUES ($uid,'$name','$pass','$email',1,0,'$code')");
        $config=GetConfig(); 
        $fp=fopen($config["email_content"],"r");
        $content=fread($fp,filesize($config["email_content"]));
        $link=GetHTTPUrl("register",array("uid"=>$uid,"code"=>$code));
        $content=str_replace("\$\$name$$",$name,$content);
        $content=str_replace("\$\$email$$",$email,$content);
        $content=str_replace("\$\$link$$",$link,$content);
        Email_Controller::SendEmail(array($email),"Email verify in ".$config["web"]["name"],$content);
        return $uid;
    }

    /**
     * 用户邮箱验证 UserEmailVerify
     * @param string $id 用户id代码
     * @param string $code 邮箱代码
     * @return void
     */
    function UserEmailVerify(string $id,string $code):void {
        $array=self::$db->Query("SELECT * FROM user WHERE id=$id");
        if (count($array)==0) Error_Controller::Common("User ID $id is not exist!");
        if ($array[0]["verify_code"]!=$code) Error_Controller::Common("User and code is not matched!");
        self::$db->Execute("UPDATE user SET verify=true WHERE id=$id"); 
    }
}

class User_Controller {
    static $db;
    function __construct() {
        self::$db=new Database_Controller;
    }

    /**
     * 获取用户邮箱对应id GetEmailId
     * @param string $email
     * @return int
     */
    function GetEmailId(string $email):int {
        $array=self::$db->Query("SELECT * FROM user WHERE email='$email'");
        if ($array==null) return 0;
        return $array[0]["id"];
    }

    /**
     * 输出api用户信息 OutputAPIInfo
     * @param int $uid 用户id
     * @return array|null
     */
    static function OutputAPIInfo(int $uid):array|null {
        $array=self::$db->Query("SELECT * FROM user WHERE id=$uid");
        if ($array==null) return null;
        return array("uid"=>$array[0]["id"],"name"=>$array[0]["name"],
        "title"=>$array[0]["title"]);
    }

    /**
     * 输出完整用户信息 GetWholeUserInfo
     * @param int $uid 用户id
     * @return array|null
     */
    static function GetWholeUserInfo(int $uid):array|null {
        $array=self::$db->Query("SELECT * FROM user WHERE id=$uid");
        if ($array==null) return null;
        $array=$array[0];  unset($array["salt"]);
        unset($array["salttime"]); unset($array["passwd"]);
        return $array;
    }
}
?>