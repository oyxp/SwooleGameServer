<?php


namespace gs;

/**
 * Class AppException
 * @package gs
 */
class AppException extends \Exception
{
    private $data;

    public function __construct($code, $msg_param = [], $data = null, $lang = null)
    {
        $message = Lang::getInstance()->get($code, $msg_param, $lang);
        $this->data = $data;
        parent::__construct($message, $code);
    }

    public function getData()
    {
        return $this->data;
    }
}