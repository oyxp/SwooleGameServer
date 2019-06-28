<?php


namespace gs;


class WebsocketController
{
    use \traits\Response;
    /**
     * @var RequestContext
     */
    private $context;

    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    /**
     * @return RequestContext
     */
    protected function getRequestContext()
    {
        return $this->context;
    }

    public function prepare(\Swoole\WebSocket\Server $server)
    {
    }
}