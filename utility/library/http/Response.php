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

    /**
     * @param null $message
     */
    public function write($message = null): void
    {
        $this->initResponse();
        $this->swooleResponse->end($message);
    }

    /**
     * @param $message
     */
    public function writeJson($message = []): void
    {
        $this->initResponse();
        $this->swooleResponse->header('Content-Type', 'application/json');
        $this->swooleResponse->end(json_encode($message));
    }

    /**
     *响应前设置header、cookie
     */
    protected function initResponse()
    {
        //添加header
        $headers = $this->getHeaders();
        foreach ($headers as $key => $header) {
            $this->swooleResponse->header($key, $header, true);
        }
        //设置code
        $this->swooleResponse->status($this->getStatusCode(), $this->getReasonPhrase());
        //设置cookie
        $cookies = $this->getCookies();
        foreach ($cookies as $cookie) {
            $this->swooleResponse->cookie(...$cookie);
        }
    }

    /*
 * 目前swoole不支持同键名的header   因此只能通过别的方式设置多个cookie
 */
    public function setCookie($name, $value = null, $expire = null, $path = '/', $domain = '', $secure = false, $httponly = false)
    {
        $this->withCookie([
            $name, $value, $expire, $path, $domain, $secure, $httponly
        ]);
    }
}