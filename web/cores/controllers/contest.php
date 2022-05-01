<?php
class Contest_Controller {
    static $db;
    static $login_controller;
    static $user_controller;
    function __construct() {
        self::$db=new Database_Controller;
        self::$login_controller=new Login_Controller;
        self::$user_controller=new User_Controller;
    }

    /**
     * 获取所有比赛信息 GetContest
     * @param float $l=1 左区间
     * @param float $r=1e9 右区间
     * @param bool $api_mode=false 是否开启api模式
     * @return array|null
     */
    function GetContest(float $l=1,float $r=1e9,bool $api_mode=false):array|null {
        $array=self::$db->Query("SELECT * FROM contest WHERE id>=$l AND id<=$r ORDER BY id");
        if ($api_mode) return $array;
        for ($i=0;$i<count($array);$i++) {
            $tmp=self::$db->Query("SELECT id FROM problem WHERE contest=".$array[$i]["id"]);
            $arr=array(); for ($j=0;$j<count($tmp);$j++) $arr[]=$tmp[$j]["id"];
            $dat=array("problem"=>$arr); $array[$i]=array_merge($array[$i],$dat);
        } return $array;
    }

    /**
     * 获取比赛总数 GetContestTotal
     * @return int
     */
    function GetContestTotal():int {
        $array=self::$db->Query("SELECT * FROM contest");
        return count($array);
    }

    /**
     * 判断用户是否报名参加比赛 JudgeSignup
     * @param int $id 比赛id
     * @return bool
     */
    function JudgeSignup(int $id):bool {
        $uid=self::$login_controller->CheckLogin();
        if (!$uid) return false;
        $array=self::$db->Query("SELECT * FROM contest_signup WHERE uid=$uid AND id=$id");
        return count($array)?true:false;
    }

    /**
     * 获取用户报名比赛 GetUserSignup
     * @param int $uid 用户id
     * @return array|null
     */
    static function GetUserSignup(int $uid):array|null {
        return self::$db->Query("SELECT * FROM contest_signup WHERE uid=$uid");
    }

    /**
     * 获取报名人数 GetContestSignupNumber
     * @param int $id 比赛id
     * @return int
     */
    function GetContestSignupNumber(int $id):int {
        $num=self::$db->Query("SELECT * FROM contest_signup WHERE id=$id");
        return count($num);
    }

    /**
     * 获取报名信息 GetContestSignup
     * @param int $id 比赛id
     * @return array|null
     */
    function GetContestSignup(int $id):array|null {
        $array=self::$db->Query("SELECT uid FROM contest_signup WHERE id=$id");
        return $array;
    }

    /**
     * 比赛报名 SignupContest
     * @param int $id 比赛id
     * @return array|null
     */
    function SignupContest(int $id):void {
        $uid=self::$login_controller->CheckLogin(); 
        $array=self::$db->Query("SELECT * FROM contest_signup WHERE id=$id AND uid=$uid");
        if (count($array)) return;
        self::$db->Execute("INSERT INTO contest_signup (id,uid) VALUES ($id,$uid)");
    }

    /**
     * 获取排行榜 GetRanking
     * @param int $id 比赛id
     * @return array|null
     */
    function GetRanking(int $id):array|null {
        $result=self::$db->Query("SELECT * FROM contest_ranking WHERE id=$id ORDER BY score DESC,time");
        for ($i=0;$i<count($result);$i++) {
            $user=self::$user_controller->GetWholeUserInfo($result[$i]["uid"]);
            $result[$i]["name"]=$user["name"];
            $result[$i]["uid"]=$user["id"];
            $result[$i]["info"]=json_decode($result[$i]["info"],true);
        } return $result;
    }

    /**
     * 获取赛时提交 GetContestSubmit
     * @param int $id 比赛id
     * @param float $l=1 左边界
     * @param float $r=1e18 右边界
     * @param int &$sum 题目总数
     * @return array|null
     */
    function GetContestSubmit(int $id,float $l=1,float $r=1e18,int& $sum):array|null {
        $array=self::$db->Query("SELECT * FROM contest WHERE id=$id");
        if ($array==null||count($array)==0) return null;
        $sql="SELECT * FROM status WHERE contest=$id AND time>=".$array[0]["starttime"]
        ." AND time<=".($array[0]["starttime"]+$array[0]["duration"])." ORDER BY id DESC"; 
        $res=self::$db->Query($sql); $endtime=$array[0]["starttime"]+$array[0]["duration"];
        if ($array[0]["type"]==0&&$endtime>=time()) {
            for ($i=0;$i<count($res);$i++) $res[$i]["status"]="Submitted";
        } $sum=count($res); return array_slice($res,$l-1,$r-$l+1);
    }
}
?>