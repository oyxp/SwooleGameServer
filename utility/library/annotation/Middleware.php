<?php


namespace gs\annotation;


use Doctrine\Common\Annotations\Annotation\Target;

/**
 * 中间件注解类
 * Class Middleware
 * @package gs\annotation
 * @Annotation()
 * @Target("CLASS")
 */
class Middleware
{
    /**
     * 中间件名称
     * @var string
     */
    private $name;

    /**
     * 权重，数字越小，表示权重越大，就越先执行
     * @var int
     */
    private $weight;

    /**
     * Middleware constructor.
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
        if (isset($values['weight'])) {
            $this->weight = $values['weight'];
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }
}