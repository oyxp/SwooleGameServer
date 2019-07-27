<?php

return [
    'driver'              => 'RedisCluster',
    'uri'                 => explode(',', env('REDIS_URI', '192.168.10.83:7001,192.168.10.83:7002,192.168.10.83:7003,192.168.10.84:7001,192.168.10.84:7002,192.168.10.84:7003')),
    'read_timout'         => 1.5,
    'connect_timeout'     => 1.5,
    'max_size'            => 20,//最大连接数
    'min_size'            => 5,//最小连接数
    'max_idel_time'       => 60,//最大闲置时间，超过该时间将自动回收
    'interval_check_time' => 10000,//循环检测时间 ms
    'persistent'          => true,
    'prefix'              => '',//前缀
];