<?php
/**
 * Created by PhpStorm.
 * User: snailzed
 * Date: 2019-06-29
 * Time: 23:52
 */

namespace app\event;


use gs\annotation\Listener;
use interfaces\event\swoole\OnWorkerStop;
use Swoole\WebSocket\Server;
use interfaces\event\SwooleEvent;

/**
 * Class WorkerStopListener
 * @package app\event
 * @Listener(SwooleEvent::ON_WORKER_STOP)
 */
class WorkerStopListener implements OnWorkerStop
{

    /**
     * 此事件在Worker进程终止时发生。在此函数中可以回收Worker进程申请的各类资源
     * @param Server $server
     * @param int $worker_id
     * @return mixed
     */
    public function handle(Server $server, int $worker_id)
    {
        // TODO: Implement handle() method.
        var_dump(__METHOD__, $server->taskworker);
    }
}