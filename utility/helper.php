<?php

if (!function_exists('env')) {
    function env($key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            return $default;
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
                return null;
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