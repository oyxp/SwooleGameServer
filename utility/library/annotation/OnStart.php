<?php


namespace gs\annotation;


use Doctrine\Common\Annotations\Annotation\Target;
use interfaces\CustomEvent;

/**
 * Class OnStart
 * @package gs\annotation
 * @Annotation
 * @Target("CLASS")
 */
class OnStart
{
    public function getEventName()
    {
        return CustomEvent::ON_START;
    }
}