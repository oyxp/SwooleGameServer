<?php
/**
 * Created by PhpStorm.
 * User: snailzed
 * Date: 2019-06-29
 * Time: 23:31
 */

namespace interfaces\event\swoole;


use interfaces\SwooleEvent;
use Swoole\WebSocket\Server;

interface OnShutDown extends SwooleEvent
{
    /**此事件在Server正常结束时发生，，函数原型
     * @param Server $server
     * @return mixed
     */
    public function handle(Server $server);
}