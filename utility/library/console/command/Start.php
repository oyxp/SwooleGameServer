<?php


namespace gs\console\command;


use app\App;
use gs\Annotation;
use gs\AppException;
use gs\CmdParser;
use gs\Config;
use gs\Session;
use Swoole\Coroutine;
use Swoole\WebSocket\Frame;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use traits\Response;

/**
 * Class Start
 * @package gs\console\command
 */
class Start extends Command
{
    use Response;

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
        $server->on('message', function (\Swoole\WebSocket\Server $server, Frame $frame) {
            //全协程 1、要执行的redis lua脚本、进行备份的数据库命令、当前请求的数据 集群要求lua脚本操作的key必须在同一个槽，可以使用{key}方式手动分配
            go(function () use ($server, $frame) {
                //cmd命令格式 {"c":"", "d":{}}  c:命令 d：请求数据
                $data = CmdParser::decode($frame->data);
                if (empty($data) || !isset($data['c']) || false === ($caller = Annotation::getInstance()->getDefinitions($data['c']))) {
                    return;
                }
                try {
                    Coroutine::getContext()->session = new Session($frame->fd, $data);
                    $ret = call_user_func([new $caller['class'](), $caller['method']]);
                    $server->push($frame->fd, CmdParser::encode($ret), WEBSOCKET_OPCODE_BINARY);
                } catch (AppException $appException) {
                    //做redis callback ,取消数据库入库操作
                    $server->push($frame->fd, CmdParser::encode($this->error(
                        $appException->getCode(),
                        $data['c'],
                        $appException->getMessage(),
                        $appException->getData()
                    )), WEBSOCKET_OPCODE_BINARY);
                } catch (\Throwable $throwable) {
                    //做redis callback，取消数据库入库操作

                    $server->push($frame->fd, CmdParser::encode($this->error(
                        -100,
                        $data['c'],
                        'system error.'
                    )), WEBSOCKET_OPCODE_BINARY);
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