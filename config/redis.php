<?php

return [
    'driver'          => 'RedisCluster',
    'uri'             => explode(',', env('REDIS_URI', '127.0.0.1:6379,127.0.0.1:6379')),
    'read_timout'     => 1.5,
    'connect_timeout' => 1.5,
    'max_size'        => 10,//最大连接数
    'min_size'        => 2,//最小连接数
    'persistent'      => true,
];