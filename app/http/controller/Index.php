<?php


namespace app\http\controller;

use gs\annotation\Route;
use gs\http\HttpController;

class Index extends HttpController
{
    /**
     * @Route(uri="/",method="GET")
     */
    public function index()
    {
        $this->response->writeJson([
            'code' => 0,
            'msg'  => 'OK',
            'data' => null
        ]);
    }
}