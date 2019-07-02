<?php


namespace app\websocket;


use gs\annotation\Command;
use gs\WebsocketController;

class Common extends WebsocketController
{
    /**
     * @Command(200)
     */
    public function heartBeat()
    {
        return $this->success(2);
    }
}