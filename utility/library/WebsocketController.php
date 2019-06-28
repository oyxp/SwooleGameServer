<?php


namespace gs;


class WebsocketController
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }


}