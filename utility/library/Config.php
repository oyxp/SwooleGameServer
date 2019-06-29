<?php

namespace gs;

use Dotenv\Dotenv;
use traits\Singleton;

class Config
{
    use Singleton;

    /**配置项
     * @var array
     */
    private $config = [];

    /**解析所有配置项 文件名 => 配置
     * Config constructor.
     */
    public function __construct()
    {
        $env_file = ROOT_PATH . '.env';
        if (file_exists($env_file)) {
            Dotenv::create(ROOT_PATH)->load();
        }
        foreach (glob(CONFIG_PATH . '*.php') as $file) {
            $this->config[pathinfo($file, PATHINFO_FILENAME)] = require $file;
        }
    }

    /**
     * @param $key
     * @param null $default
     * @return array|mixed|null
     */
    public function get($key, $default = null)
    {
        //非多级配置则获取app的配置，默认获取app下的配置
        if (!strpos($key, '.')) {
            return isset($this->config['app'][$key]) ? $this->config['app'][$key] : $default;
        }
        $names = explode('.', $key);
        $config = $this->config;
        //每次取出头部元素
        while ($temp_key = array_shift($names)) {
            if (isset($config[$temp_key])) {
                $config = $config[$temp_key];
            } else {
                //没有找到但是还有下一级
                return $default;
            }
        }
        return $config;
    }

    /**
     * @param $key
     * @return array|mixed
     */
    public function pull($key)
    {
        return isset($this->config[$key]) ? $this->config[$key] : [];
    }
}