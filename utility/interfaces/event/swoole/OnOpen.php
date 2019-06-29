<?php
/**
 * Created by PhpStorm.
 * User: snailzed
 * Date: 2019-06-29
 * Time: 23:37
 */

namespace interfaces\event\swoole;


use interfaces\SwooleEvent;
use Swoole\Http\Response;
use Swoole\WebSocket\Server;

interface OnOpen extends SwooleEvent
{
    /**当WebSocket客户端与服务器建立连接并完成握手后会回调此函数
     * @param Server $server
     * @param Response $req
     * @return mixed
     */
    public function handle(Server $server, Response $req);
}