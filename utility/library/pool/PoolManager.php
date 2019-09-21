<?php
/**
 * Created by PhpStorm.
 * User: snailzed
 * Date: 2019-09-21
 * Time: 22:47
 */

namespace gs\pool;


abstract class PoolManager extends AbstractChannelPool
{
    public function __construct($class, $min, $max, $idelTime, $interval_check_time, $args)
    {
        parent::__construct($class, $min, $max, $idelTime, $interval_check_time, $args);
    }


}