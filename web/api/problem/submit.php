<?php
    require_once "../require.php";
    CheckParam(array("code","lang","pid"),$_POST);
    $api_controller=new API_Controller;
    $login_controller=new Login_Controller;
    $status_controller=new Status_Controller;
    $user_controller=new User_Controller;
    $problem_controller=new Problem_Controller;
    $config=GetConfig();
    if (!$login_controller->CheckLogin()) $api_controller->error_login_failed();
    $uid=$login_controller->GetLoginID(); 
    $pid=$_POST["pid"]; $lang=$_POST["lang"]; $code=$_POST["code"];
    $sid=$status_controller->Submit($lang,$code,$uid,$pid);
    $api_controller->output(array(
        "id"=>$sid,
        "problem"=>$problem_controller->OutputAPIInfo($pid),
        "lang"=>$config["lang"][$lang],
        "code"=>htmlentities($code)
    ));
?>