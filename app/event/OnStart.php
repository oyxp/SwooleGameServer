<?php


namespace app\event;


use interfaces\SwooleEvent;
use Swoole\Server;

class OnStart implements SwooleEvent
{

    public function handle(Server $server)
    {
        // TODO: Implement handle() method.
    }
}