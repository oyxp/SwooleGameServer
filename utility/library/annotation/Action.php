<?php

namespace gs\annotation;

/**
 * Class Action
 * @package annotation
 * @Annotation
 * @Target("METHOD")
 */
class Action
{
    private $name;

    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->name = $values['value'];
        }
        if (isset($values['name'])) {
            $this->name = $values['name'];
        }
    }

    public function getName()
    {
        return $this->name;
    }
}