<?php
class API_Controller{
    /**
     * api输出函数 output
     * @param array $param 输出参数
     * @return void
     */
    static function output(array $param):void {
        $data=array("code"=>0,"message"=>"","data"=>$param,"ttl"=>1);
        echo "<pre style='word-wrap: break-word;white-space: pre-wrap;'>".str_replace("\\/","/",json_encode($data, JSON_UNESCAPED_UNICODE))."</pre>";
        exit;
    }

    /**
     * api错误抛出函数 error_* 
     * @return void
     */
    // 变量名未找到
    static function error_param_not_found(string $param_name):void {
        Error_Controller::Common("Cannot found param \"$param_name\"!",-404,true);
    }
    // 登录态无效
    static function error_login_failed():void {
        Error_Controller::Common("Not Login!",-101,true);
    }
    // 邮箱不存在
    static function error_email_not_exist(string $email):void {
        Error_Controller::Common("Connot found user \"$email\"",-626,true);
    }
    // 用户名或密码错误
    static function error_passwd_wrong():void {
        Error_Controller::Common("Username or password is incorrect!",-629,true);
    }
    // 服务器错误
    static function error_system_crashed():void {
        Error_Controller::Common("System Error!",-500,true);
    }
    // 盐值过期
    static function error_salt_timed_out():void {
        Error_Controller::Common("Salt value timed out! Please try again!",-658,true);
    }
}
?>