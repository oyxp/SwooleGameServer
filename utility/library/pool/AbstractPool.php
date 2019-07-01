<?php


namespace gs\pool;


use app\App;
use Swoole\Coroutine\Channel;

/**池
 * Class AbstractPool
 * @package gs\pool
 */
class AbstractPool
{
    /**当前存放所有的channel
     * @var Channel
     */
    protected $pool;
    /**最小实例数
     * @var
     */
    protected $min;

    /**实例最大数
     * @var
     */
    protected $max;

    /**池的类
     * @var
     */
    protected $class;

    /**构造函数参数
     * @var array|mixed[]
     */
    protected $args;

    /**当前正在被使用的实例个数，用于判断是否超出了当前的最大实例数
     * @var int
     */
    protected $usingNum = 0;

    /**
     * AbstractPool constructor.
     * @param $class
     * @param $min
     * @param $max
     * @param mixed ...$args
     */
    protected function __construct($class, $min, $max, ...$args)
    {
        $this->pool = new Channel($max);
        $this->min = $min;
        $this->max = $max;
        $this->args = $args;
        $this->class = $class;
        //预先创建 $min 个对象
        for ($i = 0; $i < $min; $i++) {
            $this->create();
        }
    }

    /**创建实例
     * @return mixed
     */
    protected function create()
    {
        return $this->push(App::getInstance()->makeInstance($this->class, true, ...($this->args)));
    }

    /**将实例添加到channel
     * @param $object
     * @return mixed
     */
    protected function push($object)
    {
        return $this->pool->push($object);
    }

    /**
     *从池中获取一个实例
     */
    protected function pop()
    {
        $object = $this->pool->pop(0.1);
        if (false === $object) {
            //当前正在使用的数量+闲置的数量 < 最大数
            if (($this->usingNum + $this->getCurrentSize()) < $this->max) {
                $this->create();
                return $this->pop();
            } else {
                throw new \RuntimeException('Pool reach max size.');
            }
        }
        $this->usingNum++;
        return $object;
    }

    /**回收实例
     * @param $object
     */
    protected function recycle($object)
    {
        $this->push($object);
        $this->usingNum--;
    }

    /**获取当前闲置的实例数
     * @return mixed
     */
    protected function getCurrentSize()
    {
        var_dump($this->usingNum);
        return $this->pool->length();
    }
}