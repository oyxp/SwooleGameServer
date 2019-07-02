<?php
/**
 * Created by PhpStorm.
 * User: snailzed
 * Date: 2019-06-29
 * Time: 23:35
 */

namespace interfaces\event\swoole;


use interfaces\event\SwooleEvent;
use Swoole\Http\Request;
use Swoole\Http\Response;

interface OnHandShake extends SwooleEvent
{
    /**
     * WebSocket建立连接后进行握手。WebSocket服务器已经内置了handshake，如果用户希望自己进行握手处理，可以设置onHandShake事件回调函数。
     * 设置onHandShake回调函数后不会再触发onOpen事件，需要应用代码自行处理
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function handle(Request $request, Response $response);
}