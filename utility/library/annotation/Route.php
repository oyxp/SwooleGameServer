<?php


namespace gs\annotation;


use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Route
 * @package gs\annotation
 * @Annotation
 * @Target("METHOD")
 */
class Route
{
    private $uri;
    private $method;

    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->uri = $values['value'];
        }
        if (isset($values['uri'])) {
            $this->uri = $values['uri'];
        }
        if (isset($values['method'])) {
            $this->method = $values['method'];
        }
    }

    /**
     * @return string
     */
    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }
}