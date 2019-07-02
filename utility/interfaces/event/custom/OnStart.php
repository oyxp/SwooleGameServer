<?php


namespace interfaces\event\custom;


use interfaces\event\CustomEvent;
use Swoole\WebSocket\Server;

interface OnStart extends CustomEvent
{
    /**onStart事件
     * 在此事件之前Server已进行了如下操作
     * 已创建了manager进程
     * 已创建了worker子进程
     * 已监听所有TCP/UDP/UnixSocket端口，但未开始Accept连接和请求
     * 已监听了定时器
     *
     * @param Server $server
     * @return mixed
     */
    public function handle(Server $server);
}