<?php
//根路径
define('ROOT',str_replace('\\','/',substr(dirname(__FILE__),0)));

//用户配置
define('CUSTORM_CONFIG',ROOT."/app/config/config.php");
//配置文件夹 路径
define("CONFIG_PATH",ROOT."/config/");
//配置 文件路径
define("CONFIG",CONFIG_PATH."config.php");
//数据库配置 文件路径
define("DATABASE",CONFIG_PATH."database.php");
//redis配置 文件路径
define("REDIS_CONFIG",CONFIG_PATH."redis.php");

//加载配置
include_once 'jframe/init.php';

//自动引入
function __autoload($name){
    $name = str_replace("\\","/",$name);
    $file = "$name.php";
    if(file_exists($file)){
        include_once $file;
    }else{
        throw new Exception("include faild: $file not exsits");
    }
}

set_error_handler('catch_error');
function catch_error($type, $message, $file, $line)
{
    throw new \Exception($message);
}