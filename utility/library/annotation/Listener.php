<?php
/**
 * Created by PhpStorm.
 * User: snailzed
 * Date: 2019-06-29
 * Time: 23:40
 */

namespace gs\annotation;


use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Listener
 * @package gs\annotation
 * @Annotation
 * @Target("CLASS")
 */
class Listener
{
    /**
     * @var string
     */
    private $event;

    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->event = $values['value'];
        }
        if (isset($values['event'])) {
            $this->event = $values['event'];
        }
    }

    public function getEvent()
    {
        return $this->event;
    }
}