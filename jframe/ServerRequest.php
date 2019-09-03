<?php
namespace jframe;

class ServerRequest{
    private static $man = NULL;

    public $server;
    public $header;
    public $method;
    public $get;
    public $post;
    public $data;
    public $request;
    public $files;

    function __construct(){
        // $this->init();
    }

    public static function Instance():ServerRequest{
        if(self::$man) return self::$man;
        self::$man = new ServerRequest();
        return self::$man;
    }

    public static function AllowCrossDomain(){
        header("Access-Control-Allow-Origin: *");
    }

    public static function AllowHeaders($headers){
        if(empty($headers)) return;
        header("Access-Control-Allow-Headers:".implode(",",$headers));
    }

    public function init():ServerRequest{        
        $this->server = $this->getServer();
        $this->method = $this->server['method'];
        if($this->method == "OPTIONS") die();
        $this->header = $this->getHeaders();
        $this->data = json_decode($this->rawContent(),true);
        $this->get = $_GET;
        $this->post=$_POST;        
        $this->files = $_FILES;        
        return $this;
    }

    private function getServer(){
        return array(
            "path_info"=>$_SERVER['PATH_INFO'],
            "method"=>$_SERVER['REQUEST_METHOD'],
            "remote_port"=>$_SERVER['REMOTE_PORT'],
            "remote_addr"=>$_SERVER['REMOTE_ADDR'],
            "server_name"=>$_SERVER['SERVER_NAME'],
            "server_port"=>$_SERVER['SERVER_PORT'],
            "server_addr"=>$_SERVER['SERVER_ADDR']
        );
    }
    public function rawContent(){
        return file_get_contents('php://input');
    }
    
    private function getRequest(){
        $req = json_decode(file_get_contents('php://input'),true);

        if(empty($req)) $req = $_POST;
        if(empty($req)) $req = $_GET;

        return $req;
    }

    private function getHeaders(){
               // 忽略获取的header数据。这个函数后面会用到。主要是起过滤作用
       $ignore = array('host','content-length','content-type');

       $headers = array();
       //这里大家有兴趣的话，可以打印一下。会出来很多的header头信息。咱们想要的部分，都是‘http_'开头的。所以下面会进行过滤输出。
       /*    var_dump($_SERVER);
       exit;*/

        foreach($_SERVER as $key=>$value){
            if(substr($key, 0, 5)==='HTTP_'){
            //这里取到的都是'http_'开头的数据。
            //前去开头的前5位
                   $key = substr($key, 5);
                   //把$key中的'_'下划线都替换为空字符串
                   $key = str_replace('_', ' ', $key);
                   //再把$key中的空字符串替换成‘-’
                   $key = str_replace(' ', '-', $key);
                //把$key中的所有字符转换为小写
                   $key = strtolower($key);

            //这里主要是过滤上面写的$ignore数组中的数据
                if(!in_array($key, $ignore)){
                    $headers[$key] = $value;
                }
            }
        }
       //输出获取到的header
       return $headers;
    }
}
