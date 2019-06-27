<?php

namespace interfaces;

use Swoole\Server;

interface SwooleEvent
{
    const ON_WORKER_START = 'workerStart';
    const ON_WORKER_STOP = 'wokerStop';
    const ON_SHUT_DOWN = 'shutDown';
    const ON_START = 'start';
    const ON_HAND_SHAKE = 'handshake';
    const ON_OPEN = 'open';

    public function handle(Server $server);
}