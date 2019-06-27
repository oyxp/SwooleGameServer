<?php


namespace gs;


use Swoole\WebSocket\Frame;

class Session
{
    private $session = [];
    private $fd;
    private $frame;
    private $data;

    public function __construct(Frame $frame)
    {
        $this->fd = $frame->fd;
        $this->frame = $frame;
        $this->data = $frame->data;
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

    public function setFrame($frame)
    {
        $this->frame = $frame;
    }

    public function getFrame(): ?string
    {
        return $this->frame;
    }

}