<?php


namespace gs\annotation;


use interfaces\SwooleEvent;

/**
 * Class OnWorkerStart
 * @package gs\annotation
 * @Annotation
 * @Target("CLASS")
 */
class OnWorkerStart
{
    public function getEventName()
    {
        return SwooleEvent::ON_WORKER_START;
    }
}