<?php


namespace gs;


class CmdParser
{
    /**
     * @param $data
     * @param string $func
     * @return mixed
     */
    public static function decode($data, $func = 'msgpack_unpack')
    {
        if ($func == 'json_decode') {
            return $func($data, true);
        }
        return $func($data);
    }


    /**
     * @param $data
     * @param string $func
     * @return mixed
     */
    public static function encode($data, $func = 'msgpack_pack')
    {
        return $func($data);
    }
}