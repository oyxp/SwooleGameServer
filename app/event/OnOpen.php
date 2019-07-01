<?php


namespace app\event;


use gs\annotation\Listener;
use Swoole\Http\Request;
use Swoole\WebSocket\Server;
use interfaces\SwooleEvent;

/**
 * Class OnOpen
 * @package app\event
 * @Listener(SwooleEvent::ON_OPEN)
 */
class OnOpen implements \interfaces\event\swoole\OnOpen
{

    /**当WebSocket客户端与服务器建立连接并完成握手后会回调此函数
     * @param Server $server
     * @param Request $req
     * @return mixed
     */
    public function handle(Server $server, Request $req)
    {
        // TODO: Implement handle() method.
        var_dump(__METHOD__);
    }
}