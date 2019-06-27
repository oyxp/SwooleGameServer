<?php


namespace gs\console\command;


use app\App;
use gs\Annotation;
use gs\Config;
use gs\Session;
use Swoole\Coroutine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Start
 * @package gs\console\command
 */
class Start extends Command
{
    protected function configure()
    {
        $this->setName('app:start')
            ->setDescription('start sever')
            ->setHelp('start websocket server');
    }

    /**server启动
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = Config::getInstance()->pull('server');
        $server = new \Swoole\WebSocket\Server($config['host'], $config['port'], $config['mode'], $config['sock_type']);
        $server->set($config['setting']);
        App::$swooleServer = $server;
        //设置回调函数
        $this->setServerCallback($server, $config['enable_http']);
        $server->start();
    }

    /**
     * @param \Swoole\WebSocket\Server $server
     * @param bool $enable_http
     */
    protected function setServerCallback(\Swoole\WebSocket\Server $server, $enable_http = true)
    {
        $server->on('message', function (\Swoole\WebSocket\Server $server, $frame) {
            //全协程 1、记录所有执行的redis命令、要执行的redis lua脚本、进行备份的数据库命令、当前请求的数据
            go(function () use ($server, $frame) {
                Coroutine::getContext()->session = new Session($frame);
                try {
//                    $object =
                } catch (\Throwable $throwable) {

                }
            });
        });

        if ($enable_http) {
            $server->on('request', function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
                try {
                    $response->end(1);
                } catch (\Throwable $throwable) {

                }
            });
        }
        $server->on('task', function ($serv, \Swoole\Server\Task $task) {

        });
        $server->on('finish', function (\swoole_server $serv, int $task_id, string $data) {

        });
    }
}