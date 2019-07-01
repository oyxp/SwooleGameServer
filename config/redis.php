<?php

return [
    'driver'          => 'RedisCluster',
    'uri'             => [
        'redis-cluster:7000',
        'redis-cluster:7001',
        'redis-cluster:7002',
        'redis-cluster:7003',
        'redis-cluster:7004',
        'redis-cluster:7005',
    ],
    'read_timout'     => 1.5,
    'connect_timeout' => 1.5,
    'max_size'        => 10,//最大连接数
    'min_size'        => 2,//最小连接数
    'persistent'      => true,
];