<?php

return [
    'host'            => '0.0.0.0',
    'port'            => 8080,
    'mode'            => SWOOLE_PROCESS,
    'sock_type'       => SWOOLE_SOCK_TCP,
    'enable_http'     => env('ENABLE_HTTP', true),//是否开启http
    'opcode'          => WEBSOCKET_OPCODE_TEXT,//WEBSOCKET_OPCODE_BINARY 可以发送text或二进制
    'pkg_encode_func' => 'json_encode',//打包函数
    'pkg_decode_func' => 'json_decode',//解包函数
    'setting'         => [
        'worker_num'               => env('WORKER_NUM', 10),
        'max_request'              => env('MAX_REQUEST', 10000),
        'daemonize'                => env('DAEMONIZE', 0),
        'dispatch_mode'            => env('DISPATCH_MODE', 2),
        'log_file'                 => env('LOG_FILE', RUNTIME_PATH . 'log/swoole.log'),
        'task_worker_num'          => env('TASK_WORKER_NUM', 2),
        'package_max_length'       => env('PACKAGE_MAX_LENGTH', 2048),
        'open_http2_protocol'      => env('OPEN_HTTP2_PROTOCOL', false),
        'ssl_cert_file'            => env('SSL_CERT_FILE', ''),
        'ssl_key_file'             => env('SSL_KEY_FILE', ''),
        'task_ipc_mode'            => env('TASK_IPC_MODE', 1),
        'message_queue_key'        => env('MESSAGE_QUEUE_KEY', 0x70001001),
        'task_tmpdir'              => env('TASK_TMPDIR', RUNTIME_PATH . 'temp'),
        'task_enable_coroutine'    => env('TASK_ENABLE_CO', true),
        'heartbeat_idle_time'      => 300, // 300没有心跳时则断开
        'heartbeat_check_interval' => 60,
    ],
];