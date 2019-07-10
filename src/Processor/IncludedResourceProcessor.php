<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Processor;

use NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface;
use NoGlitchYo\JsonApiBuilder\Exception\UndefinedRelationshipDataException;
use NoGlitchYo\JsonApiBuilder\Exception\UndefinedRelationshipException;

class IncludedResourceProcessor
{
    /**
     * @var ResourceObjectProcessor
     */
    private $resourceObjectProcessor;

    public function __construct(ResourceObjectProcessor $resourceObjectProcessor)
    {
        $this->resourceObjectProcessor = $resourceObjectProcessor;
    }

    public function process(ResourceObjectInterface $resourceObject, array $include = []): array
    {
        $nonExistingRelationships = array_diff($include, $resourceObject->getJsonApiRelationships());
        if (!empty($nonExistingRelationships)) {
            throw new UndefinedRelationshipException($include, $resourceObject);
        }

        $includedRelationships = array_intersect_key($resourceObject->getJsonApiRelationships(), $include);

        $included = [];
        foreach ($includedRelationships as $relationshipName) {
            if (!array_key_exists($relationshipName, $resourceObject->getJsonAttributes())) {
                throw new UndefinedRelationshipDataException($relationshipName);
            }

            $relationshipData = $resourceObject->getJsonAttributes()[$relationshipName];

            if (empty($relationshipData)) {
                continue;
            }

            if (is_array($relationshipData)) {
                foreach ($relationshipData as &$relationResourceObject) {
                    $relationResourceObject = $this->resourceObjectProcessor->process($relationResourceObject);
                }
                $included = $relationshipData;
            } else {
                $included[] = $this->resourceObjectProcessor->process($relationshipData);
            }
        }

        return $included;
    }
}
