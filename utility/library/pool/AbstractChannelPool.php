<?php


namespace gs\pool;


use gs\Log;
use interfaces\InterfacePool;
use Swoole\Coroutine\Channel;

abstract class AbstractChannelPool implements InterfacePool
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
        $this->pool = new Channel($max);
        $this->min = $min;
        $this->max = $max;
        $this->idelTime = $idelTime;
        $this->intervalCheckTime = $interval_check_time;//秒级
        $this->args = $args;
        $this->class = $class;
        //预先创建 $min 个对象
        for ($i = 0; $i < $min; $i++) {
            $this->push($this->create());
        }
        //定时检测
        $this->intervalCheck();
    }

    /**创建对象
     * @return mixed
     */
    public function create()
    {
        if ($this->createNum >= $this->max) {
            return null;
        }
        $object = new $this->class(...($this->args));
        $object->lastUseTime = time();//最后使用时间
        $this->createNum++;
        return $object;
    }

    /**入队
     * @return mixed
     */
    public function push($object)
    {
        if (is_null($object)) {
            return;
        }
        $stat = $this->pool->stats();
        if ($stat['queue_num'] >= $this->max) {
            return;
        }
        $this->pool->push($object);
    }

    /**出队：出队时，需要判断下实例是否有效
     * @return mixed
     */
    public function pop($try_times = 3)
    {
        $object = $this->pool->pop(0.05);
        if (false === $object) {
            if ($try_times <= 0) {
                throw new \RuntimeException('get pool connection timeout!');
            }
            //没有实例，创建
            if ($this->createNum < $this->max) {
                $this->push($this->create());
                return $this->pop(--$try_times);
            } else {
                throw new \RuntimeException('Connection pool is full!');
            }
        }
        $object->lastUseTime = time();//最后使用时间
        return $object;
    }

    /**回收
     * @param $object
     * @return mixed
     */
    public function recycle($object)
    {
        // TODO: Implement recycle() method.
        $object->lastUseTime = time();//最后使用时间
        $this->push($object);
    }

    /**获取当前队列中剩余的数量
     * @return mixed
     */
    public function getCurrentSize(): int
    {
        // TODO: Implement getCurrentSize() method.
        return $this->pool->length();
    }

    /**
     *定时检测，回收多余的实例
     */
    public function intervalCheck(): void
    {
        if ($this->intervalCheckTime > 0) {
            swoole_timer_tick($this->intervalCheckTime, function () {
                $objects = [];
                while (!$this->pool->isEmpty()) {
                    $object = $this->pool->pop(0.01);
                    //超过最大等待时间
                    if (time() - $object->lastUseTime > $this->idelTime) {
                        if (method_exists($object, 'close')) {
                            try {
                                call_user_func_array([$object, 'close'], []);
                            } catch (\Throwable $throwable) {
                                Log::error($throwable);
                            }
                        }
                        unset($object);
                        $this->createNum--;
                        continue;
                    }
                    //有效的实例
                    $objects[] = $object;
                }
                //重新入队
                foreach ($objects as $object) {
                    $this->pool->push($object);
                }
                //判断是否小于最小连接数
                if ($this->createNum < $this->min) {
                    $need_create_num = $this->min - $this->createNum;
                    for ($i = 0; $i < $need_create_num; $i++) {
                        $this->push($this->create());
                    }
                }
            });
        }
    }

    /**
     * 获取状态
     *
     * array(
     * "consumer_num" => 0, 消费者数量，表示当前通道为空，有N个协程正在等待其他协程调用push方法生产数据
     * "producer_num" => 1,  生产者数量，表示当前通道已满，有N个协程正在等待其他协程调用pop方法消费数据
     * "queue_num" => 10   通道中的元素数量
     * );
     * @return mixed
     */
    public function getPoolStatus()
    {
        return $this->pool->stats();
    }

}