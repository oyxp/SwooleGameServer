<?php
/**
 * Created by PhpStorm.
 * User: snailzed
 * Date: 2019-09-21
 * Time: 23:47
 */

namespace gs\swoole;


use traits\Singleton;

/**
 * Class CoroutineContext
 * @package gs\swoole
 */
class CoroutineContext
{
    use Singleton;
    /**
     * @var array
     */
    private $coroutines = [];

    /**
     * @param $cid
     */
    public function add($cid)
    {
        $this->coroutines[$cid] = true;
    }

    /**
     * @param $cid
     * @return bool
     */
    public function exists($cid)
    {
        return isset($this->coroutines[$cid]);
    }

    /**
     * @param $cid
     */
    public function delete($cid)
    {
        unset($this->coroutines[$cid]);
    }
}