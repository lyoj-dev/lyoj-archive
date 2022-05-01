<?php
require_once "../require.php";
$api_controller=new API_Controller;
$login_controller=new Login_Controller;
$user_controller=new User_Controller;
$uid=$login_controller->CheckLogin();
CheckParam(array("data"),$_POST);
if (!$uid) $api_controller->error_login_failed();
$user_controller->UploadBackground($uid,$_POST["data"]);
$api_controller->output(array("uid"=>$uid,"url"=>GetRealUrl("data/user/$uid/background.jpg",null)));
?>