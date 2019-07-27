<?php
/**
 * Created by PhpStorm.
 * User: snailzed
 * Date: 2019-06-28
 * Time: 23:08
 */

namespace gs;


/**请求上下文
 * Class RequestContext
 * @package gs
 */
class RequestContext
{
    /**
     * @var array
     */
    private $context = [];

    /**
     * @var int 当前连接
     */
    private $fd;

    /**
     * @var string 请求分发的控制器
     */
    private $controller;

    /**
     * @var string 请求的方法
     */
    private $action;

    /**
     * @var int 请求命令,注解定义
     */
    private $cmd;

    /**请求参数
     * @var array
     */
    private $param;

    /**
     * RequestContext constructor.
     * @param int $fd
     * @param $data
     */
    public function __construct(int $fd, $data)
    {
        $this->fd = $fd;
        $this->cmd = $data['c'];
        $this->param = $data['d'] ?? [];
    }

    /**设置上下文
     * @param $name
     * @param $value
     */
    public function setContext($name, $value)
    {
        $this->context[$name] = $value;
    }

    /**获取上下文设置的值
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function getContext($name = null, $default = null)
    {
        if (is_null($name)) {
            return $this->context;
        }
        return isset($this->context[$name]) ? $this->context[$name] : $default;
    }

    /**
     * @return int|null
     */
    public function getFd(): ?int
    {
        return $this->fd;
    }

    /**
     * @param $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**获取当前action
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**获取参数
     * @param null $name
     * @param null $default
     * @return array|mixed|null
     */
    public function getParam($name = null, $default = null)
    {
        if (is_null($name)) {
            return $this->param;
        }
        return isset($this->param[$name]) ? $this->param[$name] : $default;
    }

    /**
     * @return int|mixed
     */
    public function getCmd()
    {
        return $this->cmd;
    }
}