<?php


namespace gs;


abstract class CacheModel
{
    /**
     * @param $data
     * @return false|string
     */
    public static function pack($data)
    {
        return json_encode($data);
    }

    /**
     * @param $data
     * @return mixed
     */
    public static function unpack($data)
    {
        return json_decode($data, true);
    }

    /**
     * @param array $config
     * @return bool|int|string
     */
    public static function randomIndex(array $config)
    {
        $total = array_sum($config);
        mt_srand();
        $num = mt_rand(1, $total);
        $current = 0;
        foreach ($config as $index => $row_percent) {
            $current += $row_percent;
            if ($num <= $current) {
                return $index;
            }
        }
        return false;
    }

    /**设置key的过期时间
     * @param $key
     * @param $ttl
     * @param $range
     */
    public static function setKeyExpire($key, $ttl)
    {
        return cache()->expire($key, $ttl);
    }

    /**
     * @param integer $ttl
     * @param integer $range
     * @return int
     */
    public static function randomTtl($ttl, $range = null)
    {
        mt_srand();
        if (is_null($range)) {
            $range = intval($ttl / 2);
        }
        return $ttl + mt_rand(-1 * $range, $range);
    }
}