<?php declare(strict_types = 1);

namespace FZ\ControllerCache\Annotation\Mapping;

/**
 * Class Cache
 *
 * @since   2.0
 *
 * @Annotation
 * @Attributes({
 *     @Attribute("expires",type="int")
 * })
 * @package App\Annotation\Mapping
 */
class Cache
{
    /**
     * @var int
     */
    private $expires = 30;

    /**
     * StringType constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->expires = $values['value'];
        }
    }

    /**
     * @return int
     */
    public function getExpires(): int
    {
        return $this->expires;
    }
}
