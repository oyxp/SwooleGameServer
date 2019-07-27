<?php


namespace gs;


use gs\cache\Redis;
use gs\cache\RedisCluster;
use gs\pool\AbstractChannelPool;
use traits\Singleton;

/**
 * Class Cache
 * @package gs
 */
class Cache extends AbstractChannelPool
{
    use Singleton;

    /**
     * Cache constructor.
     */
    public function __construct()
    {
        if (!extension_loaded('redis')) {
            throw  new \RuntimeException('no support redis');
        }
        $config = Config::getInstance()->pull('redis');
        $driver = '\\gs\\cache\\' . $config['driver'];
        if (!class_exists($driver)) {
            throw new \RuntimeException('cache driver does not exists.');
        }
        parent::__construct($driver, $config['min_size'], $config['max_size'], $config['max_idel_time'], $config['interval_check_time'], $config);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \Throwable
     */
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        /** @var RedisCluster $object */
        $object = $this->pop();
        //如果该对象无效则重新连接
        if (!$this->isValid($object)) {
            $object->connect();
        }
        try {
            $ret = call_user_func_array([$object, $name], $arguments);
            $this->recycle($object);
            return $ret;
        } catch (\Throwable $throwable) {
            $this->recycle($object);
            throw $throwable;
        }
    }

    /**
     * @param Redis|RedisCluster $object
     * @return bool
     */
    public function isValid($object): bool
    {
        return $object->isConnected();
    }
}