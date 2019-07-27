<?php

use gs\helper\StringHelper;

if (!function_exists('env')) {
    function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        if (strlen($value) > 1 && StringHelper::startsWith($value, '"') && StringHelper::endsWith($value, '"')) {
            return substr($value, 1, -1);
        }

        if (defined($value)) {
            $value = constant($value);
        }

        return $value;
    }
}

/**
 *cache()
 */
if (!function_exists('cache')) {
    /**
     * @return \Redis|\RedisCluster|\Swoole\Coroutine\Redis|\gs\Cache
     */
    function cache()
    {
        return \gs\Cache::getInstance();
    }
}

if (!function_exists('db')) {
    /**
     * @return \Medoo\Medoo | \gs\Db
     */
    function db()
    {
        return \gs\Db::getInstance();
    }
}


if (!function_exists('value')) {
    /**
     * Return the callback value
     *
     * @param mixed $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof \Closure ? $value() : $value;
    }
}

if (!function_exists('config')) {
    /**
     * @return \gs\Config
     */
    function config()
    {
        return \gs\Config::getInstance();
    }
}

if (!function_exists('getServerAddr')) {
    /**
     * @return string
     */
    function getServerAddr()
    {
        static $server_ip = '';
        if (!empty($server_ip)) {
            return $server_ip;
        }
        $port = config()->get('server.port');
        /** @var  array $local_ips */
        $local_ips = swoole_get_local_ip();
        $server_ip = array_pop($local_ips) . ':' . $port;//本机地址
        return $server_ip;
    }
}