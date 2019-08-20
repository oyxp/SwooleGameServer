<?php


namespace gs;


use gs\helper\TimeHelper;
use traits\Singleton;

/**
 * Class Log
 * @package gs
 * @method void info(string $message) static
 * @method void warn(string $message) static
 * @method void error(string $message) static
 */
class Log
{
    use Singleton;

    /**
     *
     */
    const LEVEL_INFO = 1;
    /**
     *
     */
    const LEVEL_WARN = 2;
    /**
     *
     */
    const LEVEL_ERROR = 3;


    /**
     * @var string
     */
    private $logDir;
    /**
     * @var array
     */
    private $config = [
        'rotate_size' => 10 * 1024 * 1024,//10M
        'level'       => 1,//日志记录级别   1:info 2: warn 3: error 4:不输出日志
    ];

    /**
     * Log constructor.
     */
    public function __construct()
    {
        $this->logDir = RUNTIME_PATH . 'log' . DS;
        $this->config = array_merge($this->config, Config::getInstance()->pull('log'));
    }

    /**
     * @param $level
     * @return string
     */
    public function getLevel($level)
    {
        switch ($level) {
            case self::LEVEL_INFO:
                return 'INFO';
                break;
            case self::LEVEL_WARN:
                return 'WARN';
                break;
            case self::LEVEL_ERROR:
                return 'ERROR';
                break;
        }
        return 'UNKOWN';
    }

    /**
     * @param $level
     * @param $message
     */
    private function writeMsg($level, $message)
    {
        if (empty($message)) {
            return;
        }
        //小于当前记录的日志等级
        if ($level < $this->config['level']) {
            return;
        }
        $dir = $this->logDir . date('Ym') . DS . date('d') . DS;
        $log_file = date('H') . '.log';
        $save_file = $dir . $log_file;
        if (!file_exists($dir)) {
            @mkdir($dir, 0777, true);
        } else if (file_exists($save_file) && filesize($save_file) > $this->config['rotate_size']) {
            //如果log size大于10M，日志轮转
            @rename($save_file, $save_file . '-' . TimeHelper::getMillis());
        }
        $prefix = '[' . date('Y-m-d H:i:s') . '][' . strtoupper($this->getLevel($level)) . '] ';
        if ($message instanceof \Throwable) {
            $message = $message->getMessage() . ' in file ' . $message->getFile() . ' at line ' . $message->getLine() . "\n" . $message->getTraceAsString();
        }
        $message = $prefix . $message . PHP_EOL;
        file_put_contents($save_file, $message, FILE_APPEND);
    }


    /**
     * @param $name
     * @param $arguments
     */
    public static function __callStatic($name, $arguments)
    {
        // TODO: Implement __callStatic() method.
        go(function () use ($name, $arguments) {
            $level = self::LEVEL_ERROR;
            switch ($name) {
                case 'info':
                    $level = self::LEVEL_INFO;
                    break;
                case 'warn':
                    $level = self::LEVEL_WARN;
                    break;
                case 'error':
                    $level = self::LEVEL_ERROR;
                    break;
            }
            Log::getInstance()->writeMsg($level, ...$arguments);
        });
    }
}