<?php


namespace gs\swoole;


use Opis\Closure\SerializableClosure;

/**
 * Class Closure
 * @package gs\swoole
 */
class Closure
{
    /**
     * @var \Closure
     */
    private $closure;
    /**
     * @var
     */
    private $serialized;

    /**
     * Closure constructor.
     * @param \Closure $closure
     */
    function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * @return array
     */
    final public function __sleep()
    {
        $serializer = new SerializableClosure($this->closure);
        $this->serialized = serialize($serializer);
        unset($this->closure);
        return ['serialized'];
    }

    /**
     *
     */
    final public function __wakeup()
    {
        $this->closure = unserialize($this->serialized);
    }

    /**
     * @return mixed
     */
    final public function __invoke()
    {
        // TODO: Implement __invoke() method.
        $args = func_get_args();
        return call_user_func($this->closure, ...$args);
    }

    /**
     * @param mixed ...$args
     * @return mixed
     */
    final function call(...$args)
    {
        return call_user_func($this->closure, ...$args);
    }
}