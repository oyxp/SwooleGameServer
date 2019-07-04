<?php


namespace gs\http;


use gs\http\message\ServerRequest;
use gs\http\message\Stream;
use gs\http\message\Uri;
use traits\Singleton;

class Request extends ServerRequest
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
        //http https都是一样的协议
        $protocol = str_replace('HTTP/', '', $request->server['server_protocol']);
        //为单元测试准备
        if ($request->fd) {
            $body = new Stream($request->rawContent());
        } else {
            $body = new Stream('');
        }
        parent::__construct($request->server, $request->server['request_uri'], $request->server['request_method'], $this->initUri(), $request->header, $body, $protocol);
        $this->withCookieParams($request->cookie ?? []);
        $this->withQueryParams($request->get ?? []);
        $this->withUploadedFiles($request->files ?? []);
        $this->withParsedBody($request->post ?? []);
    }

    /**
     * @param $name
     * @param null $default
     * @return |null
     */
    public function get($name, $default = null)
    {
        return $this->getQueryParams()[$name] ?? $default;
    }

    /**
     * @param $name
     * @param null $default
     * @return |null
     */
    public function post($name, $default = null)
    {
        return $this->getParsedBody()[$name] ?? $default;
    }

    /**
     * @param $name
     * @param null $default
     * @return |null
     */
    public function server($name, $default = null)
    {
        return $this->getServerParams()[$name] ?? $default;
    }

    /**
     * @return int
     */
    public function getFd()
    {
        return $this->swooleRequest->fd;
    }

    /**
     * @return \Swoole\Http\Request
     */
    public function getSwooleRequest()
    {
        return $this->swooleRequest;
    }


    /**
     * @return Uri
     */
    private function initUri()
    {
        $uri = new Uri();
        $uri = $uri->withScheme(!empty($this->swooleRequest->server['https']) && $this->swooleRequest->server['https'] !== 'off' ? 'https' : 'http');
        $hosts = $this->swooleRequest->header['host'];
        if (false !== strpos($hosts, ':')) {
            list($host, $port) = explode(':', $hosts);
        } else {
            $host = $hosts;
            $port = 80;
        }
        $uri->withHost($host);
        $uri->withPort($port);
        $uri->withQuery($this->swooleRequest->server['query_string'] ?? '');
        $uri->withPath($this->swooleRequest->server['path_info']);
        return $uri;
    }
}