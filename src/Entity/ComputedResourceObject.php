<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Entity;

use NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface;
use NoGlitchYo\JsonApiBuilder\Traits\ResourceObjectTrait;

class ComputedResourceObject implements ResourceObjectInterface
{
    use ResourceObjectTrait;

    /**
     * @var ResourceObjectInterface
     */
    public $wrappedEntity;

    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $computedRelationships;

    /**
     * @var array
     */
    private $computedLinks;

    public function __construct(
        ResourceObjectInterface $wrappedEntity,
        array $computedRelationships,
        array $computedLinks
    ) {
        $this->wrappedEntity = $wrappedEntity;
        $this->computedRelationships = $computedRelationships;
        $this->computedLinks = $computedLinks;
    }

    public function getJsonApiId(): string
    {
        return $this->wrappedEntity->getJsonApiId();
    }

    public function getJsonApiType(): string
    {
        return $this->wrappedEntity->getJsonApiType();
    }

    public function getJsonAttributes(): array
    {
        $attributes = array_diff_key(
            $this->wrappedEntity->getJsonAttributes(),
            $this->wrappedEntity->getJsonApiRelationShips()
        );

        return $attributes;
    }

    public function getJsonApiRelationShips(): array
    {
        return $this->computedRelationships;
    }

    public function getJsonApiLinks(): array
    {
        return $this->computedLinks;
    }

    public function getJsonApiMeta(): array
    {
        return $this->wrappedEntity->getJsonApiMeta();
    }
}
