<?php


namespace traits;


use gs\AppException;

trait Response
{
    public function success($data = null, $msg = '', $cmd = null, $code = 0)
    {
        return [
            'o' => $code,
            'm' => $msg,
            'd' => $data,
            'c' => $cmd,
        ];
    }

    public function error($code, $cmd, $msg = '', $data = null)
    {
        return [
            'o' => $code,
            'c' => $cmd,
            'm' => $msg,
            'd' => $data,
        ];
    }
}