<?php


namespace gs\http;


use traits\Singleton;

class Request extends \gs\http\message\Request
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
        var_dump($request);
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

    public function server($name, $default = null)
    {
        return $this->swooleRequest->server[$name] ?? $default;
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