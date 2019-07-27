<?php


namespace gs\pool;

use interfaces\InterfacePool;
use SplDoublyLinkedList;

/**池
 * Class AbstractPool
 * @package gs\pool
 */
abstract class AbstractSplQueuePool implements InterfacePool
{
    /**当前存放所有的channel
     * @var \SplQueue
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

    /**当前创建的实例个数
     * @var int
     */
    protected $createNum = 0;

    /**最大等待闲置时间，超出该时间则回收
     * @var int
     */
    protected $idelTime = 60;

    /**循环检测间隔时间
     * @var int
     */
    protected $intervalCheckTime = 60000;

    /**
     * AbstractPool constructor.
     * @param $class
     * @param $min
     * @param $max
     * @param mixed ...$args
     */
    protected function __construct($class, $min, $max, $idelTime, $interval_check_time, ...$args)
    {
        $this->pool = new \SplQueue();
        $this->pool->setIteratorMode(SplDoublyLinkedList::IT_MODE_DELETE);//遍历后删除
        $this->min = $min;
        $this->max = $max;
        $this->idelTime = $idelTime;
        $this->interval_check_time = $interval_check_time;//秒级
        $this->args = $args;
        $this->class = $class;
        //预先创建 $min 个对象
        for ($i = 0; $i < $min; $i++) {
            $this->push($this->create());
        }
    }

    /**创建实例
     * @return mixed
     */
    public function create()
    {
        $object = new $this->class(...($this->args));
        $object->lastUseTime = time();//最后使用时间
        $this->createNum++;
        return $object;
    }

    /**将实例添加到channel
     * @param $object
     * @return mixed
     */
    public function push($object)
    {
        if ($this->createNum > $this->max) {
            return;
        }
        $this->pool->enqueue($object);
    }

    /**
     *从池中获取一个实例
     */
    public function pop()
    {
        if (!$this->pool->isEmpty()) {
            $object = $this->pool->dequeue();
        } else if ($this->createNum < $this->max) {
            $this->push($this->create());
            return $this->pop();
        } else {
            throw new \RuntimeException('Connection pool is full.');
        }
        //如果当前等待时间大于最大等待时间
        if (time() - $object->lastUseTime > $this->idelTime) {
            return $this->pop();
        }
        $object->lastUseTime = time();//最后使用时间
        return $object;
    }

    /**回收实例
     * @param $object
     */
    public function recycle($object)
    {
        $object->lastUseTime = time();//最后使用时间
        $this->push($object);
    }

    /**获取当前闲置的实例数
     * @return mixed
     */
    public function getCurrentSize(): int
    {
        return $this->pool->count();
    }
}