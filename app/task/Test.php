<?php


namespace app\task;


use gs\annotation\Task;

/**
 * Class Test
 * @package app\task
 * @Task(name="test")
 */
class Test
{
    public static function handle($hi)
    {
        var_dump($hi);
    }
}