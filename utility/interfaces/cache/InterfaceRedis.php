<?php


namespace interfaces\cache;


interface InterfaceRedis
{
    /**连接redis
     * @return mixed
     */
    public function connect();

    /**调用原生redis api
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function callRedisApi($name, $arguments);

    /**是否连接
     * @return bool
     */
    public function isConnected(): bool;
}