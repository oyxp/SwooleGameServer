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
        $object = $this->pop();
        try {
            $ret = call_user_func_array([$object, $name], $arguments);
            $this->recycle($object);
            return $ret;
        } catch (\Throwable $throwable) {
            $this->recycle($object);
            throw $throwable;
        }
    }

}