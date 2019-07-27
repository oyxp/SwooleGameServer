<?php


namespace gs;

use Swoole\WebSocket\Server;

class WebsocketController
{
    use \traits\Response;
    /**
     * @var RequestContext
     */
    protected $request;

    public function __construct(RequestContext $request)
    {
        $this->request = $request;
    }

    /**
     * @param \Swoole\WebSocket\Server $server
     * @param int $fd
     */
    public function prepare(Server $server, int $fd)
    {

    }

    /**
     * @return mixed|null
     */
    public function getUid()
    {
        return Session::getUidByFd($this->request->getFd());
    }

    public function sendToUser($uid)
    {

    }
}