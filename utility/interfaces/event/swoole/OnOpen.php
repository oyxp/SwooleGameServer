<?php
/**
 * Created by PhpStorm.
 * User: snailzed
 * Date: 2019-06-29
 * Time: 23:37
 */

namespace interfaces\event\swoole;


use interfaces\event\SwooleEvent;
use Swoole\Http\Request;
use Swoole\WebSocket\Server;

interface OnOpen extends SwooleEvent
{
    /**当WebSocket客户端与服务器建立连接并完成握手后会回调此函数
     * @param Server $server
     * @param Request $req
     * @return mixed
     */
    public function handle(Server $server, Request $req);
}