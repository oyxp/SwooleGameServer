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
use gs\WebsocketController;

class Index extends WebsocketController
{
    /**
     * @Command(100)
     */
    public function index()
    {
        var_dump(__METHOD__);
        return $this->success($this->getRequestContext()->getFd());
    }

    /**
     * @Command(101)
     * @throws AppException
     */
    public function testException()
    {
        throw new AppException(1000);
    }
}