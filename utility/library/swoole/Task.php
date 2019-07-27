<?php


namespace gs\swoole;


use app\App;
use gs\Annotation;

/**
 * Class Task
 * task任务不能传递闭包Closure
 * @package gs\swoole
 */
class Task
{

    /**
     * 异步任务，必须设置onfinish回调,返回task_worker_id
     * @param mixed $name
     * @param array $data
     * @param int $dst_worker_id
     * @return mixed
     */
    public static function async($name, array $data, int $taskWorkerId = -1)
    {
        if ($name instanceof \Closure) {
            $param = [
                new Closure($name),
                $data
            ];

        } else {
            $task_class = Annotation::getInstance()->getTask($name);
            if (false === $task_class) {
                throw new \RuntimeException(sprintf('The task name [%s] does not exists.', $name));
            }
            $param = [
                $task_class,
                $data,
            ];
        }
        return App::$swooleServer->task($param, $taskWorkerId);
    }

    /**
     * 同步执行任务
     * @param mixed $name
     * @param $data
     * @param int $type
     * @return mixed
     */
    public static function sync($name, array $data, $timeout = 0.5, int $taskWorkerId = -1)
    {
        if ($name instanceof \Closure) {
            $param = [
                new Closure($name),
                $data
            ];

        } else {
            $task_class = Annotation::getInstance()->getTask($name);
            if (false === $task_class) {
                throw new \RuntimeException(sprintf('The task name [%s] does not exists.', $name));
            }
            $param = [
                $task_class,
                $data,
            ];
        }
        return App::$swooleServer->taskwait($param, $timeout, $taskWorkerId);
    }

    /**
     * 并发执行多个task异步任务
     * [
     *  ['任务名|匿名函数', [参数列表]]
     * ]
     * @param array $tasks
     * @param float $timeout
     * @return mixed
     */
    public static function asyncMulti(array $tasks, $timeout = 0.5)
    {
        foreach ($tasks as $k => $task) {
            if (!is_array($task) || count($task) != 2) {
                throw new \RuntimeException('The ele of tasks must be array.task[0]=name,task[1]=params');
            }

            if ($task[0] instanceof \Closure) {
                $tasks[$k][0] = new Closure($task);
            } else if (is_string($task[0]) && false !== ($name = Annotation::getInstance()->getTask($task[0]))) {
                $tasks[$k][0] = $name;
            } else {
                throw new \RuntimeException(sprintf('empty task name : [%s]', $task[0]));
            }
        }
        return App::$swooleServer->taskWaitMulti($tasks, $timeout);
    }

    /**
     * 并发执行Task并进行协程调度
     * 任务完成或超时，返回结果数组。结果数组中每个任务结果的顺序与$tasks对应
     * 某个任务执行失败或超时，对应的结果数组项为false
     * [
     *  ['任务名|匿名函数', [参数列表]]
     * ]
     *
     * @param array $tasks
     * @param float $timeout
     * @return mixed
     */
    public static function coMulti(array $tasks, $timeout = 0.5)
    {
        foreach ($tasks as $k => $task) {
            if (!is_array($task) || count($task) != 2) {
                throw new \RuntimeException('The ele of tasks must be array.task[0]=name,task[1]=params');
            }

            if ($task[0] instanceof \Closure) {
                $tasks[$k][0] = new Closure($task);
            } else if (is_string($task[0]) && false !== ($name = Annotation::getInstance()->getTask($task[0]))) {
                $tasks[$k][0] = $name;
            } else {
                throw new \RuntimeException(sprintf('empty task name : [%s]', $task[0]));
            }
        }
        return App::$swooleServer->taskCo($tasks, $timeout);
    }
}