<?php


namespace gs\annotation;


use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Process
 * @package gs\annotation
 * @Annotation
 * @Target("CLASS")
 */
class Process
{
    /**
     * @var string
     */
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

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}