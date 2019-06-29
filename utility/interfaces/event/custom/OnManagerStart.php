<?php
/**
 * Created by PhpStorm.
 * User: snailzed
 * Date: 2019-06-30
 * Time: 00:24
 */

namespace interfaces\event\custom;


use interfaces\CustomEvent;
use Swoole\WebSocket\Server;

interface OnManagerStart extends CustomEvent
{
    public function handle(Server $server);
}