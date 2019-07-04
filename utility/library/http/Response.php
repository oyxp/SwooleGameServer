<?php


namespace gs\http;


class Response extends \gs\http\message\Response
{
    /**
     * @var \Swoole\Http\Response
     */
    private $swooleResponse;
    private $isEnd = false;
    private $content = null;

    public function __construct(\Swoole\Http\Response $response)
    {
        $this->swooleResponse = $response;
        $this->swooleResponse->header('Server', 'SwooleGameServer');
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
     * @param string $message
     */
    public function write(string $message)
    {
        if ($this->isEnd) {
            return false;
        }
        $this->isEnd = true;
        $this->initResponse();
        return $this->swooleResponse->end($message);
    }

    /**
     * @param array $message
     * @return mixed
     */
    public function writeJson($message = [])
    {
        if ($this->isEnd) {
            return false;
        }
        $this->isEnd = true;
        $this->initResponse();
        $this->swooleResponse->header('Content-Type', 'application/json');
        return $this->swooleResponse->end(json_encode($message));
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

    /**
     * @return bool
     */
    public function isEnd()
    {
        return $this->isEnd;
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