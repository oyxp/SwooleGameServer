<?php


namespace gs;


use gs\cache\Redis;
use gs\cache\RedisCluster;
use gs\pool\AbstractChannelPool;
use gs\swoole\CoroutineContext;
use Swoole\Coroutine;
use traits\Singleton;

/**
 * Class Cache
 * @package gs
 */
class Cache extends AbstractChannelPool
{
    use Singleton;

    private $connections = [];

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
     * 如果是http请求或websocket的协程，那么同一个协程使用同一个连接，否则自动调度
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \Throwable
     */
    public function __call($name, $arguments)
    {
        //1、先判断是否已经有了
        $cid = Coroutine::getCid();
        //如果是http或ws请求开启的协程
        if (CoroutineContext::getInstance()->exists($cid)) {
            return $this->_callRedisApi($cid, $name, $arguments);
        }
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
     * @param $cid
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \RedisClusterException
     * @throws \RedisException
     * @throws \Throwable
     */
    private function _callRedisApi($cid, $name, $arguments)
    {
        if (isset($this->connections[$cid])) {
            $object = $this->connections[$cid];
        } else {
            /** @var RedisCluster $object */
            $object = $this->pop();
            //如果是http或ws请求开启的协程
            $this->connections[$cid] = $object;
        }
        //如果该对象无效则重新连接
        if (!$this->isValid($object)) {
            $object->connect();
            $this->connections[$cid] = $object;
        }
        try {
            $ret = call_user_func_array([$object, $name], $arguments);
            return $ret;
        } catch (\Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     *回收连接
     */
    public function recycleConnection()
    {
        $cid = Coroutine::getCid();
        if (isset($this->connections[$cid])) {
            $object = $this->connections[$cid];
            parent::recycle($object);
            unset($this->connections[$cid]);
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