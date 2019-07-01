<?php


namespace app;

use gs\Annotation;
use gs\Config;
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
//        set_error_handler();
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
        self::$instances[$class] = new $class($args);
        return self::$instances[$class];
    }
}