<?php


namespace gs;


class Session
{
    private $session = [];
    private $fd;
    private $data;

    public function __construct(int $fd, $data)
    {
        $this->fd = $fd;
        $this->data = $data;
    }

    public function set($name, $value)
    {
        $this->session[$name] = $value;
    }

    public function get($name, $default = null)
    {
        return isset($this->session[$name]) ? $this->session[$name] : $default;
    }

    public function setFd(int $fd)
    {
        $this->fd = $fd;
    }

    public function getFd(): ?int
    {
        return $this->fd;
    }

}