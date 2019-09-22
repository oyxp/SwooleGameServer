<?php


namespace gs\http;

use Swoole\Coroutine;

class HttpController
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * HttpController constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
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