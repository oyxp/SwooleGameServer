<?php
/**
 * Created by PhpStorm.
 * User: snailzed
 * Date: 2019-06-28
 * Time: 23:55
 */

namespace app\websocket;


use gs\annotation\Command;
use gs\AppException;
use gs\Session;
use gs\WebsocketController;

/**
 * Class Index
 * @package app\websocket
 */
class Index extends WebsocketController
{
    /**
     * @Command(100)
     */
    public function index()
    {
        cache()->set('time', time());
        return $this->success(cache()->get('time'));
    }

    /**
     * @Command(101)
     * @throws AppException
     */
    public function testException()
    {
        throw new AppException(1000);
    }

    /**
     * @Command(102)
     * @throws AppException
     */
    public function testException1()
    {
        throw new AppException(1010);
    }

    /**
     * @Command(103)
     * @throws AppException
     */
    public function testException2()
    {
        throw new AppException(1020, [__METHOD__]);
    }

    /**
     * @Command(104)
     */
    public function getUid()
    {
        return $this->success(Session::getUidByFd($this->request->getFd()));
    }
}