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
use traits\Singleton;

class Dispatcher
{
    use Singleton;
    private $dispatcher;

    public function __construct()
    {
        $this->dispatcher = simpleDispatcher(function (RouteCollector $r) {
            $r->addRoute('GET', '/', function () {
                return [1, 2, 3];
            });
//            $r->addRoute('GET', '/users', 'get_all_users_handler');
            // {id} must be a number (\d+)
//            $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
            // The /{title} suffix is optional
//            $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
        });
    }

    public function dispatch(\Swoole\Http\Request $request)
    {
        $httpMethod = $request->server['request_method'];
        $uri = $request->server['request_uri'];
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        var_dump($uri);
        $uri = rawurldecode($uri);
        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
            case FastDispatcher::NOT_FOUND:
                // ... 404 Not Found
                var_dump('NOT_FOUD');
                break;
            case FastDispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
                var_dump('METHOD_NOT_ALLOWED');

                break;
            case FastDispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                // ... call $handler with $vars
                if (is_callable($handler)) {
                    return $handler($vars);
                } else if (false === strpos($handler, '@')) {
                    list($controller, $action) = explode('@', $handler);
                    return call_user_func_array([new $controller, $action], $vars);
                }
                break;
        }
    }
}