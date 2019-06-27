<?php

namespace gs\annotation;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Controller
 * @package annotation
 * @Annotation
 * @Target("CLASS")
 */
class Controller
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

}