<?php


namespace gs;


use gs\pool\AbstractPool;
use traits\Singleton;

/**
 * Class Cache
 * @package gs
 */
class Cache extends AbstractPool
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
        parent::__construct($driver, $config['min_size'], $config['max_size'], $config);
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
        $object = $this->pop();
        try {
            $ret = call_user_func_array([$object, $name], $arguments);
            $this->recycle($object);
            return $ret;
        } catch (\Throwable$throwable) {
            $this->recycle($object);
            throw $throwable;
        }
    }
}