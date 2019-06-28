<?php

namespace gs\annotation;

/**
 * Class Action
 * @package annotation
 * @Annotation
 * @Target("METHOD")
 */
class Command
{
    private $code;

    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->code = $values['value'];
        }
        if (isset($values['code'])) {
            $this->code = $values['code'];
        }
    }

    public function getCode()
    {
        return $this->code;
    }
}