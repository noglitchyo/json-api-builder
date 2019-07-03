<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Processor;

use NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface;
use NoGlitchYo\JsonApiBuilder\Factory\LinksFactory;
use NoGlitchYo\JsonApiBuilder\Traits\ResourceObjectTrait;
use Exception;

/**
 * Process a ResourceObjectInterface and computes relationships, links, etc...
 *
 * Class ResourceObjectProcessor
 * @package NoGlitchYo\JsonApiBuilder\Processor
 */
class ResourceObjectProcessor
{
    /**
     * @var LinksFactory
     */
    private $linksFactory;
    /**
     * @var LinksProcessor
     */
    private $linksProcessor;

    public function __construct(LinksFactory $linksFactory, LinksProcessor $linksProcessor)
    {
        $this->linksFactory = $linksFactory;
        $this->linksProcessor = $linksProcessor;
    }

    public function process(ResourceObjectInterface $resourceObject): ResourceObjectInterface
    {
        $data = [
            'relationships' => $this->processRelationships($resourceObject),
            'links' => $this->linksProcessor->process($resourceObject),
        ];

        return new class($resourceObject, $data) implements ResourceObjectInterface
        {
            use ResourceObjectTrait;

            /**
             * @var \NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface
             */
            public static $wrappedEntity;
            /**
             * @var array
             */
            private $data;

            public function __construct(ResourceObjectInterface $wrappedEntity, array $data)
            {
                self::$wrappedEntity = $wrappedEntity;
                $this->data = $data;
            }

            public function getJsonApiId(): string
            {
                return self::$wrappedEntity->getJsonApiId();
            }

            public static function getJsonApiType(): string
            {
                $entity = static::$wrappedEntity;
                return $entity::getJsonApiType();
            }

            public function getJsonAttributes(): array
            {
                $attributes = array_diff_key(
                    self::$wrappedEntity->getJsonAttributes(),
                    self::$wrappedEntity->getJsonApiRelationShips()
                );

                return $attributes;
            }

            public function getJsonApiRelationShips(): array
            {
                return $this->data['relationships'];
            }

            public function getJsonApiLinks(): array
            {
                return $this->data['links'];
            }

            public function getJsonApiMeta(): array
            {
                return [];
            }
        };
    }

    private function processRelationships(ResourceObjectInterface $resourceObject): array
    {
        $relationships = [];

        foreach ($resourceObject->getJsonApiRelationShips() as $relationshipName => $relationship) {
            if (!isset($resourceObject->getJsonAttributes()[$relationshipName])) {
                throw new Exception('Key for relationship not found in attributes. Please define a matching key name for the relationship.');
            }

            $relationData = $resourceObject->getJsonAttributes()[$relationshipName];

            $relationships[$relationshipName] = [
                'data' => $this->processRelationshipData($relationData),
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
            'type' => $resourceObject::getJsonApiType(),
            'id' => $resourceObject->getJsonApiId(),
        ];
    }
}
