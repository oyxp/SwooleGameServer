<?php


namespace app\model;


use gs\CacheModel;

class AcessTokenModel extends CacheModel
{
    public static function getKey($access_token)
    {
        return 'at:' . $access_token;
    }

    /**
     * @param $access_token
     * @return bool|mixed|string
     */
    public static function getUidByAccessToken($access_token)
    {
        return cache()->get(self::getKey($access_token));
    }
}