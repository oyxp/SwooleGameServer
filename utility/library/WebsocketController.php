<?php


namespace gs;

use Swoole\Coroutine;
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

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        //释放绑定的连接
        $cid = Coroutine::getCid();
        cache()->recycleConnection($cid);
        db()->recycleConnection($cid);
    }
}