<?php
/**
 * Created by PhpStorm.
 * User: snailzed
 * Date: 2019-06-29
 * Time: 23:31
 */

namespace app\event;


use gs\annotation\Listener;
use Swoole\WebSocket\Server;
use interfaces\event\SwooleEvent;

/**
 * Class OnShutDown
 * @package app\event
 * @Listener(SwooleEvent::ON_SHUT_DOWN)
 */
class OnShutDown implements \interfaces\event\swoole\OnShutDown
{

    /**此事件在Server正常结束时发生，，函数原型
     * @param Server $server
     * @return mixed
     */
    public function handle(Server $server)
    {
        // TODO: Implement handle() method.
        var_dump(__METHOD__);
    }
}