<?php


namespace gs\cache;


use Swoole\Coroutine;

class Redis implements InterfaceRedis
{
    /**
     * @var array|mixed
     */
    private $config = [
        'uri'             => [
            '127.0.0.1:6379',
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
            $this->redis = new \Redis();
            list($host, $port) = explode(':', $this->config['uri'][0]);
            $this->redis->connect($host, $port, $this->config['connect_timeout'], null, 0, $this->config['read_timout']);
        } else {
            $this->redis = new \Swoole\Coroutine\Redis();
            list($host, $port) = explode(':', $this->config['uri'][0]);
            $this->redis->connect($host, $port);
            $this->redis->setOptions([
                'connect_timeout' => $this->config['connect_timeout'],
                'timeout'         => $this->config['read_timout'],
            ]);
        }
        if (!$this->redis) {
            throw new \RedisException('connect redis error.');
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