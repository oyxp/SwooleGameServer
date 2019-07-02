<?php
/**
 * Created by PhpStorm.
 * User: snailzed
 * Date: 2019-06-29
 * Time: 23:34
 */

namespace interfaces\event\swoole;


use interfaces\event\SwooleEvent;
use Swoole\WebSocket\Server;

interface OnWorkerStop extends SwooleEvent
{
    /**
     * 此事件在Worker进程终止时发生。在此函数中可以回收Worker进程申请的各类资源
     * @param Server $server
     * @param int $worker_id
     * @return mixed
     */
    public function handle(Server $server, int $worker_id);
}