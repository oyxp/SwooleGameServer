<?php
/**
 * Created by PhpStorm.
 * User: snailzed
 * Date: 2019-07-02
 * Time: 22:24
 */

namespace gs;


use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use FastRoute\Dispatcher as FastDispatcher;
use gs\http\Request;
use gs\http\Response;
use traits\Singleton;

/**
 * Class Dispatcher
 * @package gs
 */
class Dispatcher
{
    use Singleton;
    /**
     * @var FastDispatcher
     */
    private $dispatcher;

    /**
     * Dispatcher constructor.
     */
    public function __construct()
    {
        $this->dispatcher = simpleDispatcher(function (RouteCollector $r) {
            $routers = Annotation::getInstance()->getDefinitions('router');
            foreach ($routers as $router) {
                list($method, $uri, $handler) = $router;
                if (is_callable($handler)) {
                    $r->addRoute($method, $uri, $handler);
                } else if (is_string($handler) && false !== strpos($handler, '@')) {
                    $handler = explode('@', $handler);
                    $r->addRoute($method, $uri, $handler);
                }
            }
        });
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed|null
     * @throws AppException
     */
    public function dispatch(Request $request, Response $response)
    {
        $httpMethod = $request->server('request_method');
        $uri = $request->server('request_uri');
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);
        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
            case FastDispatcher::NOT_FOUND:
                // ... 404 Not Found
                $response->withStatus(404);
                throw new AppException(404);
                break;
            case FastDispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
//                $response
                $response->withStatus(405);
                throw new AppException(405);
                break;
            case FastDispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                // ... call $handler with $vars
                if (is_array($handler)) {
                    list($controller, $action) = $handler;
                    $ret = call_user_func_array([new $controller($request, $response), $action], $vars);
                    return $ret;
                } else if (is_callable($handler)) {
                    return $handler($vars);
                } else {
                    $response->withStatus(500);
                    throw new AppException(500);
                }
                break;
        }
    }
}