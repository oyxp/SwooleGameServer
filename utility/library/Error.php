<?php
/**
 * Created by PhpStorm.
 * User: snailzed
 * Date: 2019-06-29
 * Time: 23:00
 */

namespace gs;


use app\App;
use Symfony\Component\Console\Output\ConsoleOutput;

class Error
{

    /**
     * 如果函数返回 FALSE，标准错误处理处理程序将会继续调用。
     *
     * 以下级别的错误不能由用户定义的函数来处理：
     *    E_ERROR
     *    E_PARSE
     *    E_CORE_ERROR
     *    E_CORE_WARNING
     *    E_COMPILE_ERROR
     *    E_COMPILE_WARNING
     *    和在 调用 set_error_handler() 函数所在文件中产生的大多数 E_STRICT
     *
     * 可以处理的错误:
     *   E_USER_ERROR
     *   E_USER_WARNING
     *   E_USER_NOTICE
     *   E_USER_DEPRECATED
     *   E_USER_WARNING
     *   E_WARNING
     *   E_NOTICE
     *   E_DEPRECATED
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return mixed
     */
    public static function handle(int $errno, string $errstr, string $errfile = '', int $errline = 0): bool
    {
        // TODO: Implement handle() method.
        $msg = " {$errstr} in file {$errfile} at line {$errline},errno = {$errno}";
        App::getInstance()->makeInstance(ConsoleOutput::class)->writeln(sprintf(self::getColor($errno), $msg));
        return false;
    }

    /**console颜色  error  info  comment
     * @param $errno
     * @return string
     */
    public static function getColor($errno)
    {
        $time = date('Y-m-d H:i:s');
        switch ($errno) {
            case E_USER_ERROR:
                return $time . ' <error>ERROR:</error> %s';
            case E_USER_WARNING:
            case E_WARNING:
                return $time . ' <comment>WARNING:</comment> %s';
            case E_USER_NOTICE:
            case E_NOTICE:
                return $time . ' <info>NOTICE:</info> %s';
            default :
                return $time . ' <comment>UNKOWN:</comment> %s';
        }
    }
}