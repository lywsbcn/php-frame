<?php
return array(
    
    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    //控制器的命名空间 (路径)
    "controller_namespace"   => "app\controller",

    //默认控制器名称
    "default_controller"     => "Index",

    //控制器默认调用的方法
    "default_action"         => "index",

    //禁止调用的 控制器
    "deny_controller"      => array("Upload"),

    /**
     * 是否自定义方法
     * 如果==true   path_info 第二段表示方法名,之后表示参数
     * 如果==false  path_info 第二段开始表示参数
     */
    "custom_action"          => true,

    //是否允许传参,path_info 表示参数的部分自动传入方法
    "allow_param"            => true,

    //模型的命名空间
    "model_namespance"       => "app\model",

    //是否记录日志
    "write_log"              => true,

    //日志保存的路径
    "log_path"               => "/log/",

    //自定义方法文件路径
    'custom_function'        => '/app/common/Function.php'
);