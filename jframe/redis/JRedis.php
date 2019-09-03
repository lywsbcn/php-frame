<?php
namespace jframe\redis;

class JRedis extends \Redis{

    public function __construct(){
        parent::__construct();

        $this->connect(_RC("redis_host"),_RC("redis_port"));
        $this->auth(_RC("redis_auth"));
        $this->select(_RC("redis_db"));

    }

    public static function instance():JRedis{
        return new JRedis();
    }

}
