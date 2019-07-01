<?php


namespace app\event;


use gs\annotation\Listener;
use Swoole\WebSocket\Server;
use interfaces\SwooleEvent;

/**
 * Class OnClose
 * @package app\event
 * @Listener(SwooleEvent::ON_CLOSE)
 */
class OnClose implements \interfaces\event\swoole\OnClose
{
    /**
     * TCP客户端连接关闭后，在worker进程中回调此函数
     * @param Server $server
     * @param int $fd
     * @param int $reactorId
     * @return mixed
     */
    public function handle(Server $server, int $fd, int $reactorId)
    {
        // TODO: Implement handle() method.
        var_dump(__METHOD__);
    }
}