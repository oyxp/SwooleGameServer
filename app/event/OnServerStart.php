<?php


namespace app\event;


use Swoole\WebSocket\Server;

/**
 * Class OnServerStart
 * @package App\event
 * @\gs\annotation\OnStart()
 */
class OnServerStart implements \interfaces\event\custom\OnStart
{
    public function handle(Server $server)
    {
        // TODO: Implement handle() method.
        var_dump(__METHOD__);
    }
}