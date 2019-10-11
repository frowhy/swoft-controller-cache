<?php declare(strict_types = 1);

namespace Frowhy\SwoftControllerCache\Annotation\Parser;

use ReflectionException;
use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Validator\Exception\ValidatorException;
use Swoft\Validator\ValidatorRegister;

/**
 * Class CacheParser
 *
 * @AnnotationParser(annotation=Cache::class)
 */
class CacheParser extends Parser
{
    /**
     * @param int    $type
     * @param object $annotationObject
     *
     * @throws ReflectionException
     * @throws ValidatorException
     * @return array
     */
    public function parse(int $type, $annotationObject): array
    {
        if ($type != self::TYPE_PROPERTY) {
            return [];
        }
        ValidatorRegister::registerValidatorItem($this->className, $this->propertyName, $annotationObject);
        return [];
    }
}
