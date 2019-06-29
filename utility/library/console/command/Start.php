<?php


namespace gs\console\command;


use app\App;
use gs\Annotation;
use gs\AppException;
use gs\CmdParser;
use gs\Config;
use gs\RequestContext;
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
        $this->setServerCallback($server, $config);
        $server->start();
    }

    /**
     * @param \Swoole\WebSocket\Server $server
     * @param array $enable_http
     */
    protected function setServerCallback(\Swoole\WebSocket\Server $server, $config)
    {
        $server->on('message', function (\Swoole\WebSocket\Server $server, Frame $frame) use ($config) {
            //全协程 1、要执行的redis lua脚本、进行备份的数据库命令、当前请求的数据 集群要求lua脚本操作的key必须在同一个槽，可以使用{key}方式手动分配
            go(function () use ($server, $frame, $config) {
                //cmd命令格式 {"c":"", "d":{}}  c:命令 d：请求数据
                $data = CmdParser::decode($frame->data, $config['pkg_decode_func']);
                if (empty($data) || !isset($data['c']) || false === ($caller = Annotation::getInstance()->getDefinitions($data['c']))) {
                    $server->push($frame->fd, CmdParser::encode($this->error(
                        -100,
                        $data['c'],
                        'unsupport cmd.'
                    ), $config['pkg_encode_func']), $config['opcode']);
                    return;
                }
                try {
                    $context = new RequestContext($frame->fd, $data);
                    $context->setController($caller['class']);
                    $context->setAction($caller['method']);
                    Coroutine::getContext()->context = $context;
                    $object = new $caller['class']($context);
                    if (method_exists($object, 'prepare')) {
                        call_user_func_array([$object, 'prepare'], [$server]);
                    }
                    $ret = call_user_func([$object, $caller['method']]);
                    $ret['c'] = $data['c'];
                    $server->push($frame->fd, CmdParser::encode($ret, $config['pkg_encode_func']), $config['opcode']);
                } catch (AppException $appException) {
                    $server->push($frame->fd, CmdParser::encode($this->error(
                        $appException->getCode(),
                        $data['c'],
                        $appException->getMessage(),
                        $appException->getData()
                    ), $config['pkg_encode_func']), $config['opcode']);
                } catch (\Throwable $throwable) {
                    $server->push($frame->fd, CmdParser::encode($this->error(
                        -100,
                        $data['c'],
                        'system error.'
                    ), $config['pkg_encode_func']), $config['opcode']);
                }
            });
        });

        if ($config['enable_http']) {
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