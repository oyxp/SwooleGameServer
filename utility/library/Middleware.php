<?php


namespace gs;


use traits\Singleton;

/**
 * 中间件初始化
 * Class Middleware
 * @package gs
 */
class Middleware
{
    use Singleton;

    /**
     * 中间件执行队列顺序
     * @var array
     */
    private $queue = [];

    /**
     * Middleware constructor.
     */
    public function __construct()
    {
        $this->queue = Annotation::getInstance()->getDefinitions('middleware');
        if (empty($this->queue)) {
            return;
        }
        usort($this->queue, function ($a, $b) {
            return $a['weight'] <=> $b['weight'];
        });
    }

    /**
     * @return array|mixed
     */
    public function getQueue()
    {
        return $this->queue;
    }
}