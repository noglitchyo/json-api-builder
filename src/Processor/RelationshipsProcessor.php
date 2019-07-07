<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Processor;

use NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface;
use Exception;

class RelationshipsProcessor
{
    /**
     * @var LinksProcessor
     */
    private $linksProcessor;

    public function __construct(LinksProcessor $linksProcessor)
    {
        $this->linksProcessor = $linksProcessor;
    }

    public function process(ResourceObjectInterface $resourceObject): array
    {
        $relationships = [];

        foreach ($resourceObject->getJsonApiRelationShips() as $relationshipName => $relationship) {
            if (!isset($resourceObject->getJsonAttributes()[$relationshipName])) {
                throw new Exception(
                    'Key for relationship not found in attributes. Please define a matching key name for the relationship.'
                );
            }

            $relationData = $resourceObject->getJsonAttributes()[$relationshipName];

            $relationships[$relationshipName] = [
                'data'  => $this->processRelationshipData($relationData),
                'links' => $this->linksProcessor->processRelationship($relationshipName, $resourceObject),
            ];
        }

        return $relationships;
    }

    private function processRelationshipData($relationData): array
    {
        if (is_array($relationData)) {
            $relationResourceIdentifiers = [];
            foreach ($relationData as $entity) {
                $relationResourceIdentifiers[] = $this->getResourceIdentifier($entity);
            }
        } else {
            $relationResourceIdentifiers = $this->getResourceIdentifier($relationData);
        }

        return $relationResourceIdentifiers;
    }

    private function getResourceIdentifier(ResourceObjectInterface $resourceObject): array
    {
        return [
            'type' => $resourceObject->getJsonApiType(),
            'id'   => $resourceObject->getJsonApiId(),
        ];
    }
}
