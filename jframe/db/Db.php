<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace jframe\db;

/**
 * ThinkPHP 数据库中间层实现类
 */
class Db {

    static private  $instance   =  array();     //  数据库连接实例
    static private  $_instance  =  null;   //  当前数据库连接实例

    /**
     * 取得数据库类实例
     * @static
     * @access public
     * @param mixed $config 连接配置
     * @return Object 返回数据库驱动类
     */
    static public function getInstance($config=array()) {
        $md5    =   md5(serialize($config));
        if(!isset(self::$instance[$md5])) {
            // 解析连接参数 支持数组和字符串
            $options    =   self::parseConfig($config);
			//var_dump($options);
            // 兼容mysqli
            if('mysqli' == $options['type']) $options['type']   =   'mysql';
            $class  =  DB_NAMESPACE.DS."driver".DS.ucwords(strtolower($options['type']));
            if(class_exists($class)){
                self::$instance[$md5]   =   new $class($options);
            }else{
                // 类没有定义
                E('类没有定义: ' . $class);
            }
        }
        self::$_instance    =   self::$instance[$md5];
        return self::$_instance;
    }

    /**
     * 数据库连接参数解析
     * @static
     * @access private
     * @param mixed $config
     * @return array
     */
    static private function parseConfig($config){
        if(!empty($config)){
            if(is_string($config)) {
                return self::parseDsn($config);
            }
            $config =   array_change_key_case($config);
            $config = array (
                'type'          =>  $config['type'],
                'username'      =>  $config['username'],
                'password'      =>  $config['password'],
                'hostname'      =>  $config['hostname'],
                'hostport'      =>  $config['hostport'],
                'database'      =>  $config['database'],
                'dsn'           =>  isset($config['dsn'])?$config['dsn']:null,
                'params'        =>  isset($config['params'])?$config['params']:null,
                'charset'       =>  isset($config['charset'])?$config['charset']:'utf8',
                'deploy'        =>  isset($config['deploy'])?$config['deploy']:0,
                'rw_separate'   =>  isset($config['rw_separate'])?$config['rw_separate']:false,
                'master_num'    =>  isset($config['master_num'])?$config['master_num']:1,
                'slave_no'      =>  isset($config['slave_no'])?$config['slave_no']:'',
                'debug'         =>  isset($config['debug'])?$config['debug']:APP_DEBUG,
                'lite'          =>  isset($config['lite'])?$config['lite']:false,
            );
        }else {
            $config = array (
                'type'          =>  _DC('type'),
                'username'      =>  _DC('username'),
                'password'      =>  _DC('password'),
                'hostname'      =>  _DC('hostname'),
                'hostport'      =>  _DC('hostport'),
                'database'      =>  _DC('database'),
                'dsn'           =>  _DC('dsn'),
                'params'        =>  _DC('params'),
                'charset'       =>  _DC('charset'),
                'deploy'        =>  _DC('deploy'),
                'rw_separate'   =>  _DC('rw_separate'),
                'master_num'    =>  _DC('master_num'),
                'slave_no'      =>  _DC('slave_no'),
                'lite'          =>  _DC('lite'),
            );
        }
        //print_r($config);
        return $config;
    }

    /**
     * DSN解析
     * 格式： mysql://username:passwd@localhost:3306/DbName?param1=val1&param2=val2#utf8
     * @static
     * @access private
     * @param string $dsnStr
     * @return array
     */
    static private function parseDsn($dsnStr) {
        if( empty($dsnStr) ){return false;}
        $info = parse_url($dsnStr);
        if(!$info) {
            return false;
        }
        $dsn = array(
            'type'      =>  $info['scheme'],
            'username'  =>  isset($info['user']) ? $info['user'] : '',
            'password'  =>  isset($info['pass']) ? $info['pass'] : '',
            'hostname'  =>  isset($info['host']) ? $info['host'] : '',
            'hostport'  =>  isset($info['port']) ? $info['port'] : '',
            'database'  =>  isset($info['path']) ? substr($info['path'],1) : '',
            'charset'   =>  isset($info['fragment'])?$info['fragment']:'utf8',
        );

        if(isset($info['query'])) {
            parse_str($info['query'],$dsn['params']);
        }else{
            $dsn['params']  =   array();
        }
        return $dsn;
     }

    // 调用驱动类的方法
    static public function __callStatic($method, $params){
        return call_user_func_array(array(self::$_instance, $method), $params);
    }
}
