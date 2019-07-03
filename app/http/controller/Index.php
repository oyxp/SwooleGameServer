<?php


namespace app\http\controller;

use gs\annotation\Route;

class Index
{
    /**
     * @Route(uri="/",method="GET")
     */
    public function index()
    {
        return __METHOD__;
    }
}