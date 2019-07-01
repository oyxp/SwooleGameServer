<?php


namespace interfaces\event\swoole;


use interfaces\SwooleEvent;
use Swoole\WebSocket\Server;

interface OnClose extends SwooleEvent
{
    /**
     * TCP客户端连接关闭后，在worker进程中回调此函数
     * @param Server $server
     * @param int $fd
     * @param int $reactorId
     * @return mixed
     */
    public function handle(Server $server, int $fd, int $reactorId);
}