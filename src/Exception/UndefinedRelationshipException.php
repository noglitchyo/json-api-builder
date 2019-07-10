<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Exception;

use NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface;
use OutOfBoundsException;

/**
 * @codeCoverageIgnore
 */
class UndefinedRelationshipException extends OutOfBoundsException
{
    public function __construct($relationshipsNames, ResourceObjectInterface $resourceObject)
    {
        parent::__construct(sprintf(
            'No `%s` relationships defined for resource `%s`',
            implode(',', $relationshipsNames),
            $resourceObject->getJsonApiType()
        ));
    }
}
