<?php


namespace app\event;

use gs\annotation\Listener;
use interfaces\event\CustomEvent;

/**
 * Class OnStart
 * @package app\event
 * @Listener(CustomEvent::ON_START)
 */
class OnStart implements \interfaces\event\custom\OnStart
{

    /**onStart事件
     * 在此事件之前Server已进行了如下操作
     * 已创建了manager进程
     * 已创建了worker子进程
     * 已监听所有TCP/UDP/UnixSocket端口，但未开始Accept连接和请求
     * 已监听了定时器
     *
     * @param \Swoole\WebSocket\Server $server
     * @return mixed
     */
    public function handle(\Swoole\WebSocket\Server $server)
    {
        // TODO: Implement handle() method.
        var_dump(__METHOD__);
    }
}