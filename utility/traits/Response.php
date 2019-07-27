<?php


namespace traits;

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

    public function httpSuccess($data = null, $msg = 'OK', $code = 0)
    {
        return [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];
    }

    public function httpError($code, $msg = '', $data = null)
    {
        return [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];
    }
}