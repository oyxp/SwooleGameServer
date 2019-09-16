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
    /**
     * @var bool
     */
    private $co = true;

    /**
     * Process constructor.
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
        if (isset($values['co'])) {
            $this->co = $values['co'];
        }
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function getCo()
    {
        return $this->co;
    }

}