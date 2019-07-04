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
        //添加header
        $headers = $this->getHeaders();
        foreach ($headers as $key => $header) {
            $this->swooleResponse->header($key, $header, true);
        }
        //设置code
        $this->swooleResponse->status($this->getStatusCode(), $this->getReasonPhrase());
        //设置cookie

        if (is_null($message)) {
            $message = null;
        } else {
            $message = is_scalar($message) ? $message : json_encode($message);
        }
        $this->swooleResponse->end($message);
    }

    public function writeJson()
    {

    }

    public function end()
    {
        $this->swooleResponse->end();
    }
}