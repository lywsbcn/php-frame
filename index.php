<?php

include_once 'autoloader.php';
use jframe\ServerRequest;
use jframe\Router;
use jframe\Log;

ServerRequest::AllowCrossDomain();

$request = ServerRequest::Instance()->init();

Log::write($request,"request");

$resp = Router::init($request);

Log::write($resp,"response");

if(is_array($resp) || is_object($resp))
    echo json_encode($resp);
else
    echo $resp;


