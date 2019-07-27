<?php


namespace interfaces\event\custom;


use interfaces\event\CustomEvent;
use Swoole\WebSocket\Server;

interface OnWorkerStart extends CustomEvent
{
    /**此事件在Worker进程/Task进程启动时发生。这里创建的对象可以在进程生命周期内使用。
     * 可以通过$server->taskworker属性来判断当前是Worker进程还是Task进程
     * 2.1.0版本onWorkerStart回调函数中创建了协程，在onWorkerStart可以调用协程API
     * @param Server $server
     * @param int $worker_id
     * @return mixed
     */
    public function handle(Server $server, int $worker_id);
}