<?php


namespace app;

use gs\Config;
use traits\Singleton;

class App
{
    use Singleton;
    /**
     * @var
     */
    public static $swooleServer;

    /**
     * @var Config
     */
    private $config;

    public function __construct()
    {
        $this->config = Config::getInstance();
    }

    /**
     *app init
     */
    public function run()
    {
        $this->initLogAndTempDir();
        $this->registerErrorExceptionHandle();
        $this->collectAnnotation();
    }

    public function collectAnnotation()
    {

    }

    /**
     *初始化日志目录
     */
    public function initLogAndTempDir()
    {
        $log_dir = pathinfo($this->config->get('server.setting.log_file'), PATHINFO_DIRNAME);
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        $temp_dir = pathinfo($this->config->get('server.setting.task_tmpdir'), true);
        if (!is_dir($temp_dir)) {
            mkdir($temp_dir, 0755, true);
        }
    }

    /**
     *注册错误处理和异常处理
     */
    public function registerErrorExceptionHandle()
    {
//        set_error_handler();
    }
}