<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Exception;

use Exception;
use Throwable;

class UndefinedRelationshipDataException extends Exception
{
    public function __construct(string $relationshipName, Throwable $previous = null)
    {
        $message = sprintf(
            'Relationship `%s` might be not declared in attributes. 
            Key for relationship in attributes must match relationship name.',
            $relationshipName
        );
        parent::__construct($message, 0, $previous);
    }
}
