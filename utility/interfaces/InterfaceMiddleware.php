<?php


namespace interfaces;


use gs\http\Response;
use gs\Http\Request;

interface InterfaceMiddleware
{
    /**
     * 处理用户发送过来的http请求，当返回结果为false时则中断请求直接返回结果;可以给请求设置上下文;可以在中间件抛出异常
     * @param Request $request
     * @param Response $response
     * @return bool
     */
    public function handle(Request $request, Response $response): bool;
}