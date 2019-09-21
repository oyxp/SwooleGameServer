<?php


namespace app\event;


use gs\annotation\Listener;
use gs\Session;
use Swoole\Coroutine;
use Swoole\WebSocket\Server;
use interfaces\event\SwooleEvent;

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
        var_dump(__METHOD__, 'CLSOE_CID:' . Coroutine::getCid());
        var_dump(cache()->get('time'));
        Session::closeFd($fd);
    }
}