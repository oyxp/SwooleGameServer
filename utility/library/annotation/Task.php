<?php


namespace gs\annotation;


use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Task
 * @package gs\annotation
 * @Annotation
 * @Target("CLASS")
 */
class Task
{
    /**
     * @var mixed
     */
    private $name;

    /**
     * Task constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->name = $values['value'];
        }
        if (isset($values['name'])) {
            $this->name = $values['name'];
        }
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}