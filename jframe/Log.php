<?php
namespace jframe;

class Log {

    public static function write($data,$type){
        if(!_SC("write_log")) return;

        $path = ROOT . _SC("log_path");

        if(!file_exists($path)){
            mkdir($path);
        }

        $file = $path . date("Ymd").".log";
        $data = json_encode($data,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);

        $text = date("Y-m-d H:i:s").": $type => ".$data."\n\n";

        file_put_contents($file,$text,FILE_APPEND);

    }

}
