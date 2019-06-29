<?php


namespace gs\annotation;


use interfaces\CustomEvent;

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
        return CustomEvent::ON_WORKER_START;
    }
}