<?php


namespace interfaces;

/**自定义事件
 * Interface CustomEvent
 * @package interfaces
 */
interface CustomEvent
{
    const ON_BEFORE_START = 'onBeForeStart';
    const ON_FRAMEWORK_INITED = 'onFrameworkInited';//当服务初始化完成，开启server之前
}