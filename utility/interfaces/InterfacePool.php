<?php


namespace interfaces;


/**连接池
 * Interface InterfacePool
 * @package interfaces
 */
interface InterfacePool
{
    /**创建对象
     * @return mixed
     */
    public function create();

    /**入队
     * @return mixed
     */
    public function push($object);

    /**出队：出队时，需要判断下实例是否有效
     * @return mixed
     */
    public function pop();

    /**回收
     * @param $object
     * @return mixed
     */
    public function recycle($object);

    /**获取当前队列中剩余的数量
     * @return mixed
     */
    public function getCurrentSize(): int;

    /**
     * @return bool
     */
    public function isValid($object): bool;

    /**
     *定时检测，回收多余的实例
     */
    public function intervalCheck(): void;
}