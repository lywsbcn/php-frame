<?php
namespace jframe;

class Router
{

    public static function init($request, $response = null)
    {

        $path_info = $request->server['path_info'];

        //去除 字符串开头的斜杠 '/'
        $path_info = substr($path_info, 0, 1) == "/" ? substr($path_info, 1) : $path_info;

        //根据 斜杠 切割成数组
        $path_arr = $path_info == "" ? array() : explode("/", $path_info);

        //控制器名称
        $class  = _SC('controller_namespace') . DS . _SC("default_controller");

        //方法名 
        $action = _SC('default_action');

        //参数值列表
        $param = array();

        for ($i = 0; $i < count($path_arr); $i++) {

            if ($i == 0) {
                //index==0 表示控制器 转换控制器首字母大写
                $name = ucwords($path_arr[$i]);

                if (!preg_match('/^[A-Za-z](\w|\.)*$/', $name)) {
                    throw new HttpException(404, 'controller not exists:' . $name);
                    exit;
                }
        
                if(in_array($name,_SC("deny_controller"))){
                    return array("action"=>0,"flag"=>0,"msg"=>"禁止访问该控制器");
                }

                $class = _SC('controller_namespace') . DS . $name;
                continue;
            }

            if (_SC('custom_action') && $i == 1) {
                //(index == 1 表示方法名) 
                $action = $path_arr[$i];
                continue;
            }

            $param[] = $path_arr[$i];
        }

        try {
            $controller = new $class($request);

            if(_SC('allow_param')){                
                return call_user_func_array(array($controller,$action),$param);
            }

            return $controller->$action();

        } catch (\Error $e) {
            $message = !_SC('debug') ? "system error" : $e->getMessage();
            return array("flag" => 0, "msg" => $message,"action"=>0 );
        } catch( \Exception $e){
            $message = !_SC('debug') ? "system error" : $e->getMessage();
            return array("flag" => 0, "msg" => $message,"action"=>0 );
        } 
    }
}
