<?php


namespace gs;


use traits\Singleton;

class Request
{
    use Singleton;
    /**
     * @var \Swoole\Http\Request
     */
    private $swooleRequest;

    /**
     * Request constructor.
     * @param \Swoole\Http\Request $request
     */
    public function __construct(\Swoole\Http\Request $request)
    {
        $this->swooleRequest = $request;
    }

    /**
     * @param $name
     * @param null $default
     * @return |null
     */
    public function get($name, $default = null)
    {
        return $this->swooleRequest->get[$name] ?? $default;
    }

    /**
     * @param $name
     * @param null $default
     * @return |null
     */
    public function post($name, $default = null)
    {
        return $this->swooleRequest->post[$name] ?? $default;
    }

    public function getFd()
    {
        return $this->swooleRequest->fd;
    }

    public function getSwooleRequest()
    {
        return $this->swooleRequest;
    }
}