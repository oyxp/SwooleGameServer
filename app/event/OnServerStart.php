<?php


namespace app\event;


use gs\annotation\Listener;
use Swoole\WebSocket\Server;
use interfaces\event\CustomEvent;

/**
 * Class OnServerStart
 * @package App\event
 * @Listener(CustomEvent::ON_START)
 */
class OnServerStart implements \interfaces\event\custom\OnStart
{
    public function handle(Server $server)
    {
        // TODO: Implement handle() method.
        var_dump(__METHOD__);
    }
}