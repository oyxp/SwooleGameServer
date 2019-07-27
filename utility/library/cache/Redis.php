<?php


namespace gs\cache;


use interfaces\cache\InterfaceRedis;
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
        'prefix'          => '',//前缀
    ];
    /**
     * @var \Redis
     */
    private $redis;

    /**是否协程redis
     * @var bool
     */
    private $isCoroutine = false;

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
        if (-1 === Coroutine::getCid()) {
            $this->redis = new \Redis();
            list($host, $port) = explode(':', $this->config['uri'][0]);
            $this->redis->connect($host, $port, $this->config['connect_timeout'], null, 0, $this->config['read_timout']);
            if (!empty($this->config['prefix']) && is_string($this->config['prefix'])) {
                $this->redis->setOption(\Redis::OPT_PREFIX, $this->config['prefix']);
            }
        } else {
            $this->redis = new \Swoole\Coroutine\Redis();
            list($host, $port) = explode(':', $this->config['uri'][0]);
            $this->redis->setOptions([
                'connect_timeout'    => $this->config['connect_timeout'],
                'timeout'            => $this->config['read_timout'],
                'compatibility_mode' => true,
            ]);
            $this->redis->connect($host, $port);
            $this->isCoroutine = true;
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
        return $this->callRedisApi($name, $arguments);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \RedisClusterException
     * @throws \RedisException
     * @throws \Throwable
     */
    public function callRedisApi($name, $arguments)
    {
        try {
            //默认$arguments[0]为redis key
            if ($this->isCoroutine) {
                $arguments[0] = $this->config['prefix'] . $arguments[0];
            }
            $ret = call_user_func_array([$this->redis, $name], $arguments);
        } catch (\Throwable $throwable) {
            if ($this->isBreak($throwable->getMessage())) {
                $this->connect();
                return $this->callRedisApi($name, $arguments);
            } else {
                throw $throwable;
            }
        }
        return $ret;
    }

    /**
     * @param $msg
     * @return bool
     */
    public function isBreak($msg)
    {
        $infos = [
            'went away',
            'close',
            'gone away'
        ];
        foreach ($infos as $info) {
            if (false !== stripos($msg, $info)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isConnected(): bool
    {
        if ($this->isCoroutine) {
            return true;
        }
        try {
            if ('+PONG' !== $this->redis->ping()) {
                throw new \RuntimeException('Connection lost');
            }
            $connected = true;
        } catch (\Throwable $throwable) {
            $connected = false;
        }
        return $connected;
    }
}