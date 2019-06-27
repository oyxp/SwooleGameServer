<?php

namespace traits;

trait Singleton
{
    private static $instance;

    public static function getInstance(...$args)
    {
        if (!(self::$instance instanceof static)) {
            self::$instance = new static(...$args);
        }
        return self::$instance;
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }
}