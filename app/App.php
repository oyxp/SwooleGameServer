<?php


namespace app;

use gs\Annotation;
use gs\Cache;
use gs\Config;
use gs\Db;
use gs\Error;
use Symfony\Component\Console\Output\ConsoleOutput;
use traits\Singleton;

class App
{
    use Singleton;
    /**
     * @var
     */
    public static $swooleServer;
    /**所有单例对象
     * @var array
     */
    private static $instances = [];

    /**
     * @var Config
     */
    private $config;

    public function __construct()
    {
        //初始化公共函数库
        require_once LIB_PATH . 'helper.php';
        require_once APP_PATH . 'common.php';

        //初始化配置
        $this->config = Config::getInstance();
        date_default_timezone_set($this->config->get('default_timezone'));
    }

    /**
     *app init
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public function run()
    {
        $this->initLogAndTempDir();
        $this->registerErrorExceptionHandle();
        $this->collectAnnotation();
//        $this->initPool();
    }

    /**收集注解
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public function collectAnnotation()
    {
        Annotation::getInstance()->collectDefinitions();
    }

    /**
     *初始化连接池
     */
    public function initPool()
    {
        Cache::getInstance();
        Db::getInstance();
    }

    /**
     *初始化日志目录
     */
    public function initLogAndTempDir()
    {
        //创建日志目录
        $log_dir = pathinfo($this->config->get('server.setting.log_file'), PATHINFO_DIRNAME);
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        //创建临时目录
        $temp_dir = pathinfo($this->config->get('server.setting.task_tmpdir'), PATHINFO_DIRNAME);
        if (!is_dir($temp_dir)) {
            mkdir($temp_dir, 0755, true);
        }
        //创建pid存放目录
        $pid_dir = pathinfo($this->config->get('server.setting.pid_file'), PATHINFO_DIRNAME);
        if (!is_dir($pid_dir)) {
            mkdir($pid_dir, 0755, true);
        }
    }

    /**
     *注册错误处理和异常处理 关闭函数
     */
    public function registerErrorExceptionHandle()
    {
        //开启错误显示
        ini_set('display_errors', 'on');
        error_reporting(E_ALL | E_STRICT);
        //错误处理
        $error_handle = $this->config->get('error_handle');
        if (!is_callable($error_handle)) {
            $error_handle = function (int $errno, string $errmsg, string $errfile = '', int $errline = 0) {
                $this->makeInstance(ConsoleOutput::class)->writeln('<comment>' . "{$errmsg} in {$errfile} at line {$errline}" . '</comment>');
            };
        }
        set_error_handler($error_handle);

        $shutdown_func = $this->config->get('shutdown_handle');
        if (!is_callable($shutdown_func)) {
            $shutdown_func = function () {
                $error = error_get_last();
                if (!empty($error)) {
                    $this->makeInstance(ConsoleOutput::class)->writeln('<error>' . "{$error['message']} in {$error['file']} at line {$error['line']}" . '</error>');
                }
            };
        }
        register_shutdown_function($shutdown_func);
    }

    /**获取对象
     * @param $class
     * @param bool $newInstance
     * @param mixed ...$args
     * @return mixed
     */
    public function makeInstance($class, $newInstance = false, ...$args)
    {
        if (!$newInstance && isset(self::$instances[$class])) {
            return self::$instances[$class];
        }
        self::$instances[$class] = new $class(...$args);
        return self::$instances[$class];
    }
}