<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Processor;

use NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface;
use NoGlitchYo\JsonApiBuilder\Exception\UndefinedRelationshipDataException;

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

        foreach ($resourceObject->getJsonApiRelationships() as $relationshipName) {
            if (!array_key_exists($relationshipName, $resourceObject->getJsonAttributes())) {
                throw new UndefinedRelationshipDataException($relationshipName);
            }

            $relationData = $resourceObject->getJsonAttributes()[$relationshipName];

            $relationships[$relationshipName]['data'] = $this->processRelationshipData($relationData);

            if (!empty($relationData)) {
                $relationships[$relationshipName]['links'] = $this->linksProcessor->processRelationship(
                    $relationshipName,
                    $resourceObject
                );
            }
        }

        return $relationships;
    }

    private function processRelationshipData($relationData)
    {
        if ($relationData === null) {
            return null;
        }

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
            'id' => $resourceObject->getJsonApiId(),
        ];
    }
}
