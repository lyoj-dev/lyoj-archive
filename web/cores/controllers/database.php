<?php
class Database_Controller {
    static $conn;

    /**
     * 数据库初始化函数(不可调用) __construct
     */
    function __construct() {
        $config=GetConfig();
        mysqli_report(MYSQLI_REPORT_OFF);
        self::$conn=mysqli_connect(
            $config["mysql"]["server"],
            $config["mysql"]["user"],
            $config["mysql"]["passwd"],
            $config["mysql"]["database"]
        ); if (!self::$conn) {
            echo Error_Controller::Common("Failed to connect database");
            exit;
        }
        return;
    } 

    /**
     * 数据库查询函数 Query 
     * @param string $sql SQL代码
     * @return array|null
     */
    static function Query(string|null $sql):array|null {
        if ($sql==null) return null;
        $result=self::$conn->query($sql);
        if (!$result) {
            echo Error_Controller::Common("Failed to query database: ".mysqli_error(self::$conn));
            exit;
        }
        $ret=array();
        while ($row=mysqli_fetch_assoc($result)) 
            $ret[]=$row;
        return $ret;
    } 

    /**
     * 数据库执行函数 Execute
     * @param string $sql SQL代码
     * @return void
     */
    static function Execute(string|null $sql):void {
        if ($sql==null) return;
        if (!self::$conn->query($sql)) {
            echo Error_Controller::Common("Failed to execute database: ".mysqli_error(self::$conn));
            exit;
        };
    }
}
?>