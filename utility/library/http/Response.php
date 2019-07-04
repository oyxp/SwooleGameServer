<?php


namespace gs\http;


class Response extends \gs\http\message\Response
{
    /**
     * @var \Swoole\Http\Response
     */
    private $swooleResponse;

    public function __construct(\Swoole\Http\Response $response)
    {
        $this->swooleResponse = $response;
        parent::__construct();
    }

    /**
     * @return \Swoole\Http\Response
     */
    public function getSwooleResponse()
    {
        return $this->swooleResponse;
    }

    public function write($message = null)
    {
        $message = is_scalar($message) ? $message : json_encode($message);
        $this->swooleResponse->end($message);
    }

    public function writeJson()
    {

    }

}