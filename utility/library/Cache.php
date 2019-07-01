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

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        $object = $this->pop();
        var_dump($object);
        $ret = call_user_func_array([$object, $name], $arguments);
        $this->recycle($object);
        var_dump($this->getCurrentSize());
        return $ret;
    }
}