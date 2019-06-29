<?php


namespace gs;


use traits\Singleton;

class Lang
{
    use Singleton;
    /**
     * @var array  语种=>信息
     */
    private $message = [];

    /**获取对应的语种信息
     * @param $code
     * @param array $msg_param
     * @param null $lang
     * @return string
     */
    public function get($code, $msg_param = [], $lang = null)
    {
        //获取默认语种
        if (is_null($lang)) {
            $lang = Config::getInstance()->get('default_lang');
        }
        //如果没有加载对应的语种包
        if (!isset($this->message[$lang])) {
            $this->message[$lang] = $this->load($lang);
        }

        $value = $this->message[$lang][$code] ?? $code;
        return sprintf($value, ...$msg_param);
    }

    /**加载语言包
     * @param $lang
     * @return array|mixed
     */
    public function load($lang)
    {
        $lang_file = APP_PATH . 'lang' . DS . $lang . '.php';
        if (!file_exists($lang_file)) {
            return [];
        }
        $data = include $lang_file;
        return $data;
    }
}