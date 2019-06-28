<?php


namespace gs;


use Throwable;

class AppException extends \Exception
{
    private $data;

    public function __construct($code, $msg_param = [], $data = null)
    {
        $message = '';
        parent::__construct($message, $code);
    }

    public function getData()
    {
        return $this->data;
    }
}