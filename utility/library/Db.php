<?php


namespace gs;


use gs\pool\AbstractPool;
use Medoo\Medoo;
use traits\Singleton;

/**
 * Class Db
 * @package gs
 */
class Db extends AbstractPool
{
    use Singleton;

    /**
     * Db constructor.
     */
    public function __construct()
    {
        $config = Config::getInstance()->pull('database');
        $max = $config['max_size'] ?? 100;
        $min = $config['min_size'] ?? 2;
        unset($config['max_size'], $config['min_size']);
        parent::__construct(Medoo::class, $min, $max, $config);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \Throwable
     */
    public function __call($name, $arguments)
    {
        /** @var Medoo $object */
        return $this->callDbMethod($name, $arguments);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \Throwable
     */
    private function callDbMethod($name, $arguments)
    {
        $object = $this->pop();
        try {
            $ret = call_user_func_array([$object, $name], $arguments);
            $this->recycle($object);
            return $ret;
        } catch (\Throwable $throwable) {
            if ($this->isBreak($throwable)) {
                unset($object);
                $this->create();
                return $this->callDbMethod($name, $arguments);
            }
            $this->recycle($object);
            throw $throwable;
        }
    }

    /**判断数据库是否断线
     * @param \Throwable $e
     * @return bool
     */
    protected function isBreak($e)
    {
        $info = [
            'server has gone away',
            'no connection to the server',
            'Lost connection',
            'is dead or not enabled',
            'Error while sending',
            'decryption failed or bad record mac',
            'server closed the connection unexpectedly',
            'SSL connection has been closed unexpectedly',
            'Error writing data to the connection',
            'Resource deadlock avoided',
            'failed with errno',
        ];
        $error = $e->getMessage();
        foreach ($info as $msg) {
            if (false !== stripos($error, $msg)) {
                return true;
            }
        }
        return false;
    }

}