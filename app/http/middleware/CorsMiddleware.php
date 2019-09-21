<?php


namespace app\http\middleware;


use gs\annotation\Middleware;
use gs\http\Response;
use interfaces\InterfaceMiddleware;
use gs\Http\Request;

/**
 * Class CorsMiddleware
 * @package app\http\middleware
 * @Middleware(name="cors",weight=1)
 */
class CorsMiddleware implements InterfaceMiddleware
{

    /**
     * 处理用户发送过来的http请求，如果返回值为null则中断请求直接返回结果
     * @param Request $request
     * @return bool
     */
    public function handle(Request $request, Response $response): bool
    {
        //var_dump(__METHOD__);
        // TODO: Implement handle() method.
        if ('OPTIONS' === $request->getMethod()) {
            $this->configResponse($response);
            return false;
        }
        $this->configResponse($response);
        return true;
    }

    private function configResponse(Response $response)
    {
        $response->withHeader('Access-Control-Allow-Origin', '*');
        $response->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization');
        $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    }
}