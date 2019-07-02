<?php


namespace app\process;


use gs\annotation\Process;
use interfaces\InterfaceProcess;
use Swoole\WebSocket\Server;
use Swoole\Process as SwooleProcess;

/**
 * Class CustomProcess
 * @package app\process
 */
class CustomProcess implements InterfaceProcess
{
    public static function handle(Server $server, SwooleProcess $process)
    {
        // TODO: Implement handle() method.
        while (true) {
            var_dump(__METHOD__);
            sleep(2);
        }
    }
}