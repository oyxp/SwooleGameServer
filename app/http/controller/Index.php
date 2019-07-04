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
        return __METHOD__;
    }
}