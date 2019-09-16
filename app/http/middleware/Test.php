<?php


namespace app\http\middleware;


use gs\annotation\Middleware;
use gs\Http\Request;
use gs\http\Response;
use interfaces\InterfaceMiddleware;

/**
 * Class Test
 * @package app\http\middleware
 * @Middleware(name="test",weight=2)
 */
class Test implements InterfaceMiddleware
{

    /**
     * 处理用户发送过来的http请求，如果返回值为null则中断请求直接返回结果;可以给请求设置上下文
     * @param Request $request
     * @param Response $response
     * @return bool
     */
    public function handle(Request $request, Response $response): bool
    {
        // TODO: Implement handle() method.
        var_dump(__METHOD__);
        return false;
    }
}