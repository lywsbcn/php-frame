<?php

include_once 'autoloader.php';

use jframe\Router;
use jframe\Log;

$http = new Swoole\Http\Server("0.0.0.0", 9502);

$http->on('request', function ($request, $response) {

    $response->header("Access-Control-Allow-Origin", "*");

    Log::write($request,"request");

    $resp = Router::init($request);

    Log::write($resp,"response");
    
    if(is_array($resp) || is_object($resp))
        $response->end(json_encode($resp));
    else
        $response->end($resp);

});

$http->start();
