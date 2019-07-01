<?php

return [
    'default_lang'     => 'zh-cn',//当前语种
    'default_timezone' => 'PRC',//时区设置
    'scan_namespace'   => [

    ],//要扫描注解的目录

    'error_handle'    => [\gs\Error::class, 'handle'],//错误处理类, 必须实现handle方法
    'shutdown_handle' => '',

];