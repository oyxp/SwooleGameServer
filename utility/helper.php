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
     * @return \Redis|\RedisCluster|\Swoole\Coroutine\Redis
     */
    function cache()
    {
        return \gs\Cache::getInstance();
    }
}

if (!function_exists('db')) {
    /**
     * @return \Medoo\Medoo
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
    function config($key, $default = null)
    {
        return \gs\Config::getInstance()->get($key, $default);
    }
}