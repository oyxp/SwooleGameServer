<?php


namespace gs\cache;


use gs\Config;
use Swoole\Coroutine;
use Swoole\Coroutine\Redis;

class RedisCluster implements InterfaceRedis
{
    /**
     * @var array|mixed
     */
    private $config = [
        'uri'             => [
            '127.0.0.1:7000',
            '127.0.0.1:7001',
            '127.0.0.1:7002',
            '127.0.0.1:7003',
            '127.0.0.1:7004',
            '127.0.0.1:7005',
        ],
        'read_timout'     => 1.5,
        'connect_timeout' => 1.5,
        'max_size'        => 10,//最大连接数
        'min_size'        => 2,//最小连接数
        'persistent'      => true,
    ];
    /**
     * @var \RedisCluster
     */
    private $redis;

    /**
     * RedisCluster constructor.
     * @throws \RedisClusterException
     * @throws \RedisException
     * @throws \Exception
     */
    public function __construct($config = [])
    {
        if (!extension_loaded('redis')) {
            throw new \Exception('no support redis');
        }
        $this->config = array_merge($this->config, $config);
        $this->connect();
    }

    /**
     * @throws \RedisClusterException
     * @throws \RedisException
     */
    public function connect()
    {
        //如果当前不在协程环境中，则返回-1
        if (-1 !== Coroutine::getCid()) {
            $this->redis = new \RedisCluster(NULL, $this->config['uri'], $this->config['connect_timeout'], $this->config['read_timout'], $this->config['persistent']);
            if (!$this->redis) {
                throw new \RedisException('connect redis error.');
            }
            $this->redis->setOption(\RedisCluster::OPT_SLAVE_FAILOVER, \RedisCluster::FAILOVER_DISTRIBUTE);
        } else {
            $this->redis = new Redis();
            $this->redis->connect($this->config['uri'][array_rand($this->config['uri'])]);
            if (!$this->redis) {
                throw new \RedisException('connect redis error.');
            }
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \RedisException
     * @throws \Throwable
     */
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        try {
            $ret = call_user_func_array([$this->redis, $name], $arguments);
        } catch (\Throwable $throwable) {
            var_dump($throwable->getMessage());
            if (false !== strpos($throwable->getMessage(), 'close')) {
                $this->connect();
                $ret = call_user_func_array([$this->redis, $name], $arguments);
            } else {
                throw $throwable;
            }
        }
        return $ret;
    }

}