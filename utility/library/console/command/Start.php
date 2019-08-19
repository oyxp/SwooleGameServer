<?php


namespace gs\console\command;


use app\App;
use gs\Annotation;
use gs\AppException;
use gs\Cache;
use gs\CmdParser;
use gs\Config;
use gs\Db;
use gs\Dispatcher;
use gs\http\Request;
use gs\Log;
use gs\RequestContext;
use gs\swoole\Closure;
use interfaces\event\CustomEvent;
use interfaces\InterfaceProcess;
use Swoole\Coroutine;
use Swoole\Process;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
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

    /**
     * 配置命令
     */
    protected function configure()
    {
        $this->setName('app:start')
            ->addArgument('d')
            ->setDescription('start sever')
            ->setHelp('start websocket server');
    }

    /**server启动
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \ReflectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->isRunning()) {
            $output->writeln('<error>The server is running!</error>');
            return;
        }
        $config = Config::getInstance()->pull('server');
        $server = new \Swoole\WebSocket\Server($config['host'], $config['port'], $config['mode'], $config['sock_type']);
        if ($input->getArgument('d')) {
            $config['setting']['daemonize'] = true;
        }
        $server->set($config['setting']);
        App::$swooleServer = $server;
        //设置回调函数
        $this->setServerCallback($server, $config);
        //添加自定义进程
        $this->addCustomProcess($server, $config['name']);
        $server->start();
    }

    /**
     * @param \Swoole\WebSocket\Server $server
     * @param array $enable_http
     */
    protected function setServerCallback(Server $server, $config)
    {
        //自定义
        $server->on('start', function (Server $server) use ($config) {
            //重命名进程
            if (PHP_OS != 'Darwin') {
                cli_set_process_title($config['name'] . ' master process');
            }
            //触发自定义onstart回调
            $events = Annotation::getInstance()->getDefinitions('custom_event.' . CustomEvent::ON_START);
            foreach ($events as $event) {
                $object = new $event();
                method_exists($object, 'handle') && call_user_func_array([$object, 'handle'], [$server]);
            }
            return true;
        });


        $server->on('managerStart', function (Server $server) use ($config) {
            //重命名进程
            if (PHP_OS != 'Darwin') {
                cli_set_process_title($config['name'] . ' manager process');
            }
            //触发自定义onstart回调
            $events = Annotation::getInstance()->getDefinitions('custom_event.' . CustomEvent::ON_MANAGER_START);
            foreach ($events as $event) {
                $object = new $event();
                method_exists($object, 'handle') && call_user_func_array([$object, 'handle'], [$server]);
            }
        });

        //自定义workerstart
        $server->on('workerStart', function (Server $server, int $worker_id) use ($config) {
            //主要是做重命名worker进程名
            if (PHP_OS != 'Darwin') {
                if ($server->taskworker) {
                    cli_set_process_title($config['name'] . ' task worker process');
                } else {
                    cli_set_process_title($config['name'] . ' worker process');
                }
            }
            //初始化连接池
            if ($config['enable_cache']) {
                Cache::getInstance();
            }
            Db::getInstance();
            //触发自定义onworker start回调
            $events = Annotation::getInstance()->getDefinitions('custom_event.' . CustomEvent::ON_WORKER_START);
            foreach ($events as $event) {
                $object = new $event();
                method_exists($object, 'handle') && call_user_func_array([$object, 'handle'], [$server, $worker_id]);
            }
        });

        $server->on('message', function (Server $server, Frame $frame) use ($config) {
            //全协程 1、要执行的redis lua脚本、进行备份的数据库命令、当前请求的数据 集群要求lua脚本操作的key必须在同一个槽，可以使用{key}方式手动分配
            go(function () use ($server, $frame, $config) {
                //cmd命令格式 {"c":"", "d":{}}  c:命令 d：请求数据
                $data = CmdParser::decode($frame->data, $config['pkg_decode_func']);
                if (empty($data) || !isset($data['c']) || false === ($caller = Annotation::getInstance()->getCommand($data['c']))) {
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
                        call_user_func_array([$object, 'prepare'], [$server, $frame->fd]);
                    }
                    $ret = call_user_func([$object, $caller['method']]);
                    $ret['c'] = $data['c'];
                    if ($server->isEstablished($frame->fd)) {
                        $server->push($frame->fd, CmdParser::encode($ret, $config['pkg_encode_func']), $config['opcode']);
                    }
                } catch (AppException $appException) {
                    if ($server->isEstablished($frame->fd)) {
                        $server->push($frame->fd, CmdParser::encode($this->error(
                            $appException->getCode(),
                            $data['c'],
                            $appException->getMessage(),
                            $appException->getData()
                        ), $config['pkg_encode_func']), $config['opcode']);
                    }
                } catch (\Throwable $throwable) {
                    Log::error($throwable);
                    if ($server->isEstablished($frame->fd)) {
                        $server->push($frame->fd, CmdParser::encode($this->error(
                            -107,
                            $data['c'],
                            'system error.'
                        ), $config['pkg_encode_func']), $config['opcode']);
                    }
                }
            });
        });
        //当enable_coroutine设置为true时，底层自动在onRequest回调中创建协程，开发者无需自行使用go函数创建协程
        if ($config['enable_http']) {
            Dispatcher::getInstance();
            $server->on('request', function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
                try {
                    $request = new Request($request);
                    $response = new \gs\http\Response($response);
                    if ($request->server('request_method') === 'OPTIONS') {
                        $response->getSwooleResponse()->header('Access-Control-Allow-Origin', '*');
                        $response->getSwooleResponse()->header('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization');
                        $response->getSwooleResponse()->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
                        return $response->getSwooleResponse()->end();
                    }
                    $ret = Dispatcher::getInstance()->dispatch($request, $response);
                    return $response->writeJson($this->httpSuccess($ret));
                } catch (AppException $appException) {
                    return $response->writeJson($this->httpError($appException->getCode(), $appException->getMessage(), $appException->getData()));
                } catch (\Throwable $throwable) {
                    Log::error($throwable);
                    $response->withStatus(500);
                    return $response->writeJson($this->httpError(-100, $throwable->getMessage()));
                }
            });
        }
        $server->on('task', function (Server $serv, \Swoole\Server\Task $task) {
            try {
                if (!is_array($task->data)) {
                    throw new \RuntimeException('Error data:' . var_export($task->data, true));
                }
                list($callback, $params) = $task->data;
                //如果是闭包，则直接调用返回
                if ($callback instanceof Closure) {
                    $ret = call_user_func_array($callback, $params);
                } else {
                    $ret = call_user_func_array([$callback, 'handle'], $params);
                }
                $task->finish($ret);
            } catch (\Throwable $throwable) {
                //todo log
                Log::error($throwable);
                $task->finish(false);
            }
        });
        /**
         *异步投递task时，onfinish的data参数是 ontask的finish函数调用的结果返回
         */
        $server->on('finish', function (Server $serv, int $task_id, $data) {
            return $data;
        });
        //这里注册其他事件
        $events = Annotation::getInstance()->getDefinitions('swoole_event');
        if (empty($events)) {
            return;
        }
        foreach ($events as $event => $class) {
            $server->on($event, [new $class(), 'handle']);
        }
    }

    /**判断服务正在运行
     * @return bool
     */
    protected function isRunning(): bool
    {
        $pid_file = Config::getInstance()->get('server.setting.pid_file');
        if (!file_exists($pid_file)) {
            return false;
        }
        $master_pid = intval(file_get_contents($pid_file));
        return Process::kill($master_pid, 0);
    }

    /**
     * 添加自定义进程
     * @param Server $server
     * @throws \ReflectionException
     */
    protected function addCustomProcess(Server $server, string $server_name)
    {
        $process_classes = Annotation::getInstance()->getDefinitions('process');
        foreach ($process_classes as $process_class) {
            $class = $process_class['class'];
            $name = $process_class['name'] ?? $class;
            $name = $server_name . ' ' . $name;
            $co = $process_class['co'] ?? true;
            $ref = new \ReflectionClass($class);
            if ($ref->implementsInterface(InterfaceProcess::class)) {
                $new_process = new Process(function (Process $process) use ($server, $class, $name, $co) {
                    if (PHP_OS != 'Darwin') {
                        $process->name($name);
                    }
                    call_user_func_array([$class, 'handle'], [$server, $process]);
                }, false, SOCK_DGRAM, $co);
                $server->addProcess($new_process);
            }
        }
    }
}