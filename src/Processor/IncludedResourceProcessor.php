<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Processor;

use NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface;
use Exception;

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

    public function process(ResourceObjectInterface $jsonApiEntity, array $include = []): array
    {
        return $this->processIncluded($jsonApiEntity, $include);
    }

    private function processIncluded(ResourceObjectInterface $jsonApiEntity, array $include = []): array
    {
        $included = [];

        $includedRelationships = array_intersect_key($jsonApiEntity->getJsonApiRelationShips(), array_flip($include));

        foreach ($includedRelationships as $attributeKey => $relationship) {
            if (!isset($jsonApiEntity->getJsonAttributes()[$attributeKey])) {
                throw new Exception('Key for relationship not found in attributes. Please define a matching key name for the relationship.');
            }

            $relationResourceObjects = $jsonApiEntity->getJsonAttributes()[$attributeKey];

            if (is_array($relationResourceObjects)) {
                foreach ($relationResourceObjects as &$relationResourceObject) {
                    $relationResourceObject = $this->resourceObjectProcessor->process($relationResourceObject);
                }
                $included = $relationResourceObjects;
            } else {
                $included[] = $this->resourceObjectProcessor->process($relationResourceObjects);
            }
        }

        return $included;
    }
}
