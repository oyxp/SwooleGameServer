<?php


namespace interfaces;


use Swoole\Process as SwooleProcess;
use Swoole\WebSocket\Server;

/**所有自定义进程都要实现该接口
 * Interface InterfaceProcess
 * @package interfaces
 */
interface InterfaceProcess
{
    /**注意：这里是自定义进程要实现的方法，该handle方法里面需要阻塞，否则swoole会一直启动该进程
     * @param Server $server
     * @return mixed
     */
    public static function handle(Server $server, SwooleProcess $process);
}